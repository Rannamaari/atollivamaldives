/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './app/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                ocean: {
                    950: '#071b26',
                    900: '#0b2230',
                    800: '#12384b',
                },
                brand: {
                    sand: '#f7f2e8',
                    aqua: '#79dae7',
                },
            },
            fontFamily: {
                display: ['Cormorant Garamond', 'serif'],
                sans: ['Inter', 'sans-serif'],
            },
            boxShadow: {
                'ocean-lg': '0 24px 60px rgba(3, 14, 24, 0.28)',
            },
            maxWidth: {
                '8xl': '90rem',
            },
        },
    },
    plugins: [],
};
