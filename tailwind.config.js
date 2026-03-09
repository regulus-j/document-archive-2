import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'bg-emerald-100', 'text-emerald-800',
        'bg-rose-100', 'text-rose-800',
        'bg-amber-100', 'text-amber-800',
        'bg-blue-100', 'text-blue-800',
        'bg-gray-100', 'text-gray-800',
        'bg-indigo-100', 'text-indigo-800',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
