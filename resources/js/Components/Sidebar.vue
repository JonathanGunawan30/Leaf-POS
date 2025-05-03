<template>
  <div class="flex h-screen overflow-hidden">
    <div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-green-600 to-green-800 text-white shadow-lg flex flex-col">
      <div class="p-6 border-b border-white/10">
        <div class="flex items-center space-x-3">
          <img src="/leaf-icon.png" alt="Leaf POS Logo" class="w-8 h-8">
          <h3 class="text-xl font-semibold text-white">Leaf POS</h3>
        </div>
      </div>
      <nav class="flex-1 overflow-y-auto p-4">
        <ul class="space-y-2">
          <li v-for="item in navigationItems" :key="item.name">
            <Link
              v-if="!item.submenu"
              :href="item.path"
              class="flex items-center px-4 py-3 rounded-lg transition-colors hover:bg-white/10"
              :class="{ 'bg-white/20': $page.url === item.path }"
            >
              <i :class="[item.icon, 'text-xl mr-3']"></i>
              <span>{{ item.name }}</span>
            </Link>
            <!-- Submenu item -->
            <div v-else>
              <button
                @click="toggleSubmenu(item.name)"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors hover:bg-white/10"
                :class="{ 'bg-white/20': activeSubmenu === item.name }"
              >
                <div class="flex items-center">
                  <i :class="[item.icon, 'text-xl mr-3']"></i>
                  <span>{{ item.name }}</span>
                </div>
                <i
                  :class="[
                    activeSubmenu === item.name
                      ? 'ri-arrow-down-s-line'
                      : 'ri-arrow-right-s-line',
                    'text-xl transition-transform'
                  ]"
                ></i>
              </button>
              <ul
                v-show="activeSubmenu === item.name"
                class="mt-1 ml-4 space-y-1"
              >
                <li v-for="subItem in item.submenu" :key="subItem.path">
                  <Link
                    :href="subItem.path"
                    class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors hover:bg-white/10"
                    :class="{ 'bg-white/20': $page.url === subItem.path }"
                  >
                    <span class="ml-5">{{ subItem.name }}</span>
                  </Link>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
    </div>
    <div class="flex-1 ml-64">
      <Navbar />
      <div class="p-8 overflow-auto">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Navbar from './Navbar.vue'

const page = usePage()
const activeSubmenu = ref(null)

const navigationItems = [
  {
    name: 'Dashboard',
    path: '/dashboard',
    icon: 'ri-dashboard-fill'
  },
  {
    name: 'Products',
    path: '/products',
    icon: 'ri-shopping-bag-fill',
    submenu: [
      {
        name: 'All Products',
        path: '/products/all'
      },
      {
        name: 'Create Products',
        path: '/products/create'
      },
      {
        name: 'Categories',
        path: '/products/categories'
      }
    ]
  }
]

const toggleSubmenu = (menu) => {
  activeSubmenu.value = activeSubmenu.value === menu ? null : menu
}
</script>

<style scoped>
nav::-webkit-scrollbar {
  width: 6px;
}

nav::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
}

nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 8px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

nav {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.2) rgba(255, 255, 255, 0.1);
}
</style>

