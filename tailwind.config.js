import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#296A6B',
                primarylight: '#FCDCB5',
                secondary: '#B1E33D',
                secondarylight: '#F8E2BA',
                redDanger: '#F80505',
                grayD9: '#D9D9D9',
                gray99: '#999999',
                gray9A: '#9A9A9A',
                grayF2: '#F2F2F2',
                gray66: '#666666',
                gray77: '#777777',
                gray37: '#373737',
                black1: '#1A1A1A',
            },
        },
    },

    plugins: [forms, typography],
};
