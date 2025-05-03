<template>
    <div>
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Search and Filter Header -->
                <div class="flex justify-between items-center mb-6">
                    <div class="relative">
                        <input 
                            v-model="searchQuery"
                            @input="handleSearch"
                            type="text" 
                            placeholder="Search Product"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-green-500"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                    <div class="flex gap-3">
                        <button 
                            @click="fetchProducts"
                            class="flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-refresh-line"></i>
                            Restore
                        </button>
                        <select 
                            v-model="selectedCategory"
                            @change="handleCategoryChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 focus:outline-none"
                        >
                            <option value="">Category</option>
                            <!-- Add categories dynamically -->
                        </select>
                        <button class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="ri-filter-3-line"></i>
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                    <i class="ri-loader-4-line animate-spin text-3xl text-green-600"></i>
                </div>

                <!-- Products Table -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left">NO.</th>
                                <th class="px-4 py-3 text-left">PRODUCT</th>
                                <th class="px-4 py-3 text-left">CATEGORY</th>
                                <th class="px-4 py-3 text-left">STOCK</th>
                                <th class="px-4 py-3 text-left">BARCODE</th>
                                <th class="px-4 py-3 text-left">SELLING PRICE</th>
                                <th class="px-4 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in products" :key="item.id" class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ item.id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img :src="item.images ? JSON.parse(item.images)[0] : '/placeholder.jpg'" 
                                             alt="Product" 
                                             class="w-12 h-12 object-cover rounded"
                                        >
                                        <span>{{ item.name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ item.category?.name || 'N/A' }}</td>
                                <td class="px-4 py-3">{{ item.stock }}</td>
                                <td class="px-4 py-3">
                                    <img :src="`data:image/png;base64,${item.barcode}`" 
                                         alt="Barcode" 
                                         class="h-8"
                                    >
                                </td>
                                <td class="px-4 py-3">{{ formatPrice(item.selling_price) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <button class="p-1 bg-red-500 text-white rounded">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <button class="p-1 bg-blue-500 text-white rounded">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="p-1 bg-green-500 text-white rounded">
                                            <i class="ri-whatsapp-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-4">
                    <span class="text-sm text-gray-600">
                        Showing {{ from }} - {{ to }} of {{ totalResults }} results
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
                                        'bg-green-600 text-white' : 
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
import { usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const page = usePage()
const auth = page.props.auth
const products = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const fetchProducts = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/products', {
            params: {
                page: currentPage.value,
                search: searchQuery.value,
                category: selectedCategory.value,
                per_page: perPage.value
            }
        })
        products.value = response.data.data
        totalResults.value = response.data.total
    } catch (error) {
        console.error('Error fetching products:', error)
    } finally {
        loading.value = false
    }
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price)
}

onMounted(() => {
    fetchProducts()
})

const handleSearch = () => {
    currentPage.value = 1
    fetchProducts()
}

const handleCategoryChange = () => {
    currentPage.value = 1
    fetchProducts()
}

const totalPages = computed(() => Math.ceil(totalResults.value / perPage.value))

const from = computed(() => ((currentPage.value - 1) * perPage.value) + 1)
const to = computed(() => Math.min(currentPage.value * perPage.value, totalResults.value))

const displayedPages = computed(() => {
    const pages = []
    const maxVisiblePages = 5 // Changed to 5 to match the UI
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

const hasMorePages = computed(() => {
    return Math.ceil(totalResults.value / perPage.value) > 6
})

const handlePageChange = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        currentPage.value = page
        fetchProducts()
    }
}
</script>