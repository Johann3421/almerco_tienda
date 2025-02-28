export const handleFileUpload = (event, imgData, errorHandler, maxSize = 1024 * 1024 * 10) => {
    const file = event.target.files[0];

    if (file.size > maxSize) {
        errorHandler(`La imagen es demasiado grande, el tamaño máximo es de ${maxSize / ( 1024 * 1024)} MB`);
        return;
    }

    const validTypes = ["image/jpeg", "image/png", "image/jpg", "image/webp", "image/svg+xml"];
    if (!validTypes.includes(file.type)) {
        errorHandler("La imagen debe ser de tipo jpg, jpeg, png, webp o svg");
        return;
    }

    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        imgData.imagen_url = reader.result;
    };
    imgData.imagen = file;
};

export const handleSubmit = async (url, data) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => formData.append(key, data[key]));

    return await axios.post(url, formData, {
        headers: { "Content-Type": "multipart/form-data" },
    });
};
