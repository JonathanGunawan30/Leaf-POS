<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <!-- Search and Filter Header -->
                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Product"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="goToAllProducts"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Products</p>
                        </button>
                        <select
                            v-model="selectedCategory"
                            @change="handleCategoryChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none"
                        >
                            <option value="" class="text-lp-green">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <button
                            @click="showFilterModal = true"
                            class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 relative"
                        >
                            <i class="ri-filter-3-line"></i>
                            Filter
                            <span
                                v-if="isFilterActive"
                                class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"
                                title="Filters are active"
                            ></span>
                        </button>
                    </div>
                </div>

                <!-- Price Filter Modal -->
                <div v-show="showFilterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out" :class="{ 'opacity-0': !showFilterModal, 'opacity-100': showFilterModal }">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg transition-transform duration-300 ease-in-out transform" :class="{ 'scale-90 opacity-0': !showFilterModal, 'scale-100 opacity-100': showFilterModal }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Filter Products</h3>
                            <button @click="showFilterModal = false" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Selling Price Range -->
                            <div>
                                <h4 class="font-medium mb-2">Selling Price Range</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Min</label>
                                        <input
                                            v-model="minSellingPrice"
                                            type="number"
                                            min="0"
                                            placeholder="Min price"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Max</label>
                                        <input
                                            v-model="maxSellingPrice"
                                            type="number"
                                            min="0"
                                            placeholder="Max price"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Purchase Price Range -->
                            <div>
                                <h4 class="font-medium mb-2">Purchase Price Range</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Min</label>
                                        <input
                                            v-model="minPurchasePrice"
                                            type="number"
                                            min="0"
                                            placeholder="Min price"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Max</label>
                                        <input
                                            v-model="maxPurchasePrice"
                                            type="number"
                                            min="0"
                                            placeholder="Max price"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="resetFilters"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Reset
                            </button>
                            <button
                                @click="applyFilters"
                                class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <div v-if="isFilterActive" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Filters:</div>

                    <div v-if="minSellingPrice" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Min Selling: {{ formatPrice(minSellingPrice) }}</span>
                        <button @click="clearFilter('minSellingPrice')" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="maxSellingPrice" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Max Selling: {{ formatPrice(maxSellingPrice) }}</span>
                        <button @click="clearFilter('maxSellingPrice')" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="minPurchasePrice" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Min Purchase: {{ formatPrice(minPurchasePrice) }}</span>
                        <button @click="clearFilter('minPurchasePrice')" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="maxPurchasePrice" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Max Purchase: {{ formatPrice(maxPurchasePrice) }}</span>
                        <button @click="clearFilter('maxPurchasePrice')" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <button
                        @click="resetFilters"
                        class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm hover:bg-red-200"
                    >
                        Clear All Filters
                    </button>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                            <th class="px-4 py-3 text-left font-medium">CATEGORY</th>
                            <th class="px-4 py-3 text-left font-medium">STOCK</th>
                            <th class="px-4 py-3 text-left font-medium">BARCODE</th>
                            <th class="px-4 py-3 text-left font-medium">SELLING PRICE</th>
                            <th class="px-4 py-3 text-left font-medium">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Skeleton Rows -->
                        <tr v-for="n in 5" :key="n" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="w-12 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-24 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-26 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                </div>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else-if="products.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Products</h3>
                    <p class="text-gray-500">There are no deleted products to restore.</p>
                </div>

                <!-- Products Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                            <th class="px-4 py-3 text-left font-medium">CATEGORY</th>
                            <th class="px-4 py-3 text-left font-medium">STOCK</th>
                            <th class="px-4 py-3 text-left font-medium">BARCODE</th>
                            <th class="px-4 py-3 text-left font-medium">SELLING PRICE</th>
                            <th class="px-4 py-3 text-left font-medium">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in products" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img :src="item.images"
                                         alt="Product"
                                         class="w-12 h-12 object-cover rounded"
                                    >
                                    <span>{{ item.name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ item.category?.name || 'N/A' }}</td>
                            <td class="px-4 py-3">{{ item.stock }}</td>
                            <td class="px-4 py-3">
                                <img v-if="item.barcode_images"
                                     :src="`${item.barcode_images}`"
                                     alt="Barcode"
                                     class="h-8"
                                >
                            </td>
                            <td class="px-4 py-3">{{ formatPrice(item.selling_price) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <!-- Button Restore -->
                                    <button
                                        @click="restoreProduct(item.id)"
                                        class="p-2 bg-lp-green text-white rounded-md hover:bg-green-600 transition-colors"
                                        title="Restore Product"
                                    >
                                        <RestoreIcon class="w-4 h-4 text-white" />
                                    </button>

                                    <!-- Button Hard Delete -->
                                    <button
                                        @click="hardDeleteProduct(item.id, item.name)"
                                        class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors"
                                        title="Permanently Delete Product"
                                    >
                                        <TrashIcon class="w-4 h-4 text-white" />
                                    </button>

                                    <!-- Button Info -->
                                    <button
                                        @click="goToDetail(item.id)"
                                        class="p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors flex items-center justify-center"
                                        title="View Product Details"
                                    >
                                        <InfoIcon class="w-4 h-4 text-white" />
                                    </button>

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.length > 0" class="flex justify-between items-center mt-4">
                    <span class="text-sm text-gray-600">
                      Showing {{ isNaN(from) ? 0 : from }} - {{ isNaN(to) ? 0 : to }} of {{ totalResults ?? 0 }} results
                    </span>

                    <div class="flex items-center gap-2">
                        <!-- Previous -->
                        <button
                            @click="handlePageChange(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="p-2 border rounded-lg hover:bg-gray-50"
                            :class="{ 'cursor-not-allowed opacity-50': currentPage === 1 }"
                        >
                            <i class="ri-arrow-left-s-line"></i>
                        </button>

                        <!-- Page Numbers -->
                        <div class="flex items-center">
                            <button
                                v-for="page in displayedPages"
                                :key="page"
                                @click="typeof page === 'number' ? handlePageChange(page) : null"
                                :class="[
                                    'px-3 py-1 rounded-lg mx-0.5',
                                    typeof page === 'number' ? (
                                        currentPage === page ?
                                        'bg-lp-green text-white' :
                                        'border hover:bg-gray-50'
                                    ) : '',
                                    typeof page === 'string' ? 'border-none cursor-default' : ''
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <!-- Next -->
                        <button
                            @click="handlePageChange(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="p-2 border rounded-lg hover:bg-gray-50"
                            :class="{ 'cursor-not-allowed opacity-50': currentPage === totalPages }"
                        >
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import Sidebar from '../Components/Sidebar.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import RestoreIcon from '/resources/assets/icons/restore-white.svg'
import TrashIcon from '/resources/assets/icons/trash.svg'
import InfoIcon from '/resources/assets/icons/info.svg'
import Swal from 'sweetalert2'

const page = usePage()
const auth = page.props.auth
const products = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('')
const categories = ref([])
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

// Price filter variables
const minSellingPrice = ref('')
const maxSellingPrice = ref('')
const minPurchasePrice = ref('')
const maxPurchasePrice = ref('')
const showFilterModal = ref(false)

// Add debounce functionality
const searchTimeout = ref(null)

// Save filter state to localStorage
const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedCategory: selectedCategory.value,
        minSellingPrice: minSellingPrice.value,
        maxSellingPrice: maxSellingPrice.value,
        minPurchasePrice: minPurchasePrice.value,
        maxPurchasePrice: maxPurchasePrice.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('restoreProductFilterState', JSON.stringify(filterState))
}

// Restore filter state from localStorage
const restoreFilterState = () => {
    const savedState = localStorage.getItem('restoreProductFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedCategory.value = filterState.selectedCategory || ''
            minSellingPrice.value = filterState.minSellingPrice || ''
            maxSellingPrice.value = filterState.maxSellingPrice || ''
            minPurchasePrice.value = filterState.minPurchasePrice || ''
            maxPurchasePrice.value = filterState.maxPurchasePrice || ''
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

// Function to go back to all products page
const goToAllProducts = () => {
    router.visit('/products/all')
}

// Function to go to the product detail page
const goToDetail = (id) => {
    // Save filter state before navigation
    saveFilterState()
    // Explicitly set the referrer to restore product page
    localStorage.setItem('productReferrer', '/products/restore')
    router.visit(`/products/${id}`)
}

// Function to hard delete a product
const hardDeleteProduct = async (id, productName) => {
    // Show confirmation dialog with clear warning
    const result = await Swal.fire({
        title: 'Permanently Delete Product',
        html: `<div class="text-left">
            <p class="mb-2">Are you sure you want to <strong>permanently delete</strong> <strong>${productName}</strong>?</p>
            <p class="text-red-600 font-bold">Warning: This action cannot be undone!</p>
            <p>The product will be permanently removed from the system and cannot be recovered.</p>
        </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete permanently!',
        cancelButtonText: 'Cancel'
    })

    // If user confirms deletion
    if (result.isConfirmed) {
        try {
            loading.value = true
            // Make API call to hard delete the product
            await axios.delete(`/api/products/${id}/force`)

            // Show success message
            Swal.fire({
                title: 'Permanently Deleted!',
                text: `${productName} has been permanently deleted.`,
                icon: 'success',
                confirmButtonText: 'OK'
            })

            // Refresh the product list
            fetchProducts()
        } catch (error) {
            console.error('Error hard deleting product:', error)

            // Show error message
            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || 'Failed to delete product permanently.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
        } finally {
            loading.value = false
        }
    }
}

// Function to restore a product
const restoreProduct = async (id) => {
    try {
        loading.value = true
        await axios.patch(`/api/products/${id}/restore`)

        Swal.fire({
            title: 'Success!',
            text: 'Product has been restored successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        })

        // Refresh the product list
        fetchProducts()
    } catch (error) {
        console.error('Error restoring product:', error)

        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to restore product.',
            icon: 'error',
            confirmButtonText: 'OK'
        })
    } finally {
        loading.value = false
    }
}

// Function to fetch categories from the API
const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories')
        categories.value = response.data.data
    } catch (error) {
        console.error('Error fetching categories:', error)
    }
}

// Function to fetch trashed products from the API
const fetchProducts = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/products/trashed', {
            params: {
                page: currentPage.value,
                search: searchQuery.value.trim(), // Trim whitespace
                category_id: selectedCategory.value,
                per_page: perPage.value,
                min_selling: minSellingPrice.value || null,
                max_selling: maxSellingPrice.value || null,
                min_purchase: minPurchasePrice.value || null,
                max_purchase: maxPurchasePrice.value || null
            }
        })
        const data = response.data
        products.value = data.data

        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page
    } catch (error) {
        if (error.response) {
            // Server merespons dengan status selain 2xx
            console.error('API Error:', {
                status: error.response.status,
                data: error.response.data,
                headers: error.response.headers
            })

            // Handle 401 Unauthorized (Token expired or invalid)
            if (error.response.status === 401) {
                localStorage.removeItem('X-API-TOKEN') // Remove the invalid token
                router.visit('/') // Redirect to login page
            }
        } else if (error.request) {
            // Tidak ada respons dari server
            console.error('No response received:', error.request)
        } else {
            // Kesalahan lain pada konfigurasi axios
            console.error('Error setting up request:', error.message)
        }
    } finally {
        loading.value = false
    }
}

// Handle search with debounce
const handleSearch = () => {
    // Clear any existing timeout
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }

    // Set a new timeout
    searchTimeout.value = setTimeout(() => {
        currentPage.value = 1
        fetchProducts()
    }, 300) // 300ms debounce delay
}

// Watch for changes to the search query
watch(searchQuery, (newValue) => {
    // If search is cleared (becomes empty), refresh products immediately
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchProducts()
    } else {
        handleSearch()
    }
})

// Watch for negative values in price filters and prevent them
watch(minSellingPrice, (newValue) => {
    if (newValue < 0) {
        minSellingPrice.value = 0
    }
})

watch(maxSellingPrice, (newValue) => {
    if (newValue < 0) {
        maxSellingPrice.value = 0
    }
})

watch(minPurchasePrice, (newValue) => {
    if (newValue < 0) {
        minPurchasePrice.value = 0
    }
})

watch(maxPurchasePrice, (newValue) => {
    if (newValue < 0) {
        maxPurchasePrice.value = 0
    }
})

// Handle category selection change
const handleCategoryChange = () => {
    currentPage.value = 1
    fetchProducts()
}

// Apply price filters
const applyFilters = () => {
    currentPage.value = 1
    fetchProducts()
    showFilterModal.value = false
}

// Clear filter state from localStorage
const clearFilterState = () => {
    localStorage.removeItem('restoreProductFilterState')
}

// Reset price filters
const resetFilters = () => {
    minSellingPrice.value = ''
    maxSellingPrice.value = ''
    minPurchasePrice.value = ''
    maxPurchasePrice.value = ''
    currentPage.value = 1
    clearFilterState() // Clear localStorage
    fetchProducts()
    showFilterModal.value = false
}

// Clear individual filter
const clearFilter = (filterName) => {
    if (filterName === 'minSellingPrice') minSellingPrice.value = ''
    if (filterName === 'maxSellingPrice') maxSellingPrice.value = ''
    if (filterName === 'minPurchasePrice') minPurchasePrice.value = ''
    if (filterName === 'maxPurchasePrice') maxPurchasePrice.value = ''
    currentPage.value = 1
    saveFilterState() // Update localStorage with cleared filter
    fetchProducts()
}

// Function to format price
const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price)
}

// On component mount, fetch the products and categories
onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')  // Redirect to login if no token exists
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}` // Set Authorization header for axios

    // First restore filter state if coming back from product details
    restoreFilterState()

    // Then fetch categories and products with the restored filters
    fetchCategories()
    fetchProducts()
})

// Computed properties for pagination
const totalPages = computed(() => Math.ceil(totalResults.value / perPage.value))
const from = computed(() => ((currentPage.value - 1) * perPage.value) + 1)
const to = computed(() => Math.min(currentPage.value * perPage.value, totalResults.value))

// Check if any filters are active
const isFilterActive = computed(() => {
    return minSellingPrice.value !== '' ||
           maxSellingPrice.value !== '' ||
           minPurchasePrice.value !== '' ||
           maxPurchasePrice.value !== ''
})

// Compute page numbers to display
const displayedPages = computed(() => {
    const pages = []
    const maxVisiblePages = 5 // Number of pages to display at once
    const totalPagesCount = Math.ceil(totalResults.value / perPage.value)

    if (totalPagesCount <= maxVisiblePages) {
        // Show all pages if total pages is less than max visible pages
        for (let i = 1; i <= totalPagesCount; i++) {
            pages.push(i)
        }
    } else {
        // Always show first page
        pages.push(1)

        let start = Math.max(2, currentPage.value - 1)
        let end = Math.min(start + 1, totalPagesCount - 1)

        // Adjust start if we're near the end
        if (end === totalPagesCount - 1) {
            start = end - 1
        }

        // Add ellipsis after first page if needed
        if (start > 2) {
            pages.push('...')
        }

        // Add middle pages
        for (let i = start; i <= end; i++) {
            pages.push(i)
        }

        // Add ellipsis before last page if needed
        if (end < totalPagesCount - 1) {
            pages.push('...')
        }

        // Always show last page
        pages.push(totalPagesCount)
    }

    return pages
})

// Check if there are more pages to show
const hasMorePages = computed(() => {
    return Math.ceil(totalResults.value / perPage.value) > 6
})

// Handle page change when user clicks on pagination
const handlePageChange = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        currentPage.value = page
        fetchProducts()
    }
}
</script>
