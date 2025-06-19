<template>
    <div class="flex">
        <div class="fixed inset-y-0 left-0 w-66 bg-gradient-to-b from-lp-green to-lp-green text-white shadow-lg flex flex-col">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <img src="/images/leafPos1.svg" alt="Leaf POS Logo" class="w-8 h-8" />
                    <h3 class="text-xl font-semibold text-white">Leaf POS</h3>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto p-4">
                <ul class="space-y-2">
                    <li v-for="item in navigationItems" :key="item.name">
                        <!-- Link biasa -->
                        <Link
                            v-if="!item.submenu"
                            :href="item.path"
                            class="flex items-center px-4 py-3 rounded-lg transition-colors hover:bg-white/10"
                            :class="{ 'bg-white/20': $page.url === item.path }"
                        >
                            <component :is="item.icon" class="mr-3" />
                            <span>{{ item.name }}</span>
                        </Link>

                        <!-- Submenu -->
                        <div v-else>
                            <Link
                                :href="item.path"
                                @click.prevent="toggleSubmenu(item.name)"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors hover:bg-white/10"
                                :class="{ 'bg-white/20': openedSubmenu === item.name || isSubmenuActive(item) }"
                            >
                                <div class="flex items-center">
                                    <component :is="item.icon" class="mr-3" />
                                    <span>{{ item.name }}</span>
                                </div>
                                <i :class="[
                                    'ri-arrow-right-s-line text-xl transition-transform duration-200',
                                    { 'rotate-90': openedSubmenu === item.name || isSubmenuActive(item) }
                                ]"></i>
                            </Link>

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
                                    class="mt-1 ml-4 space-y-1"
                                >
                                    <li v-for="subItem in item.submenu" :key="subItem.path">
                                        <Link
                                            :href="subItem.path"
                                            class="flex items-center px-4 py-2 text-sm rounded-lg transition-colors hover:bg-white/10"
                                        >
                                            <div class="flex items-center px-[10px] w-full min-w-0 space-x-2">
                                            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                     :class="{
                                                      'fill-white': isSubItemActive(subItem),
                                                      'fill-white/20': !isSubItemActive(subItem)
                                                    }"
                                                >
                                                    <circle cx="7" cy="7.02393" r="7" />
                                                </svg>
                                                <span class="truncate text-sm block">
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

        <!-- Konten utama -->
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
import { ref, onMounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
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
const activeSubmenu = ref(null)

const navigationItems = [
    {
        name: 'Dashboard',
        path: '/dashboard',
        icon: DashboardIcon
    },
    {
        name: 'Products',
        path: '/products/all',
        icon: ProductsIcon,
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
        submenu: [
            { name: 'All Purchase Return', path: '/purchase-returns/all' },
            { name: 'Create Purchase Return', path: '/purchase-returns/create' },
        ]
    },
    {
        name: 'Sales',
        icon: SalesIcon,
        path: '/sales/all',
        submenu: [
            { name: 'All Sales', path: '/sales/all' },
            { name: 'Create Sales', path: '/sales/create' },
        ]
    },
    // { name: 'Sales Return', icon: SalesReturnIcon },
    {
        name: 'Expenses',
        icon: ExpensesIcon,
        path: '/expenses/all',
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
        submenu: [
            { name: 'Customers', path: '/customers/all' },
            { name: 'Suppliers', path: '/suppliers/all' },
        ]
    },
    {
        name: 'Couriers',
        icon: CourierIcon,
        path: '/couriers/all',
        submenu: [
            { name: 'All Couriers', path: '/couriers/all' },
            { name: 'Create Courier', path: '/couriers/create' },
        ]
    },
    {
        name: 'User Management',
        path: '/users/all',
        icon: UserManagementIcon,
        submenu: [
            { name: 'All Users', path: '/users/all' },
            { name: 'Create User', path: '/users/create' },
            { name: 'Role & Permissions', path: '/users/roles' }
        ]
    },
    {
        name: 'Reports',
        icon: ReportsIcon,
        path: '/reports'
    },
    {
        name: 'Taxes',
        path: '/taxes/all',
        icon: TaxesIcon,
        submenu: [
            { name: 'All Taxes', path: '/taxes/all' },
            { name: 'Create Taxes', path: '/taxes/create' },
        ]
    },
    {
        name: 'Settings',
        icon: SettingsIcon,
        path: '/units/all',
        submenu: [
            { name: 'All Units', path: '/units/all' },
        ]
    }
]
const toggleSubmenu = (menuName) => {
    openedSubmenu.value = openedSubmenu.value === menuName ? null : menuName
}
const isSubmenuActive = (item) => {
    if (!item.submenu) return false
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

    if (subItem.name === 'Expense Create'){
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
    if (subItem.name === 'Role & Permissions') {
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

    // Purchases submenu
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

    // Fallback match
    return url === subItem.path || url.startsWith(subItem.path + '/');
}



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

/* Animasi untuk ikon */
.arrow-icon {
    transition: transform 0.2s ease;
}
.arrow-icon.rotated {
    transform: rotate(90deg);
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
