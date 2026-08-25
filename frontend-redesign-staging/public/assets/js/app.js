(() => {
    'use strict';

    const clampQuantity = (input, value) => {
        const minimum = Number.parseInt(input.min || '1', 10);
        const maximum = Number.parseInt(input.max || '999', 10);
        const safeValue = Number.isFinite(value) ? value : minimum;
        input.value = String(Math.min(maximum, Math.max(minimum, safeValue)));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    };

    document.querySelectorAll('[data-quantity-control]').forEach((control) => {
        const input = control.querySelector('input[type="number"]');
        if (!input) return;

        control.querySelector('[data-quantity-minus]')?.addEventListener('click', () => {
            clampQuantity(input, Number.parseInt(input.value, 10) - 1);
        });
        control.querySelector('[data-quantity-plus]')?.addEventListener('click', () => {
            clampQuantity(input, Number.parseInt(input.value, 10) + 1);
        });
        input.addEventListener('blur', () => clampQuantity(input, Number.parseInt(input.value, 10)));
    });

    document.querySelectorAll('[data-confirm]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (!window.confirm(button.dataset.confirm || '¿Querés continuar?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('img[data-image-fallback]').forEach((image) => {
        image.addEventListener('error', () => {
            const fallback = image.dataset.imageFallback;
            if (fallback && image.src !== fallback) {
                image.src = fallback;
            }
        }, { once: true });
    });
})();
