<script setup>
import CompanyLogo from '@/Components/CompanyLogo.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const menu = ref(false);

</script>

<template>
    <v-app-bar>
        <CompanyLogo class="h-14 mx-20" />

        <v-row justify="center" width="10rem">
            <v-col>
                <v-btn height="64" class="btn-link">
                    <Link :href="route('web')">Web</Link>
                </v-btn>
            </v-col>
        </v-row>

        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>

        <template v-slot:append>
            <div class="d-flex mr-10" height="64">
                <v-menu v-model="menu" :close-on-content-click="false" location="bottom right"
                    transition="slide-y-transition">
                    <template v-slot:activator="{ props }">
                        <v-btn color="primary" v-bind="props" :append-icon="menu ? 'mdi-chevron-up' : 'mdi-chevron-down'">
                            {{ $page.props.auth.user.name }}
                        </v-btn>
                    </template>

                    <v-card min-width="200" style="margin-top: 10px">
                        <v-list style="padding: 0">
                            <div class="block px-4 py-2 text-xs text-primary">
                                Cuenta de Administrador
                            </div>

                            <DropdownLink :href="route('profile.edit')"> Perfil </DropdownLink>

                            <DropdownLink :href="route('logout')" method="post">
                                Cerrar Sesión
                            </DropdownLink>
                        </v-list>
                    </v-card>
                </v-menu>
            </div>
        </template>
    </v-app-bar>
</template>

<style scoped>
.btn-link {
    text-decoration: none;
    border-radius: 0;
    border-bottom: 2px solid #f57d00;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
    border-color: 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
</style>
@/Stores/titleHeader
