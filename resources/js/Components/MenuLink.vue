<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: false,
        default: 'route("dashboard")',
    },
    active: {
        type: Boolean,
        required: false,
        default: false,
    },
    icon: {
        type: String,
        required: true,
        default: 'mdi-lock',
    },
    title: {
        type: String,
        required: true,
        default: 'Titulo',
    },
    value: {
        type: String,
        required: false,
        default: 'Value',
    },
    children: {
        type: Array,
        required: false,
        default: () => [],
    },
    open: {
        type: Array,
        required: false,
        default: []
    },
});

const classes = computed(() =>
    props.active
        ? 'v-list-item v-list-item--active v-list-item--link v-list-item--nav v-theme--light v-list-item--density-compact v-list-item--one-line v-list-item--rounded v-list-item--variant-text'
        : 'v-list-item v-list-item--link v-list-item--nav v-theme--light v-list-item--density-compact v-list-item--one-line v-list-item--rounded v-list-item--variant-text'
);

const activeGroup = ref((props.open.includes(props.value))
    ? true
    : false
)

const classesGroup = ref((props.open.includes(props.value))
    ? 'mdi-chevron-up mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2'
    : 'mdi-chevron-down mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2'
)

const openGroup = () => {
    classesGroup.value = (classesGroup.value === 'mdi-chevron-up mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2')
        ? 'mdi-chevron-down mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2'
        : 'mdi-chevron-up mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2'

    activeGroup.value = !activeGroup.value
}

</script>

<template>
    <template v-if="!children.length">
        <Link :class="classes" :href="href" tabindex="-2">

        <span class="v-list-item__overlay"></span>
        <span class="v-list-item__underlay"></span>

        <div class="v-list-item__prepend">
            <i :class="`${icon} mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2`" aria-hidden="true"
                density="compact"></i>
            <div class="v-list-item__spacer"></div>
        </div>

        <div class="v-list-item__content" data-no-activator="">
            <div class="v-list-item-title">{{ title }}</div>
        </div>
        </Link>
    </template>
    <template v-else>

        <div class="v-list-group v-list-group--prepend v-list-group--open">

            <div class="v-list-item v-list-item--active v-list-item--link v-list-item--nav v-theme--light v-list-item--density-compact v-list-item--one-line v-list-item--rounded v-list-item--variant-text v-list-group__header"
                tabindex="-2" :id="`v-list-group--id-${value}`" @click="openGroup">

                <span class="v-list-item__overlay"></span>
                <span class="v-list-item__underlay"></span>

                <div class="v-list-item__prepend">
                    <i :class="`${icon} mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2`"
                        aria-hidden="true" density="compact">
                    </i>

                    <div class="v-list-item__spacer"></div>
                </div>

                <div class="v-list-item__content" data-no-activator="">
                    <div class="v-list-item-title">{{ title }}</div>
                </div>

                <div class="v-list-item__append">
                    <i :class="classesGroup" aria-hidden="true" density="compact"></i>

                    <div class="v-list-item__spacer"></div>
                </div>
            </div>

            <div class="v-list-group__items" role="group" :aria-labelledby="`v-list-group--id-${value}`"
                :style="activeGroup ? 'display: block;' : 'display: none;'">

                <Link
                    class="v-list-item v-list-item--link v-list-item--nav v-theme--light v-list-item--density-compact v-list-item--one-line v-list-item--rounded v-list-item--variant-text"
                    tabindex="-2" v-for="({ title, value, icon, route }, i) in children" :key="i" :href="$route(`${route}`)"
                    :class="$route().current(`${route}`) ? 'v-list-item--active' : ''">

                <span class="v-list-item__overlay"></span>
                <span class="v-list-item__underlay"></span>

                <div class="v-list-item__prepend">
                    <i :class="`${icon} mdi v-icon notranslate v-theme--light v-icon--size-default text-orange-darken-2`"
                        aria-hidden="true" density="compact">
                    </i>

                    <div class="v-list-item__spacer"></div>
                </div>

                <div class="v-list-item__content" data-no-activator="">
                    <div class="v-list-item-title">{{ title }}</div>
                </div>
                </Link>
            </div>
        </div>
    </template>
</template>
