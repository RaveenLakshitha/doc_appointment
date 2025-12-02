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
                    // Light mode grays (50-400 range)
                    50: '#f9fafb',   // Very light background
                    100: '#f3f4f6',  // Light background
                    200: '#e5e7eb',  // Borders
                    300: '#d1d5db',  // Dividers
                    400: '#9ca3af',  // Muted text
                    500: '#6b7280',  // Secondary text
                    600: '#4b5563',  // Primary text (light mode)
                    
                    // Dark mode grays (700-900 range)
                    700: '#374151',  // Dark borders/dividers
                    750: '#2d3748',  // Custom hover state (between 700-800)
                    800: '#1f2937',  // Dark background
                    900: '#111827',  // Darker background/headers
                },
            },
        },
    },

    plugins: [forms],
};