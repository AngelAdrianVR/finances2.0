let observer = null;

function getObserver() {
    if (!observer) {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    const delay = Number(entry.target.dataset.revealDelay || 0);

                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);

                    // Restore the element styles so hover transitions keep working.
                    window.setTimeout(() => {
                        entry.target.classList.remove('reveal', 'is-revealed');
                        entry.target.style.removeProperty('--reveal-delay');
                    }, 900 + delay);
                });
            },
            { threshold: 0.15, rootMargin: '0px 0px -60px 0px' },
        );
    }

    return observer;
}

/**
 * `v-reveal` fades an element in the first time it enters the viewport.
 * Optional usage: `v-reveal="{ delay: 120 }"`.
 */
export const vReveal = {
    mounted(el, binding) {
        if (!('IntersectionObserver' in window)) {
            return;
        }

        const delay = Number(binding.value?.delay ?? 0);

        el.classList.add('reveal');

        if (delay) {
            el.dataset.revealDelay = String(delay);
            el.style.setProperty('--reveal-delay', `${delay}ms`);
        }

        getObserver().observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
