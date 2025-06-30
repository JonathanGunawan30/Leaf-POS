<template>
    <div class="flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-[270px] bg-gradient-to-b from-lp-green to-lp-green text-white shadow-lg flex flex-col">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <img src="/images/leafPos1.svg" alt="Leaf POS Logo" class="w-8 h-8" />
                    <h3 class="text-xl font-semibold text-white">Leaf POS</h3>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto p-4">
                <!-- Skeleton loading state -->
                <div v-if="isLoadingUser && !cachedUser" class="space-y-2">
                    <div v-for="i in 8" :key="i" class="animate-pulse">
                        <div class="flex items-center px-4 py-3 rounded-lg">
                            <div class="w-6 h-6 bg-white/20 rounded mr-3 flex-shrink-0"></div>
                            <div class="h-4 bg-white/20 rounded flex-1"></div>
                        </div>
                    </div>
                </div>

                <ul v-else class="space-y-2">
                    <li v-for="item in filteredNavigationItems" :key="item.name">

                        <div v-if="!item.submenu || item.submenu.length === 0">
                            <Link
                                :href="item.path"
                                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors group"
                                :class="{
                                  'bg-white/20 font-medium text-white': isMenuItemActive(item),
                                  'hover:bg-white/10 text-white/90': !isMenuItemActive(item)
                                }"
                            >
                                <div class="w-6 h-6 mr-3 flex-shrink-0 flex items-center justify-center">
                                    <component
                                        :is="item.icon"
                                        class="w-full h-full object-contain"
                                        :class="{
                                          'filter brightness-0 invert': isMenuItemActive(item),
                                          'filter brightness-0 invert opacity-90': !isMenuItemActive(item)
                                        }"
                                    />
                                </div>
                                <span class="truncate text-sm font-medium">{{ item.name }}</span>
                            </Link>
                        </div>

                        <div v-else>
                            <button
                                @click="toggleSubmenu(item.name)"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors group"
                                :class="{
                                  'bg-white/20 font-medium text-white': openedSubmenu === item.name || isSubmenuActive(item),
                                  'hover:bg-white/10 text-white/90': !(openedSubmenu === item.name || isSubmenuActive(item))
                                }"
                            >
                                <div class="flex items-center min-w-0">
                                    <div class="w-6 h-6 mr-3 flex-shrink-0 flex items-center justify-center">
                                        <component
                                            :is="item.icon"
                                            class="w-full h-full object-contain"
                                            :class="{
                                                'filter brightness-0 invert': openedSubmenu === item.name || isSubmenuActive(item),
                                                'filter brightness-0 invert opacity-90': !(openedSubmenu === item.name || isSubmenuActive(item))
                                              }"
                                        />
                                    </div>
                                    <span class="truncate text-sm font-medium">{{ item.name }}</span>
                                </div>
                                <i :class="[
                                  'ri-arrow-right-s-line text-lg transition-transform duration-200 flex-shrink-0 ml-2',
                                  { 'rotate-90': openedSubmenu === item.name || isSubmenuActive(item) }
                                ]"></i>
                            </button>

                            <transition
                                enter-active-class="transition-all duration-200 ease-out overflow-hidden"
                                leave-active-class="transition-all duration-200 ease-in overflow-hidden"
                                enter-from-class="transform opacity-0 max-h-0"
                                enter-to-class="transform opacity-100 max-h-[500px]"
                                leave-from-class="transform opacity-100 max-h-[500px]"
                                leave-to-class="transform opacity-0 max-h-0"
                            >
                                <ul
                                    v-if="openedSubmenu === item.name || isSubmenuActive(item)"
                                    class="mt-1 ml-6 space-y-1"
                                >
                                    <li v-for="subItem in item.submenu" :key="subItem.path">
                                        <Link
                                            :href="subItem.path"
                                            class="flex items-center px-4 py-2 rounded-lg transition-colors group"
                                            :class="{
                                                'bg-white/30 text-white font-medium': isSubItemActive(subItem),
                                                'hover:bg-white/10 text-white/80': !isSubItemActive(subItem)
                                              }"
                                        >
                                            <div class="flex items-center w-full min-w-0">
                                                <div class="w-3 h-3 mr-3 flex-shrink-0 flex items-center justify-center">
                                                    <svg
                                                        width="12"
                                                        height="12"
                                                        viewBox="0 0 14 15"
                                                        fill="none"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="w-full h-full"
                                                        :class="{
                                                          'fill-white': isSubItemActive(subItem),
                                                          'fill-white/60': !isSubItemActive(subItem)
                                                        }"
                                                    >
                                                        <circle cx="7" cy="7.02393" r="7" />
                                                    </svg>
                                                </div>
                                                <span class="truncate text-sm">
                                                  {{ subItem.name }}
                                                </span>
                                            </div>
                                        </Link>
                                    </li>
                                </ul>
                            </transition>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>


        <div class="ml-[270px] flex flex-col min-h-screen w-full">
            <Navbar />
            <div class="flex-grow p-8">
                <slot>
                    <div class="text-gray-400 text-sm text-center py-10">
                        There is no content to display.
                    </div>
                </slot>
            </div>
            <Footer />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'

// ICON IMPORT
import ProductsIcon from '/resources/assets/icons/products.svg';
import DashboardIcon from '/resources/assets/icons/dashboard.svg';
import InventoryIcon from '/resources/assets/icons/inventory.svg';
import PurchasesIcon from '/resources/assets/icons/purchases.svg';
import PurchaseReturnIcon from '/resources/assets/icons/purchase-return.svg';
import SalesIcon from '/resources/assets/icons/sales.svg';
import SalesReturnIcon from '/resources/assets/icons/sale-return.svg';
import ExpensesIcon from '/resources/assets/icons/expenses.svg';
import PartiesIcon from '/resources/assets/icons/parties.svg';
import UserManagementIcon from '/resources/assets/icons/user-management.svg';
import ReportsIcon from '/resources/assets/icons/reports.svg';
import TaxesIcon from '/resources/assets/icons/taxes.svg';
import SettingsIcon from '/resources/assets/icons/settings.svg';
import CourierIcon from '/resources/assets/icons/courier.svg';

const page = usePage()
const openedSubmenu = ref(null)
const currentUser = ref(null)
const isLoadingUser = ref(true)
const cachedUser = ref(null)

// Cache keys
const USER_CACHE_KEY = 'leafpos_user_data'
const CACHE_EXPIRY_KEY = 'leafpos_user_cache_expiry'
const CACHE_DURATION = 5 * 60 * 1000 // 5 minutes

const loadCachedUser = () => {
    try {
        const cached = localStorage.getItem(USER_CACHE_KEY)
        const expiry = localStorage.getItem(CACHE_EXPIRY_KEY)

        if (cached && expiry && Date.now() < parseInt(expiry)) {
            cachedUser.value = JSON.parse(cached)
            currentUser.value = cachedUser.value
            console.log('Loaded user from cache:', cachedUser.value)
            return true
        }
    } catch (error) {
        console.error('Error loading cached user:', error)
    }
    return false
}

const saveUserToCache = (userData) => {
    try {
        localStorage.setItem(USER_CACHE_KEY, JSON.stringify(userData))
        localStorage.setItem(CACHE_EXPIRY_KEY, (Date.now() + CACHE_DURATION).toString())
    } catch (error) {
        console.error('Error saving user to cache:', error)
    }
}

const clearUserCache = () => {
    localStorage.removeItem(USER_CACHE_KEY)
    localStorage.removeItem(CACHE_EXPIRY_KEY)
}

const fetchCurrentUser = async () => {
    try {
        isLoadingUser.value = true
        const response = await axios.get('/api/users/current')

        // Ambil data dari response.data.data sesuai struktur API
        const userData = response.data.data
        currentUser.value = userData
        cachedUser.value = userData

        saveUserToCache(userData)

        console.log('User data from API:', userData)
        console.log('User role:', userData?.role?.name)
    } catch (error) {
        console.error('Error fetching user data:', error)

        if (cachedUser.value) {
            currentUser.value = cachedUser.value
            console.log('Using cached user data due to API error')
        } else {
            currentUser.value = null
            clearUserCache()
        }
    } finally {
        isLoadingUser.value = false
    }
}

const userRole = computed(() => {
    if (!currentUser.value || !currentUser.value.role) {
        return 'Guest'
    }
    return currentUser.value.role.name || 'Guest'
})

const allNavigationItems = [
    {
        name: 'Dashboard',
        path: '/dashboard',
        icon: DashboardIcon,
        roles: ['Admin', 'Finance', 'Purchasing', 'Sales', 'Inventory']
    },
    {
        name: 'Products',
        path: '/products/all',
        icon: ProductsIcon,
        roles: ['Admin', 'Purchasing'],
        submenu: [
            { name: 'All Products', path: '/products/all' },
            { name: 'Create Products', path: '/products/create' },
            { name: 'Categories', path: '/categories/all' },
            { name: 'Print Barcode', path: '/products/print-barcode' }
        ]
    },
    {
        name: 'Inventory',
        icon: InventoryIcon,
        path: '/stock-opname/all',
        roles: ['Admin', 'Inventory'],
        submenu: [
            { name: 'All Stock Opnames', path: '/stock-opname/all' },
            { name: 'Create Stock Opname', path: '/stock-opname/create' },
            { name: 'Stock Adjustment', path: '/stock-adjustment/' },
            { name: 'Stock Movement', path: '/stock-movement/' },
        ]
    },
    {
        name: 'Purchases',
        icon: PurchasesIcon,
        path: '/purchases/all',
        roles: ['Admin', 'Purchasing'],
        submenu: [
            { name: 'All Purchases', path: '/purchases/all' },
            { name: 'Create Purchase', path: '/purchases/create' },
            { name: 'PO Issuance', path: '/purchases/generate-po-issuance' },
        ]
    },
    {
        name: 'Purchase Return',
        icon: PurchaseReturnIcon,
        path: '/purchase-returns/all',
        roles: ['Admin', 'Purchasing'],
        submenu: [
            { name: 'All Purchase Return', path: '/purchase-returns/all' },
            { name: 'Create Purchase Return', path: '/purchase-returns/create' },
        ]
    },
    {
        name: 'Sales',
        icon: SalesIcon,
        path: '/sales/all',
        roles: ['Admin', 'Sales'],
        submenu: [
            { name: 'All Sales', path: '/sales/all' },
            { name: 'Create Sales', path: '/sales/create' },
        ]
    },
    {
        name: 'Expenses',
        icon: ExpensesIcon,
        path: '/expenses/all',
        roles: ['Admin', 'Finance'],
        submenu: [
            { name: 'All Expenses', path: '/expenses/all' },
            { name: 'Create Expenses', path: '/expenses/create' },
            { name: 'Expense Categories', path: '/expense-categories/all' },
        ]
    },
    {
        name: 'Parties',
        icon: PartiesIcon,
        path: '/customers/all',
        roles: ['Admin', 'Purchasing', 'Sales'],
        submenu: []
    },
    {
        name: 'Couriers',
        icon: CourierIcon,
        path: '/couriers/all',
        roles: ['Admin', 'Sales'],
        submenu: [
            { name: 'All Couriers', path: '/couriers/all' },
            { name: 'Create Courier', path: '/couriers/create' },
        ]
    },
    {
        name: 'User Management',
        path: '/users/all',
        icon: UserManagementIcon,
        roles: ['Admin'],
        submenu: [
            { name: 'All Users', path: '/users/all' },
            { name: 'Create User', path: '/users/create' }
        ]
    },
    {
        name: 'Reports',
        icon: ReportsIcon,
        path: '/reports',
        roles: ['Admin']
    },
    {
        name: 'Taxes',
        path: '/taxes/all',
        icon: TaxesIcon,
        roles: ['Admin', 'Finance'],
        submenu: [
            { name: 'All Taxes', path: '/taxes/all' },
            { name: 'Create Taxes', path: '/taxes/create' },
        ]
    },
    {
        name: 'Settings',
        icon: SettingsIcon,
        path: '/units/all',
        roles: ['Admin'],
        submenu: [
            { name: 'All Units', path: '/units/all' },
        ]
    }
]

const filteredNavigationItems = computed(() => {
    const currentRole = userRole.value

    console.log('Filtering navigation for role:', currentRole)

    if (currentRole === 'Guest') {
        console.log('User is Guest, showing no menu items')
        return []
    }

    const filtered = allNavigationItems.filter(item => {

        const hasAccess = item.roles.includes(currentRole)

        if (!hasAccess) {
            console.log(`Access denied for ${item.name} - User role: ${currentRole}, Required roles:`, item.roles)
            return false
        }

        console.log(`Access granted for ${item.name}`)
        return true
    }).map(item => {

        if (item.name === 'Parties') {
            const currentRole = userRole.value
            const partiesSubmenu = []

            if (currentRole === 'Admin') {
                partiesSubmenu.push(
                    { name: 'Customers', path: '/customers/all' },
                    { name: 'Suppliers', path: '/suppliers/all' }
                )
            } else if (currentRole === 'Purchasing') {
                partiesSubmenu.push(
                    { name: 'Suppliers', path: '/suppliers/all' }
                )

                item.path = '/suppliers/all'
            } else if (currentRole === 'Sales') {
                partiesSubmenu.push(
                    { name: 'Customers', path: '/customers/all' }
                )

                item.path = '/customers/all'
            }

            return {
                ...item,
                submenu: partiesSubmenu
            }
        }

        return item
    })

    console.log('Filtered navigation items:', filtered.map(item => item.name))
    return filtered
})

const isMenuItemActive = (item) => {
    const currentUrl = page.url

    if (item.name === 'Dashboard') {
        return currentUrl === '/dashboard' || currentUrl === '/'
    }

    if (item.name === 'Reports') {
        return currentUrl === '/reports' || currentUrl.startsWith('/reports/')
    }

    if (!item.submenu || item.submenu.length === 0) {
        return currentUrl === item.path || currentUrl.startsWith(item.path + '/')
    }

    return false
}

const toggleSubmenu = (menuName) => {
    openedSubmenu.value = openedSubmenu.value === menuName ? null : menuName
}

const isSubmenuActive = (item) => {
    if (!item.submenu || item.submenu.length === 0) return false

    if (item.name === 'Products') {
        return page.url.startsWith('/products') || page.url.startsWith('/categories')
    }

    if (item.name === 'Expenses') {
        return page.url.startsWith('/expenses') || page.url.startsWith('/expense-categories')
    }

    if (item.name === 'User Management') {
        return page.url.startsWith('/users')
    }

    if (item.name === 'Inventory'){
        return page.url.startsWith('/stock-opname') || page.url.startsWith('/stock-adjustment') || page.url.startsWith('/stock-movement')
    }

    if (item.name === 'Couriers'){
        return page.url.startsWith('/couriers')
    }

    if(item.name === 'Parties') {
        return page.url.startsWith('/customers') || page.url.startsWith('/suppliers')
    }

    if(item.name === 'Taxes') {
        return page.url.startsWith('/taxes')
    }

    if(item.name === 'Purchases') {
        return page.url.startsWith('/purchases')
    }

    if(item.name === 'Purchase Return') {
        return page.url.startsWith('/purchase-returns')
    }

    if(item.name === 'Sales') {
        return page.url.startsWith('/sales')
    }

    if(item.name === 'Settings') {
        return page.url.startsWith('/units')
    }

    return item.submenu.some(subItem => {
        return (
            page.url === subItem.path ||
            page.url.startsWith(subItem.path + '/')
        )
    })
}

const isSubItemActive = (subItem) => {
    const url = page.url;

    // Products submenu
    if (subItem.name === 'All Products') {
        return url.startsWith('/products/') && !url.startsWith('/products/create') && !url.startsWith('/products/print-barcode');
    }

    if (subItem.name === 'Create Products') {
        return url.startsWith('/products/create');
    }

    if(subItem.name === 'Print Barcode'){
        return url.startsWith('/products/print-barcode')
    }

    if (subItem.name === 'Categories') {
        return url.startsWith('/categories/');
    }

    if (subItem.name === 'All Stock Opnames') {
        return url.startsWith('/stock-opname/') && !url.startsWith('/stock-opname/create') && !url.startsWith('/stock-adjustment/') && !url.startsWith('/stock-movement/')
    }

    if (subItem.name === 'Create Stock Opname'){
        return url.startsWith('/stock-opname/create')
    }

    if (subItem.name === 'Stock Adjustment') {
        return url.startsWith('/stock-adjustment')
    }

    if (subItem.name === 'Stock Movement') {
        return url.startsWith('/stock-movement')
    }

    if (subItem.name === 'All Expenses') {
        return url.startsWith('/expenses/') && !url.startsWith('/expenses/create');
    }

    if (subItem.name === 'Create Expenses'){
        return url.startsWith('/expenses/create')
    }

    if (subItem.name === 'Expense Categories') {
        return url.startsWith('/expense-categories/');
    }

    if (subItem.name === 'Customers') {
        return url.startsWith('/customers/');
    }

    if (subItem.name === 'Suppliers') {
        return url.startsWith('/suppliers/');
    }

    // Setting submenu
    if (subItem.name === 'All Units') {
        return url.startsWith('/units/');
    }

    // Users submenu
    if (subItem.name === 'All Users') {
        return url.startsWith('/users/') && !url.startsWith('/users/create');
    }

    if (subItem.name === 'Create User') {
        return url.startsWith('/users/create');
    }

    if(subItem.name === 'Role & Permissions') {
        return url.startsWith('/users/roles');
    }

    // Taxes submenu
    if(subItem.name === 'All Taxes') {
        return url.startsWith('/taxes/') && !url.startsWith('/taxes/create');
    }

    if(subItem.name === 'Create Taxes') {
        return url.startsWith('/taxes/create');
    }

    if(subItem.name === 'All Couriers'){
        return url.startsWith('/couriers/') && !url.startsWith('/couriers/create');
    }

    if(subItem.name === 'Create Courier') {
        return url.startsWith('/couriers/create');
    }

    if(subItem.name === 'All Purchases') {
        return url.startsWith('/purchases/') && !url.startsWith('/purchases/create') && !url.startsWith('/purchases/generate-po-issuance')
            && !url.startsWith('/purchase-returns/') && !url.startsWith('/purchase-returns/create');
    }

    if(subItem.name === 'All Purchase Return') {
        return url.startsWith('/purchase-returns/') && !url.startsWith('/purchase-returns/create');
    }

    if(subItem.name === 'Create Purchase Return') {
        return url.startsWith('/purchase-returns/create');
    }

    if(subItem.name === 'Create Purchase') {
        return url.startsWith('/purchases/create');
    }

    if(subItem.name === 'PO Issuance') {
        return url.startsWith('/purchases/generate-po-issuance');
    }

    if(subItem.name === 'All Sales') {
        return url.startsWith('/sales/') && !url.startsWith('/sales/create');
    }

    if(subItem.name === 'Create Sales') {
        return url.startsWith('/sales/create');
    }

    return url === subItem.path || url.startsWith(subItem.path + '/');
}

watch(() => page.url, (newUrl) => {
    filteredNavigationItems.value.forEach(item => {
        if (item.submenu && item.submenu.length > 0) {
            const isActive = isSubmenuActive(item)
            if (isActive && openedSubmenu.value !== item.name) {
                openedSubmenu.value = item.name
            }
        }
    })
}, { immediate: true })

const initializeComponent = () => {
    const hasCachedUser = loadCachedUser()
    const token = localStorage.getItem('X-API-TOKEN')
    if(!token){
        router.visit('/')
        return
    }
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    fetchCurrentUser()
}

onMounted(() => {
    initializeComponent()
})

let refreshInterval = null
const setupRefreshInterval = () => {
    refreshInterval = setInterval(() => {
        fetchCurrentUser()
    }, 10 * 60 * 1000)
}

onMounted(() => {
    setupRefreshInterval()
})

import { onUnmounted } from 'vue'
onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval)
    }
})
</script>

<style scoped>
/* Animasi untuk submenu */
.submenu-enter-active,
.submenu-leave-active {
    transition: all 0.3s ease;
    overflow: hidden;
}

.submenu-enter-from,
.submenu-leave-to {
    opacity: 0;
    max-height: 0;
}

.submenu-enter-to,
.submenu-leave-from {
    opacity: 1;
    max-height: 500px;
}

/* Scrollbar styling */
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
