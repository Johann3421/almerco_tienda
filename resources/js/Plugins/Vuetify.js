import "vuetify/styles";
import "@mdi/font/css/materialdesignicons.css";

import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import * as labsComponents from "vuetify/labs/components";

import { aliases, mdi } from "vuetify/iconsets/mdi";
import colors from "vuetify/util/colors";
const customeTheme = {
    dark: false,
    colors: {
        primary: "#2196F3", // Ya es azul, no necesita cambios
        // primary: colors.orange.darken2, // Esta línea está comentada, puedes eliminarla
        secondary: "#424242",
        accent: "#82B1FF",
        error: "#FF5252",
        info: "#2196F3",
        success: "#4CAF50",
        warning: "#FFC107", // Este es un amarillo/naranja, lo cambiamos a un azul
        lightblue: "#14c6FF",
        yellow: "#FFCF00",
        pink: "#FF1976",
        orange: "#2196F3", // Cambiado de #FF8657 (naranja) a #2196F3 (azul)
        magenta: "#C33AFC",
        darkblue: "#1E2D56",
        gray: "#909090",
        neutralgray: "#9BA6C1",
        green: "#2ED47A",
        red: "#FF5c4E",
        darkblueshade: "#308DC2",
        lightgray: "#BDBDBD",
        lightpink: "#FFCFE3",
        white: "#FFFFFF",
        muted: "#6c757d",
    },
};

const vuetify = createVuetify({
    components: {
        ...components,
        ...labsComponents,
    },
    directives,
    icons: {
        iconfont: "mdi",
        values: {
            ...mdi,
            ...aliases,
        },
    },
    theme: {
        defaultTheme: "customeTheme",
        themes: {
            customeTheme,
        },
    },
});

export default vuetify;
