import { ref } from 'vue';

const rules = ref({
    required: (value) => !!value || "Campo requerido",
    string: (value) => typeof value === "string" && !/\d/.test(value) || "Debe ser una cadena de texto sin números",
    boolean: (value) => /^(true|false)$/.test(value) || "Debe ser un valor booleano",
    number: (value) => /^\d+$/.test(value) || "Debe ser un valor numérico",
    decimal: (value) => /^\d+(\.\d{1,2})?$/.test(value) || "Debe ser un valor decimal",
    email: (value) => /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value) || "Debe ser un correo electrónico válido",
    array: (value) => Array.isArray(value) || "Debe ser un arreglo",
    arrayNotEmpty: (value) => Array.isArray(value) && value.length > 0 || "Debe contener al menos un elemento",
    slug: (value) => /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value) || "Debe ser un slug válido",
    image_icon: (value) => /\.(jpg|jpeg|png|gif|svg)$/.test(value) || "Debe ser una imagen válida",
    image_icon_size: (value) => value.size <= 5242880 || "La imagen no debe pesar más de 10MB",
    dni: (value) => /^\d{8}$/.test(value) || "Debe ser un DNI válido",
    ruc: (value) => /^\d{11}$/.test(value) || "Debe ser un RUC válido",
    phone: (value) => /^\d{9}$/.test(value) || "Debe ser un número de celular válido",
    address: (value) => /^[a-zA-Z0-9\s#\u00C0-\u017F.-]+(?:,[a-zA-Z0-9\s#\u00C0-\u017F.-]+)*$/.test(value) || "Debe ser una dirección válida",
    max: (max) => (v) => !v || v.length <= max || `Máximo ${max} caracteres`,
    min: (min) => (v) => !v || v.length >= min || `Mínimo ${min} caracteres`,
    url: (value) => /^(http|https):\/\/.*/.test(value) || "Debe ser una URL válida",
});

export default rules;
