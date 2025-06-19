<template>

    <nav class="bg-white shadow-md px-10 py-4 flex justify-between items-center">
        <div class="text-sm text-green-600">
            {{ currentModuleName }}
        </div>
        <div class="flex items-center space-x-4">
            <!-- Notification Dropdown -->
            <div class="relative">
                <button
                    @click="toggleNotificationDropdown"
                    class="notification-button relative flex items-center space-x-2 px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors duration-200"
                    :class="{ 'bg-gray-100': showNotificationDropdown }"
                    aria-haspopup="true"
                    :aria-expanded="showNotificationDropdown ? 'true' : 'false'"
                >
                    <i class="ri-notification-line text-lg"></i>
                    <span>Notifikasi</span>
                    <!-- Badge untuk unread count -->
                    <span
                        v-if="unreadNotificationsCount > 0"
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse"
                    >
                        {{ unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount }}
                    </span>
                </button>

                <!-- Notification Dropdown Content -->
                <div
                    v-if="showNotificationDropdown"
                    ref="notificationDropdownRef"
                    class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-xl z-50 max-h-96 overflow-hidden transform transition-all duration-200 ease-out origin-top-right"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-lg text-gray-800">Notifikasi</h4>
                            <span v-if="unreadNotificationsCount > 0" class="text-sm text-gray-500">
                                {{ unreadNotificationsCount }} belum dibaca
                            </span>
                        </div>
                    </div>

                    <!-- Notification List -->
                    <div class="max-h-80 overflow-y-auto">
                        <ul v-if="notifications.length > 0" class="divide-y divide-gray-100">
                            <li
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="p-4 hover:bg-gray-50 transition-colors duration-150 cursor-pointer"
                                :class="{ 'bg-blue-50 border-l-4 border-blue-400': !notification.read }"
                                @click="markAsRead(notification.id)"
                            >
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                             :class="getNotificationBgColor(notification.type)">
                                            <i :class="[getNotificationIcon(notification.type), 'text-white text-sm']"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ notification.title }}
                                            </p>
                                            <span v-if="!notification.read" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                            {{ notification.message }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-2 flex items-center">
                                            <i class="ri-time-line mr-1"></i>
                                            {{ formatTimeAgo(notification.timestamp) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div v-else class="p-8 text-center">
                            <i class="ri-notification-off-line text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div v-if="notifications.length > 0" class="p-3 border-t border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <button
                                @click="markAllAsRead"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none transition-colors duration-150"
                                :disabled="unreadNotificationsCount === 0"
                                :class="{ 'opacity-50 cursor-not-allowed': unreadNotificationsCount === 0 }"
                            >
                                Tandai Semua Dibaca
                            </button>
                            <button
                                @click="clearAllNotifications"
                                class="text-red-600 hover:text-red-800 text-sm font-medium focus:outline-none transition-colors duration-150"
                            >
                                Hapus Semua
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="relative">
                <button @click="toggleUserDropdown" class="user-dropdown-button flex items-center space-x-2 focus:outline-none">
                    <div v-if="loading" class="w-8 h-8 rounded-md bg-gray-200 animate-pulse"></div>
                    <div v-else class="w-8 h-8 rounded-md bg-green-500 flex items-center justify-center text-white font-medium transition duration-200 hover:bg-green-600">
                        {{ userInitial }}
                    </div>

                    <span class="text-gray-700 font-medium max-w-[120px] truncate flex items-center h-5">
                        <template v-if="loading">
                            <span class="inline-block bg-gray-200 rounded w-24 h-4 animate-pulse" style="height: 20px;"></span>
                        </template>
                        <template v-else>
                            <span class="text-gray-700 font-medium max-w-[120px] truncate">
                                {{ username }}
                            </span>
                        </template>
                    </span>
                </button>

                <div
                    v-if="userDropdownOpen"
                    ref="userDropdownRef"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 transform transition-all duration-200 ease-out"
                >
                    <button @click="profile" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition duration-150 w-full text-left">
                        <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile
                    </button>

                    <button
                        @click="logout"
                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150 text-left"
                    >
                        <svg class="mr-3 h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Log out
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import '@vuepic/vue-datepicker/dist/main.css'
import axios from 'axios'

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

const showNotificationDropdown = ref(false)
const notifications = ref([])
const notificationDropdownRef = ref(null)
const isLoadingNotifications = ref(false)

const user = ref(null)
const loading = ref(true)

const userDropdownOpen = ref(false)
const userDropdownRef = ref(null)

const unreadNotificationsCount = computed(() => {
    return notifications.value.filter(n => !n.read).length
})

const toggleNotificationDropdown = async () => {
    showNotificationDropdown.value = !showNotificationDropdown.value
    if (showNotificationDropdown.value && notifications.value.length === 0) {
        await loadNotifications()
    }
}

const loadNotifications = async () => {
    try {
        isLoadingNotifications.value = true
        const token = localStorage.getItem('X-API-TOKEN')

        const response = await axios.get('/api/notifications', {
            headers: { Authorization: `Bearer ${token}` }
        })

        notifications.value = response.data.data || []
    } catch (error) {
        console.error('Failed to load notifications:', error)
        notifications.value = []
    } finally {
        isLoadingNotifications.value = false
    }
}

const addNotification = (notificationData) => {
    const existingIndex = notifications.value.findIndex(n => n.id === notificationData.id)

    if (existingIndex === -1) {
        notifications.value.unshift({
            id: notificationData.id || Date.now(),
            type: notificationData.type || 'stock-alert',
            title: notificationData.title || 'Notifikasi Baru',
            message: notificationData.message || '',
            read: false,
            timestamp: notificationData.timestamp || Date.now()
        })

        if (notifications.value.length > 50) {
            notifications.value = notifications.value.slice(0, 50)
        }

        showBrowserNotification(notificationData)
    }
}

const markAsRead = async (notificationId) => {
    try {
        const notification = notifications.value.find(n => n.id === notificationId)
        if (notification && !notification.read) {
            notification.read = true

            const token = localStorage.getItem('X-API-TOKEN')
            await axios.patch(`/api/notifications/${notificationId}/read`, {}, {
                headers: { Authorization: `Bearer ${token}` }
            })
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

const markAllAsRead = async () => {
    try {
        const unreadNotifications = notifications.value.filter(n => !n.read)
        if (unreadNotifications.length === 0) return

        notifications.value.forEach(n => n.read = true)

        const token = localStorage.getItem('X-API-TOKEN')
        await axios.patch('/api/notifications/mark-all-read', {}, {
            headers: { Authorization: `Bearer ${token}` }
        })

        console.log('All notifications marked as read')
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

const clearAllNotifications = async () => {
    try {
        const token = localStorage.getItem('X-API-TOKEN')
        await axios.delete('/api/notifications/clear-all', {
            headers: { Authorization: `Bearer ${token}` }
        })

        notifications.value = []
        console.log('All notifications cleared')
    } catch (error) {
        console.error('Failed to clear all notifications:', error)
    }
}

const showBrowserNotification = (notificationData) => {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(notificationData.title, {
            body: notificationData.message,
            icon: '/favicon.ico',
            badge: '/favicon.ico'
        })
    }
}

const requestNotificationPermission = async () => {
    if ('Notification' in window && Notification.permission === 'default') {
        await Notification.requestPermission()
    }
}

const getNotificationIcon = (type) => {
    const icons = {
        'stock-alert': 'ri-alert-line',
        'low-stock': 'ri-error-warning-line',
        'new-order': 'ri-shopping-cart-line',
        'order-completed': 'ri-checkbox-circle-line',
        'payment-received': 'ri-money-dollar-circle-line',
        'user-activity': 'ri-user-line',
        'system': 'ri-settings-line',
        'default': 'ri-notification-line'
    }
    return icons[type] || icons.default
}

const getNotificationBgColor = (type) => {
    const colors = {
        'stock-alert': 'bg-red-500',
        'low-stock': 'bg-orange-500',
        'new-order': 'bg-blue-500',
        'order-completed': 'bg-green-500',
        'payment-received': 'bg-purple-500',
        'user-activity': 'bg-indigo-500',
        'system': 'bg-gray-500',
        'default': 'bg-gray-500'
    }
    return colors[type] || colors.default
}

const formatTimeAgo = (timestamp) => {
    const now = Date.now()
    const diff = Math.floor((now - timestamp) / 1000)

    if (diff < 60) return `${diff} detik yang lalu`

    const minutes = Math.floor(diff / 60)
    if (minutes < 60) return `${minutes} menit yang lalu`

    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `${hours} jam yang lalu`

    const days = Math.floor(hours / 24)
    if (days < 7) return `${days} hari yang lalu`

    const weeks = Math.floor(days / 7)
    return `${weeks} minggu yang lalu`
}
const fetchUser = async () => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    try {
        const response = await axios.get('/api/users/current', {
            headers: { Authorization: `Bearer ${token}` }
        })
        user.value = response.data.data
    } catch (error) {
        console.error('Failed to fetch user:', error)
        router.visit('/')
    } finally {
        loading.value = false
    }
}

const userInitial = computed(() => {
    if (loading.value) return ''
    return user.value?.name?.charAt(0).toUpperCase() || 'G'
})

const username = computed(() => {
    if (loading.value) return 'Loading...'
    return user.value?.name || 'Guest'
})

const toggleUserDropdown = () => {
    userDropdownOpen.value = !userDropdownOpen.value
}

const handleClickOutside = (event) => {
    if (notificationDropdownRef.value &&
        !notificationDropdownRef.value.contains(event.target) &&
        !event.target.closest('.notification-button')) {
        showNotificationDropdown.value = false
    }

    if (userDropdownRef.value &&
        !userDropdownRef.value.contains(event.target) &&
        !event.target.closest('.user-dropdown-button')) {
        userDropdownOpen.value = false
    }
}

const setupEcho = () => {
    if (!window.Echo) {
        console.warn('Laravel Echo not initialized. Make sure bootstrap.js is loaded.')
        return
    }

    try {
        window.Echo.channel('stock.alert')
            .listen('.stock-alert', (e) => {
                console.log('Stock Alert received via Echo:', e)
                addNotification({
                    id: e.product?.id || Date.now(),
                    type: 'stock-alert',
                    title: 'Stok Rendah!',
                    message: `Produk ${e.product?.name} memiliki stok ${e.product?.stock} (batas: ${e.product?.stock_alert})`,
                    timestamp: Date.now()
                })
            })
            .error((error) => {
                console.error('Pusher Channel Error for stock.alert:', error)
            })

        window.Echo.channel(`notifications.${user.value?.id}`)
            .listen('.notification', (e) => {
                console.log('General notification received:', e)
                addNotification({
                    id: e.id || Date.now(),
                    type: e.type || 'default',
                    title: e.title || 'Notifikasi Baru',
                    message: e.message || '',
                    timestamp: e.timestamp || Date.now()
                })
            })
            .error((error) => {
                console.error('Pusher Channel Error for notifications:', error)
            })

    } catch (error) {
        console.error('Error setting up Echo listeners:', error)
    }
}

const dateValue = ref([new Date(), new Date()])
const datePickerOpen = ref(false)

const locale = {
    locale: 'id',
    format: 'DD MMM'
}

const format = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

const formattedRange = computed(() => {
    if (!dateValue.value || !Array.isArray(dateValue.value)) return ''
    const [start, end] = dateValue.value
    return `${format(start)} ~ ${format(end)}`
})

const page = usePage()
const currentModuleName = computed(() => {
    const currentPath = page.url
    const navigationItems = [
        {
            name: 'Dashboard',
            path: '/dashboard',
            icon: DashboardIcon
        },
        {
            name: 'Products',
            path: '/products',
            icon: ProductsIcon,
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
                    path: '/categories/all'
                },
                {
                    name: 'Restore Categories',
                    path: '/categories/restore'
                },
                {
                    name: 'Restore Products',
                    path: '/products/restore'
                },
                {
                    name: 'Print Barcode',
                    path: '/products/print-barcode'
                },
                {
                    name: 'Product Detail',
                    path: '/products/:id',
                    dynamic: true
                }
            ]
        },
        {
            name: 'Inventory',
            icon: InventoryIcon,
            submenu: [
                {
                    name: 'All Stock Opnames',
                    path: '/stock-opname/all'
                },
                {
                    name: 'Stock Opname Detail',
                    path: '/stock-opname/:id',
                    dynamic: true
                },
                {
                    name: 'Create Stock Opname',
                    path: '/stock-opname/create'
                },
                {
                    name: 'Stock Adjustment',
                    path: '/stock-adjustment'
                },
                {
                    name: 'Stock Movement',
                    path: '/stock-movement'
                }
            ]
        },
        {
            name: 'Purchases',
            icon: PurchasesIcon,
            submenu: [
                {
                    name: 'All Purchases',
                    path: '/purchases/all'
                },
                {
                    name: 'Create Purchase',
                    path: '/purchases/create'
                },
                {
                    name: 'Purchase Detail',
                    path: '/purchases/:id',
                    dynamic: true
                },
                {
                    name: 'PO Issuance',
                    path: '/purchases/generate-po-issuance'
                }
            ]
        },
        {
            name: 'Purchase Return',
            icon: PurchaseReturnIcon,
            submenu: [
                {
                    name: 'All Purchase Returns',
                    path: '/purchase-returns/all'
                },
                {
                    name: 'Create Purchase Return',
                    path: '/purchase-returns/create'
                },
                {
                    name: 'Purchase Return Detail',
                    path: '/purchase-returns/:id',
                    dynamic: true
                },
                {
                    name: 'Restore Purchase Return',
                    path: '/purchase-returns/restore'
                }
            ]
        },
        {
            name: 'Sales',
            icon: SalesIcon,
            submenu: [
                {
                    name: 'All Sales',
                    path: '/sales/all'
                },
                {
                    name: 'Create Sales',
                    path: '/sales/create'
                },
                {
                    name: 'Sales Detail',
                    path: '/sales/:id',
                    dynamic: true
                },
                {
                    name: 'Restore Sales',
                    path: '/sales/restore'
                }
            ]
        },
        // {
        //     name: 'Sales Return',
        //     icon: SalesReturnIcon,
        // },
        {
            name: 'Expenses',
            icon: ExpensesIcon,
            submenu: [
                {
                    name: 'All Expenses',
                    path: '/expenses/all'
                },
                {
                    name: 'Create Expenses',
                    path: '/expenses/create'
                },
                {
                    name: 'Expense Categories',
                    path: '/expense-categories/all'
                },
                {
                    name: 'Restore Expense Categories',
                    path: '/expense-categories/restore'
                },
                {
                    name: 'Expense Detail',
                    path: '/expenses/:id',
                    dynamic: true
                }
            ]
        },
        {
            name: 'Parties',
            icon: PartiesIcon,
            submenu: [
                {
                    name: 'All Customers',
                    path: '/customers/all'
                },
                {
                    name: 'Customer Detail',
                    path: '/customers/:id',
                    dynamic: true
                },
                {
                    name: 'All Suppliers',
                    path: '/suppliers/all'
                },
                {
                    name: 'Supplier Detail',
                    path: '/suppliers/:id',
                    dynamic: true
                }
            ]
        },
        {
            name: 'Couriers',
            icon: CourierIcon,
            submenu: [
                {
                    name: 'All Couriers',
                    path: '/couriers/all'
                },
                {
                    name: 'Create Courier',
                    path: '/couriers/create'
                },
                {
                    name: 'Courier Detail',
                    path: '/couriers/:id',
                    dynamic: true
                }
            ]
        },
        {
            name: 'User Management',
            icon: UserManagementIcon,
            submenu: [
                {
                    name: 'All Users',
                    path: '/users/all'
                },
                {
                    name: 'Create User',
                    path: '/users/create'
                },
                {
                    name: 'User Detail',
                    path: '/users/:id',
                    dynamic: true
                }
            ]
        },
        {
            name: 'Reports',
            icon: ReportsIcon,
            path: '/reports'
        },
        {
            name: 'Taxes',
            icon: TaxesIcon,
            submenu: [
                {
                    name: 'All Taxes',
                    path: '/taxes/all'
                },
                {
                    name: 'Create Tax',
                    path: '/taxes/create'
                },
                {
                    name: 'Tax Detail',
                    path: '/taxes/:id',
                    dynamic: true
                }
            ]
        },
        {
            name: 'Settings',
            icon: SettingsIcon,
            submenu: [
                {
                    name: 'All Units',
                    path: '/units/all'
                },
                {
                    name: 'Restore Units',
                    path: '/units/restore'
                }
            ]
        }
    ]


    // Helper function to match dynamic paths
    const matchDynamicPath = (currentPath, pathPattern) => {
        const regex = new RegExp(`^${pathPattern.replace(':id', '\\d+')}$`);
        return regex.test(currentPath);
    };

    // Specific path mappings (prioritize these)
    if (currentPath === '/taxes/restore') return 'Taxes / All Taxes / Restore Taxes';
    if (currentPath === '/couriers/restore') return 'Couriers / All Couriers / Restore Couriers';
    if (currentPath === '/products/restore') return 'Products / All Products / Restore Products';
    if (currentPath === '/products/print-barcode') return 'Products / Print Barcode';
    if (currentPath === '/units/all') return 'Settings / All Units';
    if (currentPath === '/units/restore') return 'Settings / All Units / Restore Units';
    if (currentPath === '/expenses/restore') return 'Expenses / All Expenses / Restore Expenses';
    if (currentPath === '/purchases/all') return 'Purchases / All Purchases';
    if (currentPath === '/purchases/create') return 'Purchases / Create Purchase';
    if (currentPath === '/purchases/restore') return 'Purchases / All Purchases / Restore Purchases';
    if (currentPath === '/purchase-returns/all') return 'Purchase Return / All Purchase Returns';
    if (currentPath === '/purchase-returns/create') return 'Purchase Return / Create Purchase Return';
    if (currentPath === '/purchase-returns/restore') return 'Purchase Return / All Purchase Returns / Restore Purchase Returns';
    if (currentPath === '/expense-categories/all') return 'Expenses / Expense Categories';
    if (currentPath === '/expense-categories/restore') return 'Expenses / Expense Categories / Restore Expense Categories';
    if (currentPath === '/categories/all') return 'Products / Categories';
    if (currentPath === '/categories/restore') return 'Products / Categories / Restore Categories';
    if (currentPath === '/sales/restore') return 'Sales / All Sales / Restore Sales';
    if (currentPath === '/suppliers/all') return 'Parties / Suppliers';
    if (currentPath === '/suppliers/create') return 'Parties / Suppliers / Create Suppliers';
    if (currentPath === '/suppliers/restore') return 'Parties / Suppliers / Restore Suppliers';
    if (currentPath === '/customers/all') return 'Parties / Customers';
    if (currentPath === '/customers/create') return 'Parties / Customers / Create Customers';
    if (currentPath === '/customers/restore') return 'Parties / Customers / Restore Customers';
    if (currentPath === '/stock-opname/restore') return 'Inventory / All Stock Opnames / Restore Stock Opnames';
    if (currentPath === '/stock-adjustment') return 'Inventory / Stock Adjustment';
    if (currentPath === '/stock-movement') return 'Inventory / Stock Movement';
    if (currentPath === '/users/inactive') return 'User Management / All Users / Inactive Users';
    if (currentPath === '/users/restore') return 'User Management / All Users / Restore Users';


    // Dynamic path handling with referrer logic
    const handleDynamicPath = (pathPattern, referrerKey, baseName, dynamicName, restoreName) => {
        if (matchDynamicPath(currentPath, pathPattern)) {
            const referrer = localStorage.getItem(referrerKey) || baseName;
            return referrer.includes('restore') ? `${restoreName} / ${dynamicName}` : `${baseName} / ${dynamicName}`;
        }
        return null;
    };

    let moduleName = null;

    moduleName = handleDynamicPath('/categories/:id', 'categoryReferrer', 'Products / Categories', 'Category Detail', 'Products / Categories / Restore Categories');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/stock-opname/:id', 'inventoryReferrer', 'Inventory / All Stock Opnames', 'Stock Opname Detail', 'Inventory / All Stock Opnames / Restore Stock Opnames');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/sales/:id', 'saleReferrer', 'Sales / All Sales', 'Sales Detail', 'Sales / All Sales / Restore Sales');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/purchases/:id', 'purchaseReferrer', 'Purchases / All Purchases', 'Purchase Detail', 'Purchases / All Purchases / Restore Purchases');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/purchase-returns/:id', 'purchaseReturnReferrer', 'Purchase Return / All Purchase Returns', 'Purchase Return Detail', 'Purchase Return / All Purchase Returns / Restore Purchase Returns');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/expense-categories/:id', 'expenseCategoryReferrer', 'Expenses / Expense Categories', 'Expense Category Detail', 'Expenses / Expense Categories / Restore Expense Categories');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/expenses/:id', 'expenseReferrer', 'Expenses / All Expenses', 'Expense Detail', 'Expenses / All Expenses / Restore Expense');
    if (moduleName) return moduleName;


    moduleName = handleDynamicPath('/units/:id', 'unitReferrer', 'Settings / All Units', 'Unit Detail', 'Settings / All Units / Restore Units');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/taxes/:id', 'taxReferrer', 'Taxes / All Taxes', 'Tax Detail', 'Taxes / All Taxes / Restore Taxes');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/couriers/:id', 'courierReferrer', 'Couriers / All Couriers', 'Courier Detail', 'Couriers / All Couriers / Restore Couriers');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/products/:id', 'productReferrer', 'Products / All Products', 'Product Detail', 'Products / All Products / Restore Products');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/customers/:id', 'customerReferrer', 'Parties / Customers', 'Customer Detail', 'Parties / Customers / Restore Customers');
    if (moduleName) return moduleName;

    moduleName = handleDynamicPath('/suppliers/:id', 'supplierReferrer', 'Parties / Suppliers', 'Supplier Detail', 'Parties / Suppliers / Restore Suppliers');
    if (moduleName) return moduleName;

    if(currentPath.match(/^\/profile\/\d+$/)) {
        return 'User Profile'
    }

    // Check for exact path matches
    for (const item of navigationItems) {
        if (currentPath === item.path) return item.name
        if (item.submenu) {
            // Check for exact submenu matches
            const subItem = item.submenu.find(sub => currentPath === sub.path)
            if (subItem) return `${item.name} / ${subItem.name}`

            // Check for dynamic submenu matches within submenu (already covered by handleDynamicPath, but keeping for robustness)
            for (const sub of item.submenu) {
                if (sub.dynamic && sub.path) {
                    if (matchDynamicPath(currentPath, sub.path)) {
                        return `${item.name} / ${sub.name}`;
                    }
                }
            }
        }
    }
    return 'Dashboard'
})

const logout = async () => {
    try {
        await axios.post('/api/users/logout', {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('X-API-TOKEN')}` }
        })
    } catch (err) {
        console.error('Logout error:', err)
    } finally {
        localStorage.removeItem('X-API-TOKEN')
        delete axios.defaults.headers.common['Authorization']

        window.location.href = '/'
    }
}




const profile = async () => {
    try {
        const response = await axios.get('/api/users/current', {
            headers: { Authorization: `Bearer ${localStorage.getItem('X-API-TOKEN')}` }
        })
        const userId = response.data.data.id
        router.visit(`/profile/${userId}`)
    } catch (error) {
        console.error('Error get current user: ', error)
    }
}

// --- Lifecycle Hooks ---
onMounted(async () => {
    await fetchUser()
    await requestNotificationPermission()

    // Setup event listeners
    document.addEventListener('click', handleClickOutside)

    await nextTick()
    setupEcho()


    // Load initial notifications
    await loadNotifications()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)

    // Clean up Echo listeners
    if (window.Echo) {
        window.Echo.leave('stock.alert')
        if (user.value?.id) {
            window.Echo.leave(`notifications.${user.value.id}`)
        }
    }
})
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.max-h-80::-webkit-scrollbar {
    width: 4px;
}

.max-h-80::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.max-h-80::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.max-h-80::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
