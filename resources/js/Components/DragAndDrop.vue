<script setup>
import { useBannerImageStore } from '@stores/BannerImageStore';
import { ref, defineProps } from 'vue';
import Notification from '@/Components/Notification.vue';

const BannerImage = useBannerImageStore();

const props = defineProps({
    text: {
        type: String,
        default: "Arrastra y suelta una imagen aquí",
    },
    color_text: {
        type: String,
        default: "text-gray-600",
    },
    color_body: {
        type: String,
        default: "bg-gray-500",
    },
    color_border: {
        type: String,
        default: "border-gray-500",
    },
});

const drop = async (event) => {
    BannerImage.setWorking(true);
    BannerImage.setLoading(true);

    const files = event.dataTransfer.files;
    const file = files[0];

    //Quiero dejar pasar solo algunas extensiones
    const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;

    if (!allowedExtensions.exec(file.name)) {
        BannerImage.$reset();
        return handleError("Error: Formato de archivo no permitido", "Solo se permiten archivos jpg, jpeg, png y gif");
    }

    const reader = new FileReader();

    const loadImage = () => {
        return new Promise((resolve) => {
            reader.onload = (f) => {
                const src = f.target.result;
                BannerImage.setUrl(src);
                resolve();
            };
        });
    };

    reader.readAsDataURL(file);

    await loadImage();

    BannerImage.setFile(file);

    BannerImage.setLoading(false);
};

const dragLeave = (event) => {
    console.log("Saliendo de la zona");
};

const dragOver = (event) => {
};

const alert = ref({
    status: false,
    message: "",
    color: "#D4E7C5",
    timeout: 1000,
    vertical: true,
    location: "right top",
    rounded: "md"
})

const showAlert = (status, message, color, timeout) => {
    alert.value.status = status
    alert.value.message = message
    alert.value.color = color
    alert.value.timeout = timeout
}

const handleError = (error, errorMessage) => {
    console.error(error);
    showAlert(true, errorMessage, "#F9D7DA", 3000);
};

</script>

<template>
    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0;">
        {{ alert.message }}
    </Notification>

    <div @dragover.prevent="dragOver" @dragleave.prevent="dragLeave" @drop.prevent="drop($event)"
        class="w-full aspect-39/10 bg-clip-padding bg-origin-content bg-no-repeat bg-center bg-cover border-2 border-dashed rounded-lg flex justify-center items-center opacity-1 hover:opacity-70 hover:cursor-pointer transition duration-100 ease-in-out"
        :class="`${color_border} ${color_body}')]`">
        <span class="text-sm sm:text-lg md:text-xl lg:text-xl text-overline" :class="`${color_text}`">{{ text }}</span>
    </div>
</template>
