<script setup>
import { computed, ref, onMounted } from "vue";

import AppMenuItem from "./AppMenuItem.vue";
import { userStore } from "@/app/store/user";
const useUserStore = userStore();

function buildMenuStructure(permissions) {
  const menu = [];
  permissions.forEach((permission) => {
    const { description, children } = permission;
    const item = { label: description, items: buildSubmenuEstructure(children) };
    menu.push(item);
  });
  return menu;
}

function buildSubmenuEstructure(items) {
    if (!Array.isArray(items)) {
        return [];
    }
    const submenu = [];
    items.forEach((item) => {
        if (item.children) {
            const { description, icon, children } = item;
            const subItem = { label: capitalizeFirstLetter(description), icon: icon, items: buildSubmenuEstructure(children) };
            submenu.push(subItem);
        } else {
            const { description, icon, route } = item;
            submenu.push({ label: capitalizeFirstLetter(description), icon: icon, to: route });
        }
    })
    return submenu;
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}


const menu = computed(() => {
  return buildMenuStructure(useUserStore.permissions);
});
</script>

<template>
  <ul class="layout-menu">
    <template v-for="(item, i) in menu" :key="item">
      <app-menu-item
        v-if="!item.separator"
        :item="item"
        :index="i"
      ></app-menu-item>
      <li v-if="item.separator" class="menu-separator"></li>
    </template>
  </ul>
</template>

<style lang="scss" scoped></style>
