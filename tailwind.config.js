import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // habilita alternância manual via classe 'dark'
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: { // mantém azul claro para tema light
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                navy: { // paleta corporativa escura para o tema dark
                    50: '#f2f6fb',
                    100: '#e2eaf5',
                    200: '#c6d6ea',
                    300: '#9eb8d8',
                    400: '#6c92bd',
                    500: '#3f6da3',
                    600: '#1f4f82',
                    700: '#143a62',
                    800: '#0f2d4a',
                    900: '#0c2238',
                    950: '#081827',
                }
            }
        },
    },

    plugins: [forms],
};
