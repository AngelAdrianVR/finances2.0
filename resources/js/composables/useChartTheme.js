import { ref, onMounted, onUnmounted } from 'vue';

/**
 * Reactive dark-mode flag that updates whenever the `dark` class
 * on <html> changes, so charts can refresh their palette.
 */
export function useChartTheme() {
    const isDark = ref(document.documentElement.classList.contains('dark'));

    let observer;

    onMounted(() => {
        observer = new MutationObserver(() => {
            isDark.value = document.documentElement.classList.contains('dark');
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });
    });

    onUnmounted(() => observer?.disconnect());

    return { isDark };
}
