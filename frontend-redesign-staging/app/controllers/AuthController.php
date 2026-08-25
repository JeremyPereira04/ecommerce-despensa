<?php

declare(strict_types=1);

class AuthController
{
    private const MAX_ATTEMPTS = 5;
    private const ATTEMPT_WINDOW = 900;

    public function __construct(private readonly User $userModel)
    {
    }

    public function showAdminLogin(): void
    {
        if (is_admin_authenticated()) {
            header('Location: ' . url('admin-dashboard'), true, 303);
            exit;
        }

        $notice = $_SESSION['admin_login_notice'] ?? null;
        $oldEmail = $_SESSION['admin_login_email'] ?? '';
        unset($_SESSION['admin_login_notice'], $_SESSION['admin_login_email']);
        render_admin('admin/login.php', [
            'pageTitle' => 'Acceso administrativo',
            'adminLogin' => true,
            'loginNotice' => is_string($notice) ? $notice : null,
            'oldEmail' => is_string($oldEmail) ? $oldEmail : '',
        ]);
    }

    public function loginAdmin(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $_SESSION['admin_login_email'] = $email;

        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            $this->fail('La solicitud expiró. Recargá la página e intentá nuevamente.');
        }

        if ($this->isRateLimited()) {
            $this->fail('Se alcanzó el límite temporal de intentos. Esperá unos minutos antes de volver a intentar.', false);
        }

        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->registerFailure();
            $this->fail('Revisá los campos obligatorios e ingresá un correo válido.', false);
        }

        try {
            $user = $this->userModel->findByEmail($email);
        } catch (Throwable) {
            $this->fail('No pudimos procesar el acceso en este momento. Intentá nuevamente más tarde.', false);
        }

        if (!valid_admin_credentials($user, $password)) {
            $this->registerFailure();
            $this->fail('El correo o la contraseña son incorrectos.', false);
        }

        sign_in_admin($user);
        header('Location: ' . url('admin-dashboard'), true, 303);
        exit;
    }

    public function logoutAdmin(): void
    {
        require_admin();
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            render_admin('admin/login.php', [
                'pageTitle' => 'Acceso administrativo',
                'adminLogin' => true,
                'loginNotice' => 'La solicitud de cierre de sesión expiró.',
                'oldEmail' => '',
            ]);
            return;
        }

        sign_out_admin();
        header('Location: ' . url('admin-login'), true, 303);
        exit;
    }

    private function isRateLimited(): bool
    {
        $cutoff = time() - self::ATTEMPT_WINDOW;
        $attempts = array_values(array_filter(
            $_SESSION['admin_login_attempts'] ?? [],
            static fn (mixed $timestamp): bool => is_int($timestamp) && $timestamp >= $cutoff
        ));
        $_SESSION['admin_login_attempts'] = $attempts;

        return count($attempts) >= self::MAX_ATTEMPTS;
    }

    private function registerFailure(): void
    {
        $_SESSION['admin_login_attempts'][] = time();
    }

    private function fail(string $message, bool $clearEmail = false): never
    {
        $_SESSION['admin_login_notice'] = $message;
        if ($clearEmail) {
            unset($_SESSION['admin_login_email']);
        }
        header('Location: ' . url('admin-login'), true, 303);
        exit;
    }
}
