<template>
    <div class="mx-auto text-center">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="text-center sm:text-left">
                <h1
                    class="text-xl font-bold text-gray-900 md:text-start md:text-3xl sm:text-2xl">
                    {{ title }}
                </h1>

                <nav aria-label="Breadcrumb" class="mt-4">
                    <ol class="flex items-center gap-1 text-sm text-gray-600">
                        <li>
                            <Link :href="route('dashboard')"
                                class="block transition hover:text-gray-700">
                            <span class="sr-only"> Home </span>

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            </Link>
                        </li>

                        <li class="rtl:rotate-180">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </li>

                        <li>
                            <Link :href="breadcrumb_link"
                                class="block transition hover:text-gray-700">
                                {{ breadcrumb }}
                            </Link>
                        </li>

                        <li class="rtl:rotate-180">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </li>

                        <li>
                            <Link :href="breadcrumb_link_2"
                                class="block transition hover:text-gray-700">
                                {{ breadcrumb_2 }}
                            </Link>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    title: {
        type: String,
        required: true,
    },
    breadcrumb: {
        type: String,
        required: true,
    },
    breadcrumb_link: {
        type: String,
        required: true,
    },
    breadcrumb_2: {
        type: String,
        required: true,
    },
    breadcrumb_link_2: {
        type: String,
        required: true,
    }
});
</script>
