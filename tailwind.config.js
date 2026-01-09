import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',  
        './resources/js/**/*.vue',   
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                gray: {
                    50:   '#fafafa',
                    100:  '#f5f5f5',
                    200:  '#e5e5e5',
                    300:  '#d4d4d4',
                    400:  '#a3a3a3',
                    500:  '#737373',
                    600:  '#525252',
                    700:  '#404040',
                    750:  '#262626',
                    800:  '#171717',
                    900:  '#0f0f0f',

















                },
            },
        },
    },

    plugins: [forms],
};