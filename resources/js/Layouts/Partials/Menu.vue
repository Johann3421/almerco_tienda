<script setup>
import MenuLink from '@/Components/MenuLink.vue';

const links = [
    {
        title: 'Inicio',
        value: 'dashboard',
        icon: 'mdi-home-outline',
        route: 'dashboard',
    },
    {
        title: 'Pedidos',
        value: 'orders',
        icon: 'mdi-cart-outline',
        route: 'orders.index',
    },
    {
        title: 'Productos',
        value: 'products',
        icon: 'mdi-package-variant-closed',
        route: 'products.index',
    },
    // {
    //     title: 'Paquetes',
    //     value: 'Packs',
    //     icon: 'mdi-set-all',
    //     route: 'dashboard',
    // },
    {
        title: 'Categorías',
        value: 'categories',
        icon: 'mdi-tag-multiple-outline',
        route: 'categories.index',
    },
    {
        title: 'Filtros',
        value: 'filters',
        icon: 'mdi-filter-variant',
        route: 'filters.index',
    },
    {
        title: 'Banners',
        value: 'promotions',
        icon: 'mdi-view-array',
        route: 'banners.index',
    },
    
    // {
    //     title: 'Clientes',
    //     value: 'clients',
    //     icon: 'mdi-account-multiple-outline',
    //     route: 'profile.edit',
    // },
    {
        title: 'Configuración',
        value: 'settings',
        icon: 'mdi-cog-outline',
        route: 'settings.index',
    },
    
    // {
    //     title: 'Administración',
    //     value: 'admin',
    //     icon: 'mdi-account-cog-outline',
    //     children: [
    //         {
    //             title: 'Usuarios',
    //             value: 'users',
    //             icon: 'mdi-account-circle',
    //             route: 'profile.edit',
    //         },
    //         {
    //             title: 'Roles',
    //             value: 'roles',
    //             icon: 'mdi-badge-account-outline',
    //             route: 'profile.edit',
    //         },
    //     ],
    // },
]

</script>

<template>
    <v-navigation-drawer expand-on-hover rail class="bg-grey-darken-4" permanent>
        <v-list density="default" nav>
            <MenuLink v-for="({ title, value, icon, route, children }, i) in links" :key="i"
                :href="(route) ? $route(route) : undefined" :active="(route) ? $route().current(route) : undefined"
                :icon="icon" :title="title" :value="value" :children="children">
            </MenuLink>
        </v-list>
    </v-navigation-drawer>
</template>
