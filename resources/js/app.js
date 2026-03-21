import { Livewire, Alpine } from '../../public/vendor/livewire/livewire.esm.js';

/*
 * Theme: "light", "dark" or "system", kept in localStorage. The inline script
 * in the layout head applies it before paint so there is no flash.
 */
Alpine.store('theme', {
    mode: localStorage.getItem('theme') ?? 'system',

    set(mode) {
        this.mode = mode;
        localStorage.setItem('theme', mode);
        applyTheme(mode);
    },

    cycle() {
        const order = ['light', 'dark', 'system'];
        this.set(order[(order.indexOf(this.mode) + 1) % order.length]);
    },
});

function applyTheme(mode) {
    const dark = mode === 'dark' || (mode === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark } }));
}

matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (Alpine.store('theme').mode === 'system') applyTheme('system');
});

/*
 * Toasts: flash messages from the server and events dispatched by Livewire
 * components ($this->dispatch('toast', type: 'success', message: '...')).
 */
Alpine.data('toasts', (initial = []) => ({
    items: [],

    init() {
        initial.forEach((toast) => this.push(toast));
        window.addEventListener('toast', (event) => this.push(event.detail));
    },

    push(toast) {
        const id = Date.now() + Math.random();
        this.items.push({ id, type: 'success', ...toast });
        setTimeout(() => this.dismiss(id), toast.type === 'error' ? 7000 : 4500);
    },

    dismiss(id) {
        this.items = this.items.filter((item) => item.id !== id);
    },
}));

/*
 * Product gallery: thumbnails swap the main picture; arrow keys step through.
 */
Alpine.data('gallery', (images = []) => ({
    images,
    active: 0,

    show(index) {
        this.active = (index + this.images.length) % this.images.length;
    },
}));

/*
 * Quantity stepper used on the product page form.
 */
Alpine.data('stepper', (value = 1, max = 50) => ({
    value,
    max,

    inc() {
        this.value = Math.min(this.max, this.value + 1);
    },

    dec() {
        this.value = Math.max(1, this.value - 1);
    },
}));

/*
 * Repeating rows (fitments on the admin product form).
 */
Alpine.data('rows', (initial = [], blank = {}) => ({
    rows: initial.length ? initial : [{ ...blank }],

    add() {
        this.rows.push({ ...blank });
    },

    remove(index) {
        this.rows.splice(index, 1);
        if (!this.rows.length) this.add();
    },
}));

/*
 * Ask before a destructive form submits.
 */
Alpine.directive('confirm', (el, { expression }) => {
    el.addEventListener('submit', (event) => {
        if (!window.confirm(expression)) event.preventDefault();
    });
});

window.Alpine = Alpine;

Livewire.start();
