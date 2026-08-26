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

    const statsContainer = document.querySelector('[data-dashboard-stats]');
    if (statsContainer instanceof HTMLElement) {
        const errorBox = document.querySelector('[data-stats-error]');
        fetch(statsContainer.dataset.statsUrl || '', {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        })
            .then(async (response) => {
                const payload = await response.json().catch(() => null);
                if (!response.ok || !payload?.success) {
                    throw new Error(payload?.error?.message || 'No se pudieron cargar las estadísticas.');
                }
                return payload.data;
            })
            .then((stats) => {
                const sales = statsContainer.querySelector('[data-stat="ventas_hoy"]');
                const pending = statsContainer.querySelector('[data-stat="pedidos_pendientes"]');
                const lowStock = statsContainer.querySelector('[data-stat="productos_stock_bajo"]');
                const active = statsContainer.querySelector('[data-stat="productos_activos"]');
                if (sales) sales.textContent = `${new Intl.NumberFormat('es-PY').format(stats.ventas_hoy)} Gs.`;
                if (pending) pending.textContent = String(stats.pedidos_pendientes);
                if (lowStock) lowStock.textContent = String(stats.productos_stock_bajo);
                if (active) active.textContent = String(stats.productos_activos);
                const chart = document.querySelector('[data-sales-chart]');
                if (chart) {
                    const max = Math.max(1, ...stats.ventas_7_dias.map((day) => Number(day.total)));
                    chart.innerHTML = stats.ventas_7_dias.map((day) => `<div><span>${new Intl.NumberFormat('es-PY').format(day.total)}</span><i style="height:${Math.max(3, Number(day.total) / max * 100)}%"></i><small>${new Date(`${day.fecha}T12:00:00`).toLocaleDateString('es-PY',{weekday:'short'})}</small></div>`).join('');
                }
                const states = document.querySelector('[data-order-states]');
                if (states) states.innerHTML = stats.estados_pedidos.map((state) => `<div><i class="state-${state.estado.toLowerCase()}"></i><span>${state.estado}</span><strong>${state.cantidad}</strong></div>`).join('') || '<p>Sin pedidos todavía.</p>';
                const orders = document.querySelector('[data-recent-orders]');
                if (orders) orders.innerHTML = stats.pedidos_recientes.map((order) => `<tr><td>#${order.id_pedido}</td><td>${escapeHtml(order.cliente)}</td><td>${escapeHtml(order.fecha_pedido)}</td><td><span class="admin-status">${escapeHtml(order.estado)}</span></td><td>${new Intl.NumberFormat('es-PY').format(order.total)} Gs.</td></tr>`).join('') || '<tr><td colspan="5">Sin pedidos.</td></tr>';
                const stockList = document.querySelector('[data-low-stock]');
                if (stockList) stockList.innerHTML = stats.stock_bajo.map((product) => `<li><div><strong>${escapeHtml(product.nombre)}</strong><small>${escapeHtml(product.categoria)}</small></div><span>${product.stock} unidades</span></li>`).join('') || '<li>Sin productos con stock bajo.</li>';
            })
            .catch((error) => {
                if (errorBox instanceof HTMLElement) {
                    errorBox.textContent = error.message;
                    errorBox.hidden = false;
                }
            });
    }

    const escapeHtml = (value) => {
        const node = document.createElement('div');
        node.textContent = String(value ?? '');
        return node.innerHTML;
    };

    document.querySelectorAll('img[data-image-fallback]').forEach((image) => {
        image.addEventListener('error', () => {
            const fallback = image.dataset.imageFallback;
            if (fallback && image.src !== fallback) image.src = fallback;
        }, { once: true });
    });

    const categoryForm = document.querySelector('[data-category-form]');
    const categoryPreview = document.querySelector('[data-category-image-preview]');
    const categoryImageInput = document.querySelector('[data-category-image-input]');
    const categoryImageError = document.querySelector('[data-category-image-error]');
    const categoryTitle = document.querySelector('[data-category-modal-title]');
    const defaultCategoryImage = categoryPreview?.querySelector('img')?.src || '';
    let categoryPreviewUrl = '';

    const showCategoryPreview = (source) => {
        if (!categoryPreview) return;
        if (categoryPreviewUrl && categoryPreviewUrl !== source) {
            URL.revokeObjectURL(categoryPreviewUrl);
            categoryPreviewUrl = '';
        }
        categoryPreview.innerHTML = '';
        const image = document.createElement('img');
        image.src = source || defaultCategoryImage;
        image.alt = 'Vista previa de la categoría';
        categoryPreview.append(image);
    };

    const resetCategoryImageValidation = () => {
        if (categoryImageInput instanceof HTMLInputElement) {
            categoryImageInput.value = '';
            categoryImageInput.setCustomValidity('');
        }
        if (categoryImageError) categoryImageError.textContent = '';
    };

    document.querySelectorAll('[data-category-edit]').forEach((button) => button.addEventListener('click', () => {
        if (!(categoryForm instanceof HTMLFormElement)) return;
        categoryForm.action = `${categoryForm.dataset.updateAction}&id=${encodeURIComponent(button.dataset.id || '')}`;
        categoryForm.elements.nombre.value = button.dataset.name || '';
        categoryForm.elements.descripcion.value = button.dataset.description || '';
        if (categoryForm.elements.activo instanceof HTMLInputElement) categoryForm.elements.activo.checked = button.dataset.active === '1';
        resetCategoryImageValidation();
        showCategoryPreview(button.dataset.imageUrl || defaultCategoryImage);
        if (categoryTitle) categoryTitle.textContent = 'Editar categoría';
    }));
    document.querySelector('[data-category-create]')?.addEventListener('click', () => {
        if (!(categoryForm instanceof HTMLFormElement)) return;
        categoryForm.action = categoryForm.dataset.createAction || '';
        categoryForm.reset();
        if (categoryForm.elements.activo instanceof HTMLInputElement) categoryForm.elements.activo.checked = true;
        resetCategoryImageValidation();
        showCategoryPreview(defaultCategoryImage);
        if (categoryTitle) categoryTitle.textContent = 'Nueva categoría';
    });

    categoryImageInput?.addEventListener('change', () => {
        if (!(categoryImageInput instanceof HTMLInputElement)) return;
        const file = categoryImageInput.files?.[0];
        categoryImageInput.setCustomValidity('');
        if (categoryImageError) categoryImageError.textContent = '';
        if (!file) return;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = Number.parseInt(categoryImageInput.dataset.maxSize || '2097152', 10);
        let message = '';
        if (!allowedTypes.includes(file.type)) message = 'Seleccioná una imagen JPG, JPEG, PNG o WebP.';
        else if (file.size > maxSize) message = 'La imagen supera el máximo de 2 MB.';
        if (message) {
            categoryImageInput.setCustomValidity(message);
            if (categoryImageError) categoryImageError.textContent = message;
            categoryImageInput.reportValidity();
            return;
        }
        const nextPreviewUrl = URL.createObjectURL(file);
        showCategoryPreview(nextPreviewUrl);
        categoryPreviewUrl = nextPreviewUrl;
    });

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
