<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <EditProductPopup
                    :show="showEditPopup"
                    :product-id="selectedProductId"
                    @close="closeEditPopup"
                    @updated="handleProductUpdated"
                />
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
                            @click="exportToPDF"
                            class="flex items-center gap-2 px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-50"
                        >
                            <i class="ri-file-pdf-line"></i>
                            Export PDF
                        </button>

                        <button
                            @click="exportToExcel"
                            class="flex items-center gap-2 px-4 py-2 border border-green-600 text-green-600 rounded-lg hover:bg-green-50"
                        >
                            <i class="ri-file-excel-line"></i>
                            Export Excel
                        </button>

                        <button
                            @click="goToRestorePage"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
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
                        <button
                            @click="refreshData"
                            :disabled="loading"
                            class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50"
                        >
                            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="text-sm">{{ loading ? 'Loading...' : 'Sync' }}</span>
                        </button>
                    </div>
                </div>

                <div v-show="showFilterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out" :class="{ 'opacity-0': !showFilterModal, 'opacity-100': showFilterModal }">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg transition-transform duration-300 ease-in-out transform" :class="{ 'scale-90 opacity-0': !showFilterModal, 'scale-100 opacity-100': showFilterModal }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Filter Products</h3>
                            <button @click="showFilterModal = false" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
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
                                    <button
                                        @click="deleteProduct(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <button
                                        @click="openEditPopup(item.id)"
                                        class="p-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                                    >
                                        <pencil-icon class="" />
                                    </button>

                                    <button @click="goToDetail(item.id)" class="p-2 bg-lp-green text-white rounded-md hover:bg-lp-green-dark transition-colors">
                                        <info-icon class="" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <span class="text-sm text-gray-600">
                      Showing {{ isNaN(from) ? 0 : from }} - {{ isNaN(to) ? 0 : to }} of {{ totalResults ?? 0 }} results
                    </span>

                    <div class="flex items-center gap-2">
                        <button
                            @click="handlePageChange(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="p-2 border rounded-lg hover:bg-gray-50"
                            :class="{ 'cursor-not-allowed opacity-50': currentPage === 1 }"
                        >
                            <i class="ri-arrow-left-s-line"></i>
                        </button>

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
import Swal from 'sweetalert2'
import RestoreIcon from '/resources/assets/icons/restore.svg'
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import InfoIcon from '/resources/assets/icons/info.svg'
import EditProductPopup from '../Components/EditProductPopup.vue'
import { jsPDF } from 'jspdf'
import 'jspdf-autotable'
import * as XLSX from 'xlsx'

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

const showEditPopup = ref(false)
const selectedProductId = ref(null)

const minSellingPrice = ref('')
const maxSellingPrice = ref('')
const minPurchasePrice = ref('')
const maxPurchasePrice = ref('')
const showFilterModal = ref(false)

const searchTimeout = ref(null)

// Function to trigger data refresh
const refreshData = () => {
    fetchProducts();
};

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
    localStorage.setItem('productFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('productFilterState')
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

const openEditPopup = (id) => {
    selectedProductId.value = id
    showEditPopup.value = true
}

const closeEditPopup = () => {
    showEditPopup.value = false
}

const handleProductUpdated = () => {
    fetchProducts()
}

const goToDetail = (id) => {
    saveFilterState()
    localStorage.setItem('productReferrer', '/products/all')
    router.visit(`/products/${id}`)
}

const goToRestorePage = () => {
    router.visit('/products/restore')
}

const deleteProduct = async (id, productName) => {
    const result = await Swal.fire({
        title: 'Delete Product',
        html: `Are you sure you want to delete <strong>${productName}</strong>?<br>The product will be moved to the restore page.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    })

    if (result.isConfirmed) {
        try {
            loading.value = true
            await axios.delete(`/api/products/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `${productName} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK'
            })

            fetchProducts()
        } catch (error) {
            console.error('Error deleting product:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || 'Failed to delete product.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
        } finally {
            loading.value = false
        }
    }
}

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories')
        categories.value = response.data.data
    } catch (error) {
        console.error('Error fetching categories:', error)
    }
}

const fetchProducts = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/products', {
            params: {
                page: currentPage.value,
                search: searchQuery.value.trim(),
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
            console.error('API Error:', {
                status: error.response.status,
                data: error.response.data,
                headers: error.response.headers
            })

            if (error.response.status === 401) {
                localStorage.removeItem('X-API-TOKEN')
                router.visit('/')
            }
        } else if (error.request) {

            console.error('No response received:', error.request)
        } else {

            console.error('Error setting up request:', error.message)
        }
    } finally {
        loading.value = false
    }
}

const handleSearch = () => {

    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }

    searchTimeout.value = setTimeout(() => {
        currentPage.value = 1
        fetchProducts()
    }, 300)
}

watch(searchQuery, (newValue) => {
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


const handleCategoryChange = () => {
    currentPage.value = 1
    fetchProducts()
}

const applyFilters = () => {
    currentPage.value = 1
    fetchProducts()
    showFilterModal.value = false
}

const clearFilterState = () => {
    localStorage.removeItem('productFilterState')
}

const resetFilters = () => {
    minSellingPrice.value = ''
    maxSellingPrice.value = ''
    minPurchasePrice.value = ''
    maxPurchasePrice.value = ''
    currentPage.value = 1
    clearFilterState()
    fetchProducts()
    showFilterModal.value = false
}

const clearFilter = (filterName) => {
    if (filterName === 'minSellingPrice') minSellingPrice.value = ''
    if (filterName === 'maxSellingPrice') maxSellingPrice.value = ''
    if (filterName === 'minPurchasePrice') minPurchasePrice.value = ''
    if (filterName === 'maxPurchasePrice') maxPurchasePrice.value = ''
    currentPage.value = 1
    saveFilterState()
    fetchProducts()
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price)
}

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    restoreFilterState()

    fetchCategories()
    fetchProducts()
})

const totalPages = computed(() => Math.ceil(totalResults.value / perPage.value))
const from = computed(() => ((currentPage.value - 1) * perPage.value) + 1)
const to = computed(() => Math.min(currentPage.value * perPage.value, totalResults.value))

const isFilterActive = computed(() => {
    return minSellingPrice.value !== '' ||
        maxSellingPrice.value !== '' ||
        minPurchasePrice.value !== '' ||
        maxPurchasePrice.value !== ''
})

const displayedPages = computed(() => {
    const pages = []
    const maxVisiblePages = 5
    const totalPagesCount = Math.ceil(totalResults.value / perPage.value)

    if (totalPagesCount <= maxVisiblePages) {
        for (let i = 1; i <= totalPagesCount; i++) {
            pages.push(i)
        }
    } else {
        pages.push(1)

        let start = Math.max(2, currentPage.value - 1)
        let end = Math.min(start + 1, totalPagesCount - 1)

        if (end === totalPagesCount - 1) {
            start = end - 1
        }
        if (start > 2) {
            pages.push('...')
        }


        for (let i = start; i <= end; i++) {
            pages.push(i)
        }

        if (end < totalPagesCount - 1) {
            pages.push('...')
        }

        pages.push(totalPagesCount)
    }

    return pages
})

const hasMorePages = computed(() => {
    return Math.ceil(totalResults.value / perPage.value) > 6
})

const handlePageChange = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        currentPage.value = page
        fetchProducts()
    }
}

const fetchAllProducts = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/products', {
            params: {
                per_page: 9999,
                category_id: selectedCategory.value || null,
                min_selling: minSellingPrice.value || null,
                max_selling: maxSellingPrice.value || null,
                min_purchase: minPurchasePrice.value || null,
                max_purchase: maxPurchasePrice.value || null
            }
        })
        return response.data.data
    } catch (error) {
        console.error('Error fetching all products:', error)
        Swal.fire({
            title: 'Error',
            text: 'Failed to fetch all products for export.',
            icon: 'error',
            confirmButtonText: 'OK'
        })
        return []
    } finally {
        loading.value = false
    }
}

const exportToPDF = async () => {
    Swal.fire({
        title: 'Exporting...',
        text: 'Preparing your PDF file',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    })

    const rawProducts = await fetchAllProducts()

    const allProducts = await Promise.all(rawProducts.map(async (item) => {
        let base64Image = ''
        const imageUrl = item.images
        if (imageUrl) {
            try {
                base64Image = await convertImageToBase64(imageUrl)
            } catch (e) {
                console.warn('Image load failed:', imageUrl)
            }
        }
        return { ...item, image_base64: base64Image }
    }))

    const doc = new jsPDF()

    doc.setFontSize(18)
    doc.text('Product List', 14, 22)

    doc.setFontSize(11)
    let filterText = 'Filter: '
    if (selectedCategory.value) {
        const category = categories.value.find(c => c.id === selectedCategory.value)
        filterText += `Category: ${category ? category.name : 'Selected Category'}`
    } else {
        filterText += 'All Categories'
    }

    if (isFilterActive.value) {
        filterText += ' (Price filters applied)'
    }

    doc.text(filterText, 14, 30)

    const today = new Date()
    doc.text(`Date: ${today.toLocaleDateString()}`, 14, 38)

    const tableColumn = ["No", "Image", "Product", "Category", "Stock", "Barcode", "Selling Price"]
    const tableRows = allProducts.map((item, index) => [
        index + 1,
        '',
        item.name,
        item.category?.name || 'N/A',
        item.stock,
        item.barcode || 'N/A',
        formatPrice(item.selling_price).replace('Rp', '').trim()
    ])

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 45,
        styles: {
            fontSize: 10,
            cellPadding: 3,
            overflow: 'linebreak',
            valign: 'middle'
        },
        headStyles: {
            fillColor: [47, 132, 81],
        },
        columnStyles: {
            0: { cellWidth: 12 },
            1: { cellWidth: 20 },
            2: { cellWidth: 35 },
            3: { cellWidth: 25 },
            4: { cellWidth: 20 },
            5: { cellWidth: 33 },
            6: { cellWidth: 30 }
        },
        didDrawCell: function (data) {
            if (data.section === 'body' && data.column.index === 1 && data.row.index < allProducts.length) {

                const imgData = allProducts[data.row.index].image_base64
                if (imgData) {
                    doc.addImage(imgData, 'PNG', data.cell.x + 1, data.cell.y + 1, 8, 8)
                }
            }
        }
    })

    Swal.close()
    doc.save('product-list.pdf')
}

const exportToExcel = async () => {
    Swal.fire({
        title: 'Exporting...',
        text: 'Preparing your Excel file',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    })

    const allProducts = await fetchAllProducts()

    const worksheet = XLSX.utils.aoa_to_sheet([[
        "No", "Product", "Description", "Category", "Unit", "Barcode", "Weight", "Volume",
        "Selling Price", "Purchase Price", "Stock", "Stock Alert", "SKU", "Discount",
        "Brand"
    ]])

    allProducts.forEach((item, index) => {
        XLSX.utils.sheet_add_aoa(worksheet, [[
            index + 1,
            item.name || '',
            item.description || '',
            item.category?.name || '',
            item.unit?.name || '',
            item.barcode || '',
            item.weight || '',
            item.volume || '',
            formatPrice(item.selling_price || 0).replace('Rp', '').trim(),
            formatPrice(item.purchase_price || 0).replace('Rp', '').trim(),
            item.stock || 0,
            item.stock_alert || '',
            item.sku || '',
            item.discount || '',
            item.brand || '',
        ]], { origin: -1 })
    })


    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, "Products")

    let fileName = 'product-list'
    if (selectedCategory.value) {
        const category = categories.value.find(c => c.id === selectedCategory.value)
        if (category) {
            fileName = `${category.name.toLowerCase().replace(/\s+/g, '-')}-products`
        }
    }

    Swal.close()

    XLSX.writeFile(workbook, `${fileName}.xlsx`)
}

const convertImageToBase64 = (url) => {
    return new Promise((resolve, reject) => {
        const img = new Image()
        img.crossOrigin = 'anonymous'
        img.onload = function () {
            const canvas = document.createElement('canvas')
            canvas.width = this.width
            canvas.height = this.height
            const ctx = canvas.getContext('2d')
            ctx.drawImage(this, 0, 0)
            resolve(canvas.toDataURL('image/png'))
        }
        img.onerror = reject
        img.src = url
    })
}
</script>
