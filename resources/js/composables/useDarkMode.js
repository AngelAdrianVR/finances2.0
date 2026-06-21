import { ref, watch, onMounted } from 'vue';

const isDark = ref(false);

export function useDarkMode() {
    onMounted(() => {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            isDark.value = true;
            document.documentElement.classList.add('dark');
        }
    });

    function toggleDark() {
        isDark.value = !isDark.value;
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }

    return { isDark, toggleDark };
}
