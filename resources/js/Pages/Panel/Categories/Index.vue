<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SimpleHeader from "@/Components/SimpleHeader.vue";
import Notification from "@/Components/Notification.vue";
import { Link, Head } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import axios from "axios";
import useFormRules from "@utils/UseFormRules";
import Favicon from "@assets/img/favicon.png";

const page = ref(1);

const page_groups = ref(1);

const page_subgroups = ref(1);

const categories = ref([]);

const search = ref("");

const search_group = ref("");

const search_subgroup = ref("");

const items_per_page = ref(10);

const items_per_page_groups = ref(10);

const items_per_page_subgroups = ref(10);

const fetch_loader = ref(false);

const fetch_loader_form = ref(false);

const fetchCategories = async (criteria) => {
  fetch_loader.value = true;

  const url_base = window.location.href;

  const url = `${url_base}/buscar/${criteria}`;

  try {
    const response = await axios.get(url);

    categories.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    fetch_loader.value = false;
  }
};

const headers = ref([]);

const headers_groups = ref([]);

const headers_subgroups = ref([]);

onMounted(async () => {
  headers.value = [
    { key: "index", title: "# Categoria", align: "start", sortable: false },
    { key: "name", title: "Nombre", align: "start", sortable: true },
    { key: "slug", title: "Slug", align: "start", sortable: false },
    { key: "icon", title: "Icono", align: "center", sortable: false },
    {
      key: "featured",
      title: "Destacado",
      align: "center",
      sortable: false,
    },
    { key: "active", title: "Activo", align: "center", sortable: false },
    { key: "actions", title: "Acciones", align: "center", sortable: false },
  ];

  headers_groups.value = [
    { key: "index", title: "# Grupo", align: "start", sortable: false },
    { key: "name", title: "Nombre", align: "start", sortable: true },
    { key: "slug", title: "Slug", align: "start", sortable: false },
    {
      key: "featured",
      title: "Destacado",
      align: "center",
      sortable: false,
    },
    { key: "active", title: "Activo", align: "center", sortable: false },
    { key: "actions", title: "Acciones", align: "center", sortable: false },
  ];

  headers_subgroups.value = [
    { key: "index", title: "# Sub Grupo", align: "start", sortable: false },
    { key: "name", title: "Nombre", align: "start", sortable: true },
    { key: "slug", title: "Slug", align: "start", sortable: false },
    {
      key: "featured",
      title: "Destacado",
      align: "center",
      sortable: false,
    },
    { key: "active", title: "Activo", align: "center", sortable: false },
    { key: "actions", title: "Acciones", align: "center", sortable: false },
  ];

  await fetchCategories();
});

const category = ref({
  id: 0,
  name: "",
  description: "",
  slug: "",
  icon: "",
  featured: false,
  active: true,
});

const group = ref({
  id: 0,
  name: "",
  description: "",
  slug: "",
  featured: false,
  active: true,
  category_id: 0,
});

const subgroup = ref({
  id: 0,
  name: "",
  description: "",
  slug: "",
  featured: false,
  active: true,
  group_id: 0,
  category_id: 0,
});

const rules = useFormRules;

const dialog_create_category = ref(false);

const dialog_create_group = ref(false);

const dialog_create_subgroup = ref(false);

const dialog_update_category = ref(false);

const dialog_update_group = ref(false);

const dialog_update_subgroup = ref(false);

const dialog_delete_category = ref(false);

const dialog_delete_group = ref(false);

const dialog_delete_subgroup = ref(false);

const form_create_category = ref(null);

const form_update_category = ref(null);

const form_create_group = ref(null);

const form_update_group = ref(null);

const form_create_subgroup = ref(null);

const form_update_subgroup = ref(null);

const Modal = (dialog, selected) => {
  category.value = {
    id: 0,
    name: "",
    description: "",
    slug: "",
    icon: "",
    featured: false,
    active: true,
  };

  group.value = {
    id: 0,
    name: "",
    description: "",
    slug: "",
    featured: false,
    active: true,
    category_id: null,
  };

  subgroup.value = {
    id: 0,
    name: "",
    description: "",
    slug: "",
    featured: false,
    active: true,
    group_id: null,
    category_id: null,
  };

  groups_selected.value = [];

  if (dialog === "create_category") {
    dialog_create_category.value = true;
  } else if (dialog === "create_group") {
    dialog_create_group.value = true;
  } else if (dialog === "create_subgroup") {
    dialog_create_subgroup.value = true;
  } else if (dialog === "update_category") {
    dialog_update_category.value = true;
    category.value = { ...selected };
  } else if (dialog === "update_group") {
    dialog_update_group.value = true;
    group.value = { ...selected };
  } else if (dialog === "update_subgroup") {
    dialog_update_subgroup.value = true;
    subgroup.value = { ...selected };
  } else if (dialog === "delete_category") {
    dialog_delete_category.value = true;
    category.value = { ...selected };
  } else if (dialog === "delete_group") {
    dialog_delete_group.value = true;
    group.value = { ...selected };
  } else if (dialog === "delete_subgroup") {
    dialog_delete_subgroup.value = true;
    subgroup.value = { ...selected };
  }
};

const storeCategory = async () => {
  const { valid } = await form_create_category.value.validate();

  if (valid) {
    try {
      fetch_loader_form.value = true;
      const response = await axios.post(route("categories.store"), {
        name: category.value.name,
        slug: category.value.slug,
        icon: category.value.icon,
        featured: category.value.featured,
        active: category.value.active,
      });

      if (response.status === 201) {
        categories.value.push(response.data.category);

        showAlert(true, response.data.message, "#D4E7C5", 2000);
      }
    } catch (error) {
      handleError(error, "Error al guardar la categoria");
    } finally {
      fetch_loader_form.value = false;
      dialog_create_category.value = false;

      await form_create_category.value.reset();
    }
  }
};

const updateCategory = async () => {
  const { valid } = await form_update_category.value.validate();

  if (valid) {
    try {
      fetch_loader_form.value = true;
      const response = await axios.patch(route("categories.update", category.value.id), {
        name: category.value.name,
        slug: category.value.slug,
        icon: category.value.icon,
        featured: category.value.featured,
        active: category.value.active,
      });

      if (response.status === 200) {
        const index = categories.value.findIndex((item) => item.id === category.value.id);

        categories.value[index] = response.data.category;

        showAlert(true, response.data.message, "#D4E7C5", 2000);
      }
    } catch (error) {
      handleError(error, "Error al actualizar la categoria");
    } finally {
      fetch_loader_form.value = false;
      dialog_update_category.value = false;

      await form_update_category.value.reset();
    }
  }
};

const deleteCategory = async () => {
  try {
    fetch_loader_form.value = true;
    const response = await axios.delete(route("categories.destroy", category.value.id));

    if (response.status === 200) {
      categories.value = categories.value.filter((c) => c.id !== category.value.id);

      showAlert(true, response.data.message, "#D4E7C5", 2000);
    }
  } catch (error) {
    handleError(error, "Error al eliminar la categoria");
  } finally {
    fetch_loader_form.value = false;
    dialog_delete_category.value = false;
  }
};

const storeGroup = async () => {
  const { valid } = await form_create_group.value.validate();

  if (valid) {
    try {
      fetch_loader_form.value = true;
      const response = await axios.post(
        route("categories.groups.store", {
          categoria: group.value.category_id,
          grupo: group.value.id,
        }),
        {
          category_id: group.value.category_id,
          name: group.value.name,
          slug: group.value.slug,
          featured: group.value.featured,
          active: group.value.active,
        }
      );

      if (response.status === 201) {
        const index = categories.value.findIndex((c) => c.id === group.value.category_id);

        categories.value[index] = response.data.category;

        showAlert(true, response.data.message, "#D4E7C5", 2000);
      }
    } catch (error) {
      handleError(error, "Error al guardar el grupo");
    } finally {
      fetch_loader_form.value = false;
      dialog_create_group.value = false;

      await form_create_group.value.reset();
    }
  }
};

const updateGroup = async () => {
  const { valid } = await form_update_group.value.validate();

  if (valid) {
    try {
      fetch_loader_form.value = true;
      const response = await axios.patch(
        route("categories.groups.update", {
          categoria: group.value.category_id,
          grupo: group.value.id,
        }),
        {
          category_id: group.value.category_id,
          name: group.value.name,
          slug: group.value.slug,
          featured: group.value.featured,
          active: group.value.active,
        }
      );

      if (response.status === 200) {
        const index = categories.value.findIndex((c) => c.id === group.value.category_id);

        categories.value[index] = response.data.category;

        showAlert(true, response.data.message, "#D4E7C5", 2000);
      }
    } catch (error) {
      handleError(error, "Error al actualizar el grupo");
    } finally {
      fetch_loader_form.value = false;
      dialog_update_group.value = false;

      await form_update_group.value.reset();
    }
  }
};

const deleteGroup = async () => {
  try {
    fetch_loader_form.value = true;
    const response = await axios.delete(
      route("categories.groups.destroy", {
        categoria: group.value.category_id,
        grupo: group.value.id,
      })
    );

    if (response.status === 200) {
      const index = categories.value.findIndex((c) => c.id === group.value.category_id);

      categories.value[index] = response.data.category;

      showAlert(true, response.data.message, "#D4E7C5", 2000);
    }
  } catch (error) {
    handleError(error, "Error al eliminar el grupo");
  } finally {
    fetch_loader_form.value = false;
    dialog_delete_group.value = false;
  }
};

const storeSubgroup = async () => {
  const { valid } = await form_create_subgroup.value.validate();

  if (valid) {
    try {
      fetch_loader_form.value = true;
      const response = await axios.post(
        route("categories.groups.subgroups.store", {
          categoria: subgroup.value.category_id,
          grupo: subgroup.value.group_id,
          subgrupo: subgroup.value.id,
        }),
        {
          category_id: subgroup.value.category_id,
          group_id: subgroup.value.group_id,
          name: subgroup.value.name,
          slug: subgroup.value.slug,
          featured: subgroup.value.featured,
          active: subgroup.value.active,
        }
      );

      if (response.status === 201) {
        const index = categories.value.findIndex(
          (c) => c.id === subgroup.value.category_id
        );

        categories.value[index] = response.data.category;

        showAlert(true, response.data.message, "#D4E7C5", 2000);
      }
    } catch (error) {
      handleError(error, error.response.data.message);
    } finally {
      fetch_loader_form.value = false;
      dialog_create_subgroup.value = false;

      await form_create_subgroup.value.reset();
    }
  }
};

const updateSubgroup = async () => {
  const { valid } = await form_update_subgroup.value.validate();

  if (valid) {
    try {
      fetch_loader_form.value = true;
      const response = await axios.patch(
        route("categories.groups.subgroups.update", {
          categoria: subgroup.value.category_id,
          grupo: subgroup.value.group_id,
          subgrupo: subgroup.value.id,
        }),
        {
          category_id: subgroup.value.category_id,
          group_id: subgroup.value.group_id,
          name: subgroup.value.name,
          slug: subgroup.value.slug,
          featured: subgroup.value.featured,
          active: subgroup.value.active,
        }
      );

      if (response.status === 200) {
        const index = categories.value.findIndex(
          (c) => c.id === subgroup.value.category_id
        );

        categories.value[index] = response.data.category;

        showAlert(true, response.data.message, "#D4E7C5", 2000);
      }
    } catch (error) {
      handleError(error, error.response.data.message);
    } finally {
      fetch_loader_form.value = false;
      dialog_update_subgroup.value = false;

      await form_update_subgroup.value.reset();
    }
  }
};

const deleteSubgroup = async () => {
  try {
    fetch_loader_form.value = true;
    const response = await axios.delete(
      route("categories.groups.subgroups.destroy", {
        categoria: subgroup.value.category_id,
        grupo: subgroup.value.group_id,
        subgrupo: subgroup.value.id,
      })
    );

    if (response.status === 200) {
      const index = categories.value.findIndex(
        (c) => c.id === subgroup.value.category_id
      );

      categories.value[index] = response.data.category;

      showAlert(true, response.data.message, "#D4E7C5", 2000);
    }
  } catch (error) {
    handleError(error, error.response.data.message);
  } finally {
    fetch_loader_form.value = false;
    dialog_delete_subgroup.value = false;
  }
};

const generateCategorySlug = () => {
  category.value.slug = category.value.name
    .toLowerCase()
    .replace(/ /g, "-")
    .replace(/á/g, "a")
    .replace(/é/g, "e")
    .replace(/í/g, "i")
    .replace(/ó/g, "o")
    .replace(/ú/g, "u")
    .replace(/ñ/g, "n")
    .replace(/[^a-z0-9-]/g, "");
};

const generateGroupSlug = () => {
  group.value.slug = group.value.name
    .toLowerCase()
    .replace(/ /g, "-")
    .replace(/á/g, "a")
    .replace(/é/g, "e")
    .replace(/í/g, "i")
    .replace(/ó/g, "o")
    .replace(/ú/g, "u")
    .replace(/ñ/g, "n")
    .replace(/[^a-z0-9-]/g, "");
};

const generateSubgroupSlug = () => {
  subgroup.value.slug = subgroup.value.name
    .toLowerCase()
    .replace(/ /g, "-")
    .replace(/á/g, "a")
    .replace(/é/g, "e")
    .replace(/í/g, "i")
    .replace(/ó/g, "o")
    .replace(/ú/g, "u")
    .replace(/ñ/g, "n")
    .replace(/[^a-z0-9-]/g, "");
};

const groups_selected = ref([]);

const updateGroups = () => {
  const selectedCategory = categories.value.find(
    (c) => c.id === subgroup.value.category_id
  );
  if (selectedCategory) {
    groups_selected.value = selectedCategory.groups;
  } else {
    groups_selected.value = [];
  }
};

const alert = ref({
  status: false,
  message: "",
  color: "#D4E7C5",
  timeout: 1000,
  vertical: true,
  location: "right top",
  rounded: "md",
});

const showAlert = (status, message, color, timeout) => {
  alert.value.status = status;
  alert.value.message = message;
  alert.value.color = color;
  alert.value.timeout = timeout;
};

const handleError = (error, errorMessage) => {
  console.error(error);
  showAlert(true, errorMessage, "#F9D7DA", 3000);
};
</script>

<template>
    <Head title="Categorias">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </Head>

  <Notification
    v-model="alert.status"
    :message="alert.message"
    :color="alert.color"
    :timeout="alert.timeout"
    :vertical="alert.vertical"
    :location="alert.location"
    :rounded="alert.rounded"
    style="margin: 6.5rem 2rem 0 0"
  >
    {{ alert.message }}
  </Notification>

  <v-dialog v-model="dialog_create_category" width="400" persistent>
    <v-card
      max-width="400"
      title="Guardar Categoria"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-form
        @submit.prevent="storeCategory"
        ref="form_create_category"
        validate-on="submit lazy"
      >
        <v-card-text class="px-8 py-8">
          <v-text-field
            density="comfortable"
            v-model="category.name"
            label="Nombre de la Categoria (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: Tecnología"
            :rules="[rules.required]"
            @input="generateCategorySlug"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="category.slug"
            label="Slug (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: tecnologia"
            :rules="[rules.required, rules.slug]"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="category.icon"
            label="Icono"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: mdi-laptop"
            :rules="[]"
          >
          </v-text-field>

          <div class="flex justify-between align-center px-6 pr-10">
            <v-checkbox
              label="Activo"
              v-model="category.active"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
            <v-checkbox
              label="Destacado"
              v-model="category.featured"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
          </div>

          <small class="text-caption text-medium-emphasis"> (*) Campos requeridos </small>
        </v-card-text>
        <v-card-actions>
          <v-divider></v-divider>
          <v-btn @click="dialog_create_category = false"> Cancelar </v-btn>

          <v-btn type="submit" color="primary" :loading="fetch_loader_form">
            Guardar
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_update_category" width="400" persistent>
    <v-card
      max-width="400"
      title="Actualizar Categoria"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-form
        @submit.prevent="updateCategory"
        ref="form_update_category"
        validate-on="submit lazy"
      >
        <v-card-text class="px-8 py-8">
          <v-text-field
            density="comfortable"
            v-model="category.name"
            label="Nombre de la Categoria (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: Tecnología"
            :rules="[rules.required]"
            @input="generateCategorySlug"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="category.slug"
            label="Slug (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: tecnologia"
            :rules="[rules.required, rules.slug]"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="category.icon"
            label="Icono"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: mdi-laptop"
            :rules="[]"
          >
          </v-text-field>

          <div class="flex justify-between align-center px-6 pr-10">
            <v-checkbox
              label="Activo"
              v-model="category.active"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
            <v-checkbox
              label="Destacado"
              v-model="category.featured"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
          </div>

          <small class="text-caption text-medium-emphasis"> (*) Campos requeridos </small>
        </v-card-text>
        <v-card-actions>
          <v-divider></v-divider>
          <v-btn @click="dialog_update_category = false"> Cancelar </v-btn>

          <v-btn type="submit" color="primary" :loading="fetch_loader_form">
            Actualizar
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_delete_category" width="450" persistent>
    <v-card
      max-width="450"
      title="Eliminar Categoria"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-card-text class="px-8 py-8">
        ¿Estás seguro de eliminar la categoria
        <strong>{{ category.name }}</strong
        >?
      </v-card-text>
      <v-card-actions>
        <v-divider></v-divider>
        <v-btn @click="dialog_delete_category = false"> Cancelar </v-btn>

        <v-btn @click="deleteCategory" color="primary" :loading="fetch_loader_form">
          Eliminar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_create_group" width="400" persistent>
    <v-card
      max-width="400"
      title="Guardar Grupo"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-form
        @submit.prevent="storeGroup"
        ref="form_create_group"
        validate-on="submit lazy"
      >
        <v-card-text class="px-8 py-8">
          <v-autocomplete
            v-model="group.category_id"
            :items="categories"
            item-title="name"
            item-value="id"
            label="Categoria (*)"
            color="primary"
            variant="outlined"
            required
            :rules="[rules.required]"
            chips
            :loading="fetch_loader"
            density="comfortable"
          >
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
            </template>

            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-title> No hay categorias disponibles </v-list-item-title>
              </v-list-item>
            </template>
          </v-autocomplete>

          <v-text-field
            density="comfortable"
            v-model="group.name"
            label="Nombre del Grupo (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: Computadoras"
            :rules="[rules.required]"
            @input="generateGroupSlug"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="group.slug"
            label="Slug"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: computadoras"
            :rules="[rules.required, rules.slug]"
          >
          </v-text-field>

          <div class="flex justify-between align-center px-6 pr-8">
            <v-checkbox
              label="Activo"
              v-model="group.active"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
            <v-checkbox
              label="Destacado"
              v-model="group.featured"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
          </div>

          <small class="text-caption text-medium-emphasis"> (*) Campos requeridos </small>
        </v-card-text>
        <v-card-actions>
          <v-divider></v-divider>
          <v-btn @click="dialog_create_group = false"> Cancelar </v-btn>

          <v-btn type="submit" color="primary" :loading="fetch_loader_form">
            Guardar
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_update_group" width="400" persistent>
    <v-card
      max-width="400"
      title="Actualizar Grupo"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-form
        @submit.prevent="updateGroup"
        ref="form_update_group"
        validate-on="submit lazy"
      >
        <v-card-text class="px-8 py-8">
          <v-autocomplete
            v-model="group.category_id"
            :items="categories"
            item-title="name"
            item-value="id"
            label="Categoria (*)"
            color="primary"
            variant="outlined"
            required
            disabled
            :rules="[rules.required]"
            chips
            :loading="fetch_loader"
            density="comfortable"
          >
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
            </template>

            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-title> No hay categorias disponibles </v-list-item-title>
              </v-list-item>
            </template>
          </v-autocomplete>

          <v-text-field
            density="comfortable"
            v-model="group.name"
            label="Nombre del Grupo (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: Computadoras"
            :rules="[rules.required]"
            @input="generateGroupSlug"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="group.slug"
            label="Slug"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: computadoras"
            :rules="[rules.required, rules.slug]"
          >
          </v-text-field>

          <div class="flex justify-between align-center px-6 pr-8">
            <v-checkbox
              label="Activo"
              v-model="group.active"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
            <v-checkbox
              label="Destacado"
              v-model="group.featured"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
          </div>

          <small class="text-caption text-medium-emphasis"> (*) Campos requeridos </small>
        </v-card-text>
        <v-card-actions>
          <v-divider></v-divider>
          <v-btn @click="dialog_update_group = false"> Cancelar </v-btn>
          <v-btn type="submit" color="primary" :loading="fetch_loader_form">
            Actualizar
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_delete_group" width="450" persistent>
    <v-card
      max-width="450"
      title="Eliminar Grupo"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-card-text class="px-8 py-8">
        ¿Estás seguro de eliminar el grupo
        <strong>{{ group.name }}</strong
        >?
      </v-card-text>
      <v-card-actions>
        <v-divider></v-divider>
        <v-btn @click="dialog_delete_group = false"> Cancelar </v-btn>

        <v-btn @click="deleteGroup" color="primary" :loading="fetch_loader_form">
          Eliminar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_create_subgroup" width="400" persistent>
    <v-card
      max-width="400"
      title="Guardar Sub Grupo"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-form
        @submit.prevent="storeSubgroup"
        ref="form_create_subgroup"
        validate-on="submit lazy"
      >
        <v-card-text class="px-8 py-8">
          <v-autocomplete
            v-model="subgroup.category_id"
            :items="categories"
            item-title="name"
            item-value="id"
            label="Categoria (*)"
            color="primary"
            variant="outlined"
            required
            :rules="[rules.required]"
            chips
            :loading="fetch_loader"
            density="comfortable"
            @update:focused="updateGroups"
          >
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
            </template>

            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-title> No hay categorias disponibles </v-list-item-title>
              </v-list-item>
            </template>
          </v-autocomplete>

          <v-autocomplete
            v-model="subgroup.group_id"
            :items="groups_selected"
            item-title="name"
            item-value="id"
            label="Grupo (*)"
            color="primary"
            variant="outlined"
            required
            :rules="[rules.required]"
            chips
            :loading="fetch_loader"
            density="comfortable"
            :disabled="subgroup.category_id === null"
          >
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
            </template>

            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-title> No hay grupos disponibles </v-list-item-title>
              </v-list-item>
            </template>
          </v-autocomplete>

          <v-text-field
            density="comfortable"
            v-model="subgroup.name"
            label="Nombre del Sub Grupo (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: Laptops"
            :rules="[rules.required]"
            @input="generateSubgroupSlug"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="subgroup.slug"
            label="Slug"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: laptops"
            :rules="[rules.required, rules.slug]"
          >
          </v-text-field>

          <div class="flex justify-between align-center px-6 pr-8">
            <v-checkbox
              label="Activo"
              v-model="subgroup.active"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
            <v-checkbox
              label="Destacado"
              v-model="subgroup.featured"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
          </div>

          <small class="text-caption text-medium-emphasis"> (*) Campos requeridos </small>
        </v-card-text>
        <v-card-actions>
          <v-divider></v-divider>
          <v-btn @click="dialog_create_subgroup = false"> Cancelar </v-btn>
          <v-btn type="submit" color="primary" :loading="fetch_loader_form">
            Guardar
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_update_subgroup" width="400" persistent>
    <v-card
      max-width="400"
      title="Actualizar Sub Grupo"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-form
        @submit.prevent="updateSubgroup"
        ref="form_update_subgroup"
        validate-on="submit lazy"
      >
        <v-card-text class="px-8 py-8">
          <v-autocomplete
            v-model="subgroup.category_id"
            :items="categories"
            item-title="name"
            item-value="id"
            label="Categoria (*)"
            color="primary"
            variant="outlined"
            required
            :rules="[rules.required]"
            chips
            :loading="fetch_loader"
            density="comfortable"
            @update:focused="updateGroups"
            disabled
          >
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
            </template>

            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-title> No hay categorias disponibles </v-list-item-title>
              </v-list-item>
            </template>
          </v-autocomplete>

          <v-autocomplete
            v-model="subgroup.group_id"
            :items="groups_selected"
            item-title="name"
            item-value="id"
            label="Grupo (*)"
            color="primary"
            variant="outlined"
            required
            :rules="[rules.required]"
            chips
            :loading="fetch_loader"
            density="comfortable"
            disabled
          >
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
            </template>

            <template v-slot:no-data>
              <v-list-item>
                <v-list-item-title> No hay grupos disponibles </v-list-item-title>
              </v-list-item>
            </template>
          </v-autocomplete>

          <v-text-field
            density="comfortable"
            v-model="subgroup.name"
            label="Nombre del Sub Grupo (*)"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: Laptops"
            :rules="[rules.required]"
            @input="generateSubgroupSlug"
          >
          </v-text-field>

          <v-text-field
            density="comfortable"
            v-model="subgroup.slug"
            label="Slug"
            color="primary"
            variant="outlined"
            required
            hint="Ejemplo: laptops"
            :rules="[rules.required, rules.slug]"
          >
          </v-text-field>

          <div class="flex justify-between align-center px-6 pr-8">
            <v-checkbox
              label="Activo"
              v-model="subgroup.active"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
            <v-checkbox
              label="Destacado"
              v-model="subgroup.featured"
              color="primary"
              :rules="[rules.boolean]"
            >
            </v-checkbox>
          </div>

          <small class="text-caption text-medium-emphasis"> (*) Campos requeridos </small>
        </v-card-text>
        <v-card-actions>
          <v-divider></v-divider>
          <v-btn @click="dialog_update_subgroup = false"> Cancelar </v-btn>
          <v-btn type="submit" color="primary" :loading="fetch_loader_form">
            Actualizar
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>

  <v-dialog v-model="dialog_delete_subgroup" width="450" persistent>
    <v-card
      max-width="450"
      title="Eliminar Sub Grupo"
      prepend-icon="mdi-format-list-group-plus"
    >
      <v-card-text class="px-8 py-8">
        ¿Estás seguro de eliminar el subgrupo
        <strong>{{ subgroup.name }}</strong
        >?
      </v-card-text>
      <v-card-actions>
        <v-divider></v-divider>
        <v-btn @click="dialog_delete_subgroup = false"> Cancelar </v-btn>
        <v-btn @click="deleteSubgroup" color="primary" :loading="fetch_loader_form">
          Eliminar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <AuthenticatedLayout>
    <div class="py-6 h-full">
      <div class="max-w-8xl mx-auto sm:px-4 lg:px-6">
        <div class="overflow-hidden sm:rounded-sm lg:rounded-lg grid place-items-center">
          <section class="bg-gris-300 w-full grid place-items-center">
            <div
              class="max-w-screen-2xl w-full px-4 py-4 sm:px-6 sm:py-4 lg:px-12 lg:py-12 bg-white-200 rounded-lg"
            >
              <SimpleHeader
                title="Gestiona tus Categorias"
                breadcrumb="Categorias"
                :breadcrumb_link="route('categories.index')"
              />

              <article class="mt-4">
                <v-card flat>
                  <template v-slot:title>
                    <v-row
                      align="center"
                      justify="space-between"
                      density="comfortable"
                      class="p-2"
                      no-gutters
                    >
                      <v-col cols="12" md="4" lg="6">
                        <v-sheet>
                          <h2 class="text-lg md:text-xl lg:text-xl text-gray-900">
                            Categorias
                          </h2>
                        </v-sheet>
                      </v-col>
                      <v-col cols="12" md="3" lg="2">
                        <v-sheet>
                          <v-btn
                            color="primary"
                            @click="Modal('create_category')"
                            variant="tonal"
                          >
                            <v-icon left>mdi-plus</v-icon>
                            Agregar Categoria
                          </v-btn>
                        </v-sheet>
                      </v-col>
                      <v-col cols="12" md="2" lg="2">
                        <v-sheet class="mx-2 px-2">
                          <v-btn
                            color="primary"
                            @click="Modal('create_group')"
                            variant="tonal"
                          >
                            <v-icon left>mdi-plus</v-icon>
                            Agregar Grupo
                          </v-btn>
                        </v-sheet>
                      </v-col>
                      <v-col cols="12" md="3" lg="2">
                        <v-sheet>
                          <v-btn
                            color="primary"
                            @click="Modal('create_subgroup')"
                            variant="tonal"
                          >
                            <v-icon left>mdi-plus</v-icon>
                            Agregar SubGrupo
                          </v-btn>
                        </v-sheet>
                      </v-col>
                    </v-row>
                  </template>

                  <template v-slot:text>
                    <v-text-field
                      v-model="search"
                      label="Buscar"
                      prepend-inner-icon="mdi-magnify"
                      single-line
                      variant="outlined"
                      hide-details
                      color="primary"
                      density="comfortable"
                    >
                    </v-text-field>

                    <v-data-table
                      :loading="fetch_loader"
                      loading-text="Cargando Categorias ..."
                      v-model:page="page"
                      :items-per-page="items_per_page"
                      :headers="headers"
                      :search="search"
                      :items="categories"
                      show-expand
                      :page="page"
                      :total-items="categories.length"
                      :no-data-text="`No hay categorias`"
                      items-per-page-text="Categorias por página"
                    >

                        <template v-slot:item.index="{ index }">
                            {{ index + 1 }}
                        </template>

                        <template v-slot:loader="{ isActive }">
                            <v-progress-linear
                            :active="isActive"
                            color="primary"
                            height="3"
                            indeterminate
                            >
                            </v-progress-linear>
                        </template>

                        <template v-slot:item.icon="{ item }">
                            <v-icon>{{ item.icon }}</v-icon>
                        </template>

                        <template v-slot:item.featured="{ item }">
                            <v-chip v-if="item.featured" color="success"> Sí </v-chip>
                            <v-chip v-else color="error"> No </v-chip>
                        </template>

                        <template v-slot:item.active="{ item }">
                            <v-chip v-if="item.active" color="success"> Sí </v-chip>
                            <v-chip v-else color="error"> No </v-chip>
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <v-icon
                            @click="Modal('update_category', item)"
                            class="cursor-pointer"
                            color="primary"
                            style="margin-right: 0.5rem"
                            >
                            mdi-pencil
                            </v-icon>
                            <template v-if="item.groups && item.groups.length === 0">
                            <v-icon
                                @click="Modal('delete_category', item)"
                                class="cursor-pointer"
                                color="primary"
                                style="margin-left: 0.5rem"
                            >
                                mdi-delete
                            </v-icon>
                            </template>
                        </template>

                        <template v-slot:expanded-row="{ columns, item }">
                            <tr>
                            <td
                                :colspan="columns.length"
                                style="padding: 1rem 3rem 1rem 3rem"
                            >
                                <v-text-field
                                v-model="search_group"
                                label="Buscar Grupo"
                                prepend-inner-icon="mdi-magnify"
                                variant="outlined"
                                hide-details
                                density="comfortable"
                                color="primary"
                                style="margin: 1rem 0rem 0 0rem"
                                >
                                </v-text-field>

                                <v-data-table
                                :headers="headers_groups"
                                :items="item.groups"
                                :items-per-page="items_per_page_groups"
                                v-model:page="page_groups"
                                show-expand
                                itemsPerPageText="Grupos por página"
                                no-data-text="No hay grupos"
                                >
                                    <template v-slot:item.index="{ index }">
                                        {{ index + 1 }}
                                    </template>

                                    <template v-slot:loader="{ isActive }">
                                        <v-progress-linear
                                        :active="isActive"
                                        color="primary"
                                        height="3"
                                        indeterminate
                                        >
                                        </v-progress-linear>
                                    </template>

                                    <template v-slot:item.featured="{ item }">
                                        <v-chip v-if="item.featured" color="success" label>
                                        Sí
                                        </v-chip>
                                        <v-chip v-else color="error" label> No </v-chip>
                                    </template>

                                    <template v-slot:item.active="{ item }">
                                        <v-chip v-if="item.active" color="success" label>
                                        Sí
                                        </v-chip>
                                        <v-chip v-else color="error" label> No </v-chip>
                                    </template>

                                    <template v-slot:item.actions="{ item }">
                                        <v-icon
                                        @click="Modal('update_group', item)"
                                        class="cursor-pointer"
                                        color="primary"
                                        style="margin-right: 0.5rem"
                                        >
                                        mdi-pencil
                                        </v-icon>

                                        <template
                                        v-if="item.subgroups && item.subgroups.length === 0"
                                        >
                                        <v-icon
                                            @click="Modal('delete_group', item)"
                                            class="cursor-pointer"
                                            color="primary"
                                            style="margin-left: 0.5rem"
                                        >
                                            mdi-delete
                                        </v-icon>
                                        </template>
                                    </template>

                                    <template v-slot:expanded-row="{ columns, item }">
                                        <tr>
                                        <td
                                            :colspan="columns.length"
                                            style="padding: 1rem 5rem 1rem 5rem"
                                        >
                                            <v-text-field
                                            v-model="search_subgroup"
                                            label="Buscar Sub Grupo"
                                            prepend-inner-icon="mdi-magnify"
                                            variant="outlined"
                                            hide-details
                                            density="comfortable"
                                            color="primary"
                                            style="margin: 1rem 0rem 0 0rem"
                                            >
                                            </v-text-field>

                                            <v-data-table
                                            :headers="headers_subgroups"
                                            :items="item.subgroups"
                                            :items-per-page="items_per_page_subgroups"
                                            v-model:page="page_subgroups"
                                            show-expand
                                            itemsPerPageText="Sub Grupos por página"
                                            no-data-text="No hay subgrupos"
                                            >
                                                <template v-slot:item.index="{ index }">
                                                    {{ index + 1 }}
                                                </template>

                                                <template v-slot:loader="{ isActive }">
                                                    <v-progress-linear
                                                    :active="isActive"
                                                    color="primary"
                                                    height="3"
                                                    indeterminate
                                                    >
                                                    </v-progress-linear>
                                                </template>

                                                <template v-slot:item.featured="{ item }">
                                                    <v-chip
                                                    v-if="item.featured"
                                                    color="success"
                                                    label
                                                    >
                                                    Sí
                                                    </v-chip>
                                                    <v-chip v-else color="error" label> No </v-chip>
                                                </template>

                                                <template v-slot:item.active="{ item }">
                                                    <v-chip v-if="item.active" color="success" label>
                                                    Sí
                                                    </v-chip>
                                                    <v-chip v-else color="error" label> No </v-chip>
                                                </template>

                                                <template v-slot:item.actions="{ item }">
                                                    <v-icon
                                                    @click="Modal('update_subgroup', item)"
                                                    class="cursor-pointer"
                                                    color="primary"
                                                    style="margin-right: 0.5rem"
                                                    >
                                                    mdi-pencil
                                                    </v-icon>
                                                    <v-icon
                                                    @click="Modal('delete_subgroup', item)"
                                                    class="cursor-pointer"
                                                    color="primary"
                                                    style="margin-left: 0.5rem"
                                                    >
                                                    mdi-delete
                                                    </v-icon>
                                                </template>
                                            </v-data-table>
                                        </td>
                                        </tr>
                                    </template>
                                </v-data-table>
                            </td>
                            </tr>
                        </template>
                    </v-data-table>
                  </template>
                </v-card>
              </article>
            </div>
          </section>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
