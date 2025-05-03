<template>
    <nav class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
      <!-- Logo / Brand -->
      <div class="text-sm text-green-600">
        {{ currentModuleName }}
      </div>
      <div class="flex items-center space-x-4">
        <VueDatePicker
          v-model="dateValue"
          :format="format"
          :locale="locale"
          :max-date="new Date()"
          range
          auto-apply
          :shortcuts="shortcuts"
          :menu-class="menuClass"
          :input-class-name="inputClass"
          :preview-format="previewFormat"
          :enable-time-picker="false"
        >
          <template #trigger>
            <button class="flex items-center space-x-2 px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-green-500">
              <span>{{ formattedRange }}</span>
              <i class="ri-calendar-2-line"></i>
            </button>
          </template>
        </VueDatePicker>
        <button class="flex items-center space-x-2 px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-green-500">
            <i class="ri-notification-line"></i>
        </button>
        <!-- Profile -->
        <div class="relative">
          <button @click="toggleDropdown" class="flex items-center space-x-2 focus:outline-none">
            <div 
              class="w-8 h-8 rounded-md bg-green-500 flex items-center justify-center text-white font-medium transition duration-200 hover:bg-green-600"
            >
              {{ userInitial }}
            </div>
            <span class="text-gray-700 font-medium"> 
              {{ username }}
            </span>
          </button>
    
          <!-- Enhanced Dropdown -->
          <div
            v-if="dropdownOpen"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 transform transition-all duration-200 ease-out"
          >
            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
              <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Profile
            </a>
            
            <Link
              href="/logout"
              method="post"
              as="button"
              class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150"
            >
              <svg class="mr-3 h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Keluar
            </Link>
          </div>
        </div>
      </div>
    </nav>
</template>
  
<script setup>
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const page = usePage()
const dropdownOpen = ref(false)

// Add date range refs
const startDate = ref(new Date().toISOString().split('T')[0])
const endDate = ref(new Date().toISOString().split('T')[0])

const currentModuleName = computed(() => {
  const currentPath = page.url
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

  // Find matching menu item or submenu item
  for (const item of navigationItems) {
    // Check if current path matches main menu item
    if (currentPath === item.path) {
      return item.name
    }
    
    // Check submenu items if they exist
    if (item.submenu) {
      const subItem = item.submenu.find(sub => currentPath === sub.path)
      if (subItem) {
        return `${item.name} / ${subItem.name}`
      }
    }
  }
  return 'Dashboard' // Fallback value
})

const userInitial = computed(() => {
  const username = page.props.auth.username || 'Guest'
  return username.charAt(0).toUpperCase()
})

const username = computed(() => {
    return page.props.auth.username || 'Guest'
})
function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}
document.addEventListener('click', (e) => {
  if (!e.target.closest('.relative')) {
    dropdownOpen.value = false
  }
})

const datePickerOpen = ref(false)

const formattedDateRange = computed(() => {
  const start = new Date(startDate.value).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
  const end = new Date(endDate.value).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
  return `${start} - ${end}`
})

function toggleDatePicker() {
  datePickerOpen.value = !datePickerOpen.value
}

// Close date picker when clicking outside
document.addEventListener('click', (e) => {
  const isDatePickerClick = e.target.closest('.date-picker')
  if (!isDatePickerClick && datePickerOpen.value) {
    datePickerOpen.value = false
  }
})

// Date picker configuration
const dateValue = ref([new Date(), new Date()])

const locale = {
  locale: 'id',
  format: 'DD MMM YYYY'
}

const format = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const previewFormat = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const shortcuts = [
  { label: 'Hari Ini', days: 0 },
  { label: 'Kemarin', days: 1 },
  { label: '7 Hari Terakhir', days: 7 },
  { label: '30 Hari Terakhir', days: 30 },
  { label: 'Bulan Ini', days: 'month' },
  { label: 'Bulan Lalu', days: 'lastMonth' }
]

const menuClass = [
  'dp-menu',
  'bg-white',
  'rounded-lg',
  'shadow-lg',
  'p-4'
]

const inputClass = () => 'dp-input'

const formattedRange = computed(() => {
  if (!dateValue.value || !Array.isArray(dateValue.value)) return ''
  const [start, end] = dateValue.value
  return `${format(start)} ~ ${format(end)}`
})
</script>

<style>
.dp-menu {
  @apply z-50;
}

.dp-input {
  @apply px-4 py-2 text-sm border border-gray-300 rounded-md;
  @apply hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-green-500;
}

:root {
  --dp-primary-color: #10b981;
  --dp-secondary-color: #f3f4f6;
  --dp-success-color: #10b981;
  --dp-font-family: inherit;
}
</style>
