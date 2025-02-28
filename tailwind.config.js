import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // primary: "#f57d00",
                // secondary: "#424242",
                // accent: "#82B1FF",
                // error: "#FF5252",
                // info: "#2196F3",
                // success: "#4CAF50",
                // warning: "#FFC107",
                // lightblue: "#14c6FF",
                // yellow: "#FFCF00",
                // pink: "#FF1976",
                // orange: "#FF8657",
                // magenta: "#C33AFC",
                // darkblue: "#1E2D56",
                // gray: "#909090",
                // neutralgray: "#9BA6C1",
                // green: "#2ED47A",
                // red: "#FF5c4E",
                // darkblueshade: "#308DC2",
                // lightgray: "#BDBDBD",
                // lightpink: "#FFCFE3",
                // white: "#FFFFFF",
                // muted: "#6c757d",
                black: '#000000', // negro
                white: '#ffffff', // blanco
                naranja: '#f56f00', //Naranja
                ntonalidadoscuro: '#f54300', // Naranja tonalidad oscuro
                cremaoscuro: '#F59600', // Crema oscuro
                naranjaclaro: '#F57D00', // Naranja claro
                cremaclaro: '#F5B300', // Crema claro
                rojo: '#F51800', // Rojo
                plomooscuro: '#5A5A5A', // Plomo oscuro
                plomomediooscuro: '#7C7C7C',
                plomobajooscuro: '#9E9E9E',
                plomomedioclaro: '#C0C0C0',
                plomobajoclaro: '#E2E2E2',
                plomoclaro: '#EBEBEB',
                encabezado: '#1a1a1a',
                navegation:'#5a5a5a',
                fondoback: '#f8f8f8',
                greenhad: '#5db500'
            },
        },
        aspectRatio: {
            '1/1': '1 / 1',
            '4/3': '4 / 3',
            '9/16': '9 / 16',
            '3/4': '3 / 4',
            '5/2': '5 / 2',
            '32/15': '32 / 15',
            '39/10': '39 / 10',
            '237/6': '237 / 6',
            '37/6': '37 / 6',
            '215/24': '215 / 24',
        },
    },

    // plugins: [forms],
};
