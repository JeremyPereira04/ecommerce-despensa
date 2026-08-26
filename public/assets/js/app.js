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

    document.querySelectorAll('[data-horizontal-carousel]').forEach((carousel) => {
        const viewport = carousel.querySelector('[data-carousel-viewport]');
        const previous = carousel.querySelector('[data-carousel-previous]');
        const next = carousel.querySelector('[data-carousel-next]');
        if (!(viewport instanceof HTMLElement)) return;

        const updateControls = () => {
            const maximum = Math.max(0, viewport.scrollWidth - viewport.clientWidth);
            if (previous instanceof HTMLButtonElement) previous.disabled = viewport.scrollLeft <= 8;
            if (next instanceof HTMLButtonElement) next.disabled = viewport.scrollLeft >= maximum - 2;
        };
        const move = (direction) => viewport.scrollBy({ left: direction * Math.max(220, viewport.clientWidth * .82), behavior: 'smooth' });

        previous?.addEventListener('click', () => move(-1));
        next?.addEventListener('click', () => move(1));
        viewport.addEventListener('scroll', updateControls, { passive: true });
        viewport.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
            event.preventDefault();
            move(event.key === 'ArrowRight' ? 1 : -1);
        });
        window.addEventListener('resize', updateControls);
        updateControls();
    });
})();
