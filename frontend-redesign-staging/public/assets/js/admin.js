(() => {
    'use strict';

    const body = document.body;
    const toast = document.querySelector('[data-demo-toast]');
    let toastTimer;

    const showDemo = (message) => {
        if (!toast) return;
        toast.textContent = message || 'Prototipo visual: esta acción se habilitará al integrar el backend.';
        toast.hidden = false;
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => { toast.hidden = true; }, 4200);
    };

    document.querySelector('[data-admin-menu]')?.addEventListener('click', (event) => {
        const open = body.classList.toggle('admin-menu-open');
        event.currentTarget.setAttribute('aria-expanded', String(open));
    });
    document.querySelector('[data-admin-overlay]')?.addEventListener('click', () => body.classList.remove('admin-menu-open'));
    document.querySelectorAll('[data-demo-form]').forEach((form) => form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (form.reportValidity()) showDemo(form.dataset.demoMessage);
    }));
    document.querySelectorAll('[data-demo-action]').forEach((button) => button.addEventListener('click', () => showDemo()));

    const passwordToggle = document.querySelector('[data-password-toggle]');
    const passwordInput = document.querySelector('#admin-password');
    passwordToggle?.addEventListener('click', () => {
        if (!(passwordInput instanceof HTMLInputElement)) return;
        const revealing = passwordInput.type === 'password';
        passwordInput.type = revealing ? 'text' : 'password';
        passwordToggle.setAttribute('aria-pressed', String(revealing));
        passwordToggle.setAttribute('aria-label', revealing ? 'Ocultar contraseña' : 'Mostrar contraseña');
        passwordInput.focus();
    });

    const loginForm = document.querySelector('[data-admin-login-form]');
    const loginStatus = document.querySelector('[data-login-status]');
    const loginFields = {
        email: document.querySelector('#admin-email'),
        password: document.querySelector('#admin-password'),
    };

    const setLoginFieldError = (name, message) => {
        const input = loginFields[name];
        const error = document.querySelector(`[data-field-error="${name}"]`);
        if (input instanceof HTMLInputElement) {
            if (message) input.setAttribute('aria-invalid', 'true');
            else input.removeAttribute('aria-invalid');
        }
        if (error) error.textContent = message;
    };

    Object.entries(loginFields).forEach(([name, input]) => {
        input?.addEventListener('input', () => setLoginFieldError(name, ''));
    });

    loginForm?.addEventListener('submit', (event) => {
        const form = event.currentTarget;
        if (!(form instanceof HTMLFormElement)) return;

        const email = loginFields.email;
        const password = loginFields.password;
        if (email instanceof HTMLInputElement) email.value = email.value.trim();

        setLoginFieldError('email', '');
        setLoginFieldError('password', '');

        if (email instanceof HTMLInputElement && email.validity.valueMissing) {
            setLoginFieldError('email', 'Ingresá tu correo electrónico.');
        } else if (email instanceof HTMLInputElement && email.validity.typeMismatch) {
            setLoginFieldError('email', 'Ingresá un correo electrónico válido.');
        }
        if (password instanceof HTMLInputElement && password.validity.valueMissing) {
            setLoginFieldError('password', 'Ingresá tu contraseña.');
        }

        if (!form.checkValidity()) {
            event.preventDefault();
            const firstInvalid = form.querySelector('[aria-invalid="true"]');
            if (firstInvalid instanceof HTMLElement) firstInvalid.focus();
            if (loginStatus) loginStatus.textContent = 'Revisá los campos indicados antes de continuar.';
            return;
        }

        const button = form.querySelector('[data-login-submit]');
        if (!(button instanceof HTMLButtonElement) || button.disabled) {
            event.preventDefault();
            return;
        }
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        const label = button.querySelector('[data-login-label]');
        const spinner = button.querySelector('[data-login-spinner]');
        if (label) label.textContent = 'Verificando…';
        if (spinner) spinner.hidden = false;
        if (loginStatus) loginStatus.textContent = 'Verificando credenciales.';
    });

    const imageInput = document.querySelector('[data-image-input]');
    const preview = document.querySelector('[data-image-preview]');
    imageInput?.addEventListener('change', () => {
        const file = imageInput.files?.[0];
        if (!file || !preview) return;
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            preview.innerHTML = '';
            const image = document.createElement('img');
            image.src = String(reader.result);
            image.alt = 'Vista previa de la imagen seleccionada';
            preview.append(image);
        });
        reader.readAsDataURL(file);
    });

    const search = document.querySelector('[data-admin-product-search]');
    const category = document.querySelector('[data-admin-category-filter]');
    const filterRows = () => document.querySelectorAll('[data-product-row]').forEach((row) => {
        const term = (search?.value || '').trim().toLowerCase();
        const selected = category?.value || '';
        row.hidden = !(row.dataset.name.includes(term) && (!selected || row.dataset.category === selected));
    });
    search?.addEventListener('input', filterRows);
    category?.addEventListener('change', filterRows);
})();
