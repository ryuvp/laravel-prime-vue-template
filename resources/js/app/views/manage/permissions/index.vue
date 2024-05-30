<script setup>
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted, onBeforeMount } from 'vue';
import { PermissionsResource } from '@/app/api/permissions';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const permissions = ref(null);
const permissionDialog = ref(false);
const deletePermissionDialog = ref(false);
const deletePermissionsDialog = ref(false);
const permission = ref({});
const selectedPermissions = ref(null);
const dt = ref(null);
const filters = ref({});
const submitted = ref(false);
const selectedCategory = ref(null);
const categories = ref([
    { label: 'Section', value: '0' },
    { label: 'Menu', value: '1' },
    { label: 'Menu-Link', value: '2' }
]);
const sections = ref([
    { label: 'home', value: 'home' },
    { label: 'manage', value: 'manage' },
    { label: 'profile', value: 'profile' }
]);
const parents = ref([
    { label: 'dashboard', value: 'home.dashboard' },
    { label: 'user', value: 'manage.users' },
    { label: 'user', value: 'profile.user' }
]);

const permissionsResource = new PermissionsResource();

onBeforeMount(() => {
    initFilters();
});
onMounted(() => {
    permissionsResource.get().then((data) => {
        permissions.value = data.data;
    });
});

const openNew = () => {
    permission.value = {};
    submitted.value = false;
    permissionDialog.value = true;
};

const hideDialog = () => {
    permissionDialog.value = false;
    submitted.value = false;
};

const saveProduct = () => {
    submitted.value = true;
    if (permission.value.name && permission.value.name.trim() && permission.value.price) {
        if (permission.value.id) {
            permission.value.inventoryStatus = permission.value.inventoryStatus.value ? permission.value.inventoryStatus.value : permission.value.inventoryStatus;
            products.value[findIndexById(permission.value.id)] = permission.value;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Updated', life: 3000 });
        } else {
            permission.value.id = createId();
            permission.value.code = createId();
            permission.value.image = 'product-placeholder.svg';
            permission.value.inventoryStatus = permission.value.inventoryStatus ? permission.value.inventoryStatus.value : 'INSTOCK';
            products.value.push(permission.value);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Created', life: 3000 });
        }
        permissionDialog.value = false;
        permission.value = {};
    }
};

const editPermission = (editPermission) => {
    permission.value = { ...editPermission };
    permissionDialog.value = true;
};

const confirmDeletePermission = (editPermission) => {
    permission.value = editPermission;
    deletePermissionDialog.value = true;
};

const deletePermission = () => {
    permissions.value = permissions.value.filter((val) => val.id !== permission.value.id);
    deletePermissionDialog.value = false;
    permission.value = {};
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Permision Deleted', life: 3000 });
};

const findIndexById = (id) => {
    let index = -1;
    for (let i = 0; i < permissions.value.length; i++) {
        if (permissions.value[i].id === id) {
            index = i;
            break;
        }
    }
    return index;
};

const confirmDeleteSelected = () => {
    deletePermissionsDialog.value = true;
};
const deleteSelectedProducts = () => {
    permissions.value = permissions.value.filter((val) => !selectedPermissions.value.includes(val));
    deletePermissionsDialog.value = false;
    selectedPermissions.value = null;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Permissions Deleted', life: 3000 });
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    };
};
const onChange = (category) =>{
    selectedCategory.value = category.value;
}
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card">
                <Toolbar class="mb-2">
                    <template v-slot:start>
                        <div class="my-2">
                            <h5 class="m-0">Manage Permissions</h5>
                        </div>
                    </template>

                    <template v-slot:end>
                        <Button label="New" icon="pi pi-plus" class="mr-2" severity="success" @click="openNew" />
                        <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected"
                            :disabled="!selectedPermissions || !selectedPermissions.length" />
                    </template>
                </Toolbar>

                <DataTable ref="dt" :value="permissions" v-model:selection="selectedPermissions" dataKey="id"
                    :paginator="true" :rows="10" :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[5, 10, 25]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} permissions">
                    <template #header>
                        <div class="flex flex-column md:flex-row md:justify-content-start md:align-items-center">
                            <IconField iconPosition="left" class="block mt-2 md:mt-0">
                                <InputIcon class="pi pi-search" />
                                <InputText class="w-full sm:w-auto" v-model="filters['global'].value"
                                    placeholder="Search..." />
                            </IconField>
                        </div>
                    </template>

                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column field="name" header="Name" headerStyle="width:24%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Name</span>
                            {{ slotProps.data.name }}
                        </template>
                    </Column>
                    <Column field="description" header="Description" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Description</span>
                            {{ slotProps.data.description }}
                        </template>
                    </Column>
                    <Column field="icon" header="Icon" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Icon</span>
                            <i :class="slotProps.data.icon"></i> {{ slotProps.data.icon }}
                        </template>
                    </Column>
                    <Column field="route" header="Route" headerStyle="width:24%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Route</span>
                            {{ slotProps.data.route }}
                        </template>
                    </Column>

                    <Column field="category" header="Category" headerStyle="width:14%; min-width:10rem;">
                        <template #body="slotProps">
                            <span class="p-column-title">Category</span>
                                <Tag :severity="slotProps.data.category === 2 ? 'success' : (slotProps.data.category === 1 ? 'warning' : 'info')">
                                    {{ slotProps.data.category_name }}
                                </Tag>
                        </template>
                    </Column>
                    <Column headerStyle="min-width:10rem;">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" class="mr-2" severity="success" rounded
                                @click="editPermission(slotProps.data)" />
                            <Button icon="pi pi-trash" class="mt-2" severity="warning" rounded
                                @click="confirmDeleteProduct(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Dialog v-model:visible="permissionDialog" :style="{ width: '450px' }" header="Permission Details"
                    :modal="true" class="p-fluid">

                    <div class="field">
                        <label for="category" class="mb-3">Category</label>
                        <Dropdown id="category" v-model="permission.category" :options="categories" @change="onChange(permission.category)"
                        optionLabel="label" placeholder="Select a Category" />
                    </div>
                    <div v-if="selectedCategory === 'menu'" class="field">
                        <label for="section" class="mb-3">Section</label>
                        <Dropdown id="section" v-model="permission.section" :options="sections" optionLabel="label" placeholder="Select a Section" />
                    </div>
                    <div v-else-if="selectedCategory === 'submenu'" class="field">
                        <label for="parent" class="mb-3">Parent</label>
                        <Dropdown id="parent" v-model="permission.parent" :options="parents" optionLabel="label" placeholder="Select a Parent" />
                    </div>
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model.trim="permission.name" required="true" autofocus
                            :invalid="submitted && !permission.name" :disabled="true" />
                        <small class="p-invalid" v-if="submitted && !permission.name">Name is required.</small>
                    </div>
                    <div class="field">
                        <label for="description">Description</label>
                        <InputText id="description" v-model.trim="permission.description" required="true" autofocus
                            :invalid="submitted && !permission.description" />
                        <small class="p-invalid" v-if="submitted && !permission.description">Description is
                            required.</small>
                    </div>
                    <div class="field">
                        <label for="route">Route</label>
                        <InputText id="route" v-model.trim="permission.route" required="true" autofocus :disabled="true"
                            :invalid="submitted && !permission.route" />
                        <small class="p-invalid" v-if="submitted && !permission.route">Route is required.</small>
                    </div>
                    <div class="field">
                        <label for="icon">Icon</label>
                        <IconField id="icon">
                            <InputIcon :class="permission.icon" />
                            <InputText v-model.trim="permission.icon" required="true" autofocus
                                :invalid="submitted && !permission.icon" />
                        </IconField>
                        <small class="p-invalid" v-if="submitted && !permission.icon">Icon is required.</small>
                    </div>
                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text="" @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" text="" @click="saveProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePermissionDialog" :style="{ width: '450px' }" header="Confirm"
                    :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="product">Are you sure you want to delete <b>{{ permission.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deletePermissionDialog = false" />
                        <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePermissionsDialog" :style="{ width: '450px' }" header="Confirm"
                    :modal="true">
                    <div class="flex align-items-center justify-content-center">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                        <span v-if="product">Are you sure you want to delete the selected products?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deletePermissionsDialog = false" />
                        <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
