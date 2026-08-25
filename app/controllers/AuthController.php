<?php

declare(strict_types=1);

class AuthController
{
    private const MAX_ATTEMPTS = 5;
    private const ATTEMPT_WINDOW = 900;

    public function __construct(private readonly User $userModel, private readonly RateLimiter $rateLimiter)
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

    public function showLogin():void{if(is_customer_authenticated()){header('Location: '.url('home'),true,303);exit;}render('auth/login.php',['pageTitle'=>'Iniciar sesión']);}
    public function showRegister():void{if(is_customer_authenticated()){header('Location: '.url('home'),true,303);exit;}render('auth/register.php',['pageTitle'=>'Crear cuenta']);}
    public function loginCustomer():never
    {
        if(!verify_csrf($_POST['csrf_token']??null)){flash('danger','El formulario venció.');$this->redirect('login');}$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');$key=$email.'|'.($_SERVER['REMOTE_ADDR']??'unknown');if(!$this->rateLimiter->consume('customer-login',$key,5,900)){flash('danger','Demasiados intentos. Esperá 15 minutos.');$this->redirect('login');}
        try{$u=$this->userModel->findByEmail($email);$active=is_array($u)&&in_array($u['activo'],[true,1,'1','t'],true);if(!$active||($u['rol']??null)!=='CLIENTE'||!password_verify($password,(string)($u['contrasena_hash']??''))){throw new RuntimeException();}sign_in_customer($u);flash('success','Bienvenido/a, '.$u['nombre'].'.');$this->redirect('checkout');}catch(Throwable){flash('danger','Correo o contraseña incorrectos.');$this->redirect('login');}
    }
    public function registerCustomer():never
    {
        if(!verify_csrf($_POST['csrf_token']??null)){flash('danger','El formulario venció.');$this->redirect('register');}$n=trim((string)($_POST['first_name']??''));$a=trim((string)($_POST['last_name']??''));$e=strtolower(trim((string)($_POST['email']??'')));$p=(string)($_POST['password']??'');if($n===''||$a===''||mb_strlen($n)>100||mb_strlen($a)>100||!filter_var($e,FILTER_VALIDATE_EMAIL)||strlen($p)<12){flash('danger','Completá los datos y usá una contraseña de al menos 12 caracteres.');$this->redirect('register');}
        try{$hash=password_hash($p,defined('PASSWORD_ARGON2ID')?PASSWORD_ARGON2ID:PASSWORD_DEFAULT);$u=$this->userModel->create($n,$a,$e,$hash);sign_in_customer($u);flash('success','Tu cuenta fue creada.');$this->redirect('checkout');}catch(PDOException $x){flash('danger',$x->getCode()==='23505'?'Ese correo ya está registrado.':'No pudimos crear la cuenta.');$this->redirect('register');}
    }
    public function logoutCustomer():never{if(verify_csrf($_POST['csrf_token']??null))sign_out_customer();$this->redirect('home');}
    private function redirect(string $page):never{header('Location: '.url($page),true,303);exit;}

    public function loginAdmin(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $_SESSION['admin_login_email'] = $email;

        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            $this->fail('La solicitud expiró. Recargá la página e intentá nuevamente.');
        }

        $clientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $rateKey = strtolower($email) . '|' . $clientIp;
        if (!$this->rateLimiter->consume('admin-login', $rateKey, self::MAX_ATTEMPTS, self::ATTEMPT_WINDOW)) {
            $this->fail('Se alcanzó el límite temporal de intentos. Esperá unos minutos antes de volver a intentar.', false);
        }

        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->fail('Revisá los campos obligatorios e ingresá un correo válido.', false);
        }

        try {
            $user = $this->userModel->findByEmail($email);
        } catch (Throwable) {
            $this->fail('No pudimos procesar el acceso en este momento. Intentá nuevamente más tarde.', false);
        }

        if (!valid_admin_credentials($user, $password)) {
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
