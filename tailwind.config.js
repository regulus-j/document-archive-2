import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    /*
     * Safelist: The dashboard renders stat-card colors dynamically via Blade
     * loops (e.g.  bg-{{ $stat['color'] }}-50 ).  Tailwind's JIT compiler
     * cannot detect those at build time, so we safelist every combination
     * that the dashboard uses.
     */
    safelist: [
        { pattern: /bg-(sky|blue|indigo|violet|purple|emerald|amber)-(50|100|200)/ },
        { pattern: /text-(sky|blue|indigo|violet|purple|emerald|amber)-(600|700|800|900)/ },
        { pattern: /border-(sky|blue|indigo|violet|purple|emerald|amber)-200/ },
        // Force responsive variants for grid layout
        { pattern: /(lg:)?(grid-cols-|col-span-)/ },
        { pattern: /(lg:)?grid/ },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            /*
             * Design-system shadows – unified across cards, navigation,
             * dropdowns and elevated surfaces.
             */
            boxShadow: {
                'card':       '0 1px 3px 0 rgb(0 0 0 / .04), 0 1px 2px -1px rgb(0 0 0 / .04)',
                'card-hover': '0 4px 6px -1px rgb(0 0 0 / .07), 0 2px 4px -2px rgb(0 0 0 / .05)',
                'nav':        '0 1px 3px 0 rgb(0 0 0 / .05)',
                'dropdown':   '0 4px 16px -2px rgb(0 0 0 / .10), 0 2px 4px -2px rgb(0 0 0 / .06)',
            },
        },
    },

    plugins: [forms],
};
