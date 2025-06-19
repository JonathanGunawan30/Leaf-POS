<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Edit Stock Popup -->
                <div v-if="showEditPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Adjust Stock for {{ editingProduct.name }}</h3>
                            <button @click="closeEditPopup" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                                <input
                                    type="text"
                                    class="w-full px-3 py-2 border rounded-lg bg-gray-100"
                                    :value="editingProduct.stock"
                                    readonly
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Stock</label>
                                <input
                                    v-model="newStock"
                                    type="number"
                                    min="0"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea
                                    v-model="adjustmentNotes"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                    rows="3"
                                    placeholder="Reason for adjustment"
                                ></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="closeEditPopup"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="saveStockAdjustment"
                                class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                                :disabled="!isValidAdjustment"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>

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
                        <!-- Export buttons -->
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
                    </div>
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
                            <th class="px-4 py-3 text-left font-medium">SKU</th>
                            <th class="px-4 py-3 text-left font-medium">ACTION</th>
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
                                <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
                            <th class="px-4 py-3 text-left font-medium">SKU</th>
                            <th class="px-4 py-3 text-left font-medium">ACTION</th>
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
                                <span v-else>{{ item.barcode || 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">{{ item.sku || 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <button
                                    @click="openEditPopup(item)"
                                    class="p-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                                >
                                    <pencil-icon class="" />
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-4">
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
import Swal from 'sweetalert2'
import PencilIcon from '/resources/assets/icons/pencil.svg'
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

// Stock adjustment specific variables
const showEditPopup = ref(false)
const editingProduct = ref({})
const newStock = ref(0)
const adjustmentNotes = ref('')

const isValidAdjustment = computed(() => {
    return newStock.value !== '' &&
           newStock.value >= 0 &&
           adjustmentNotes.value.trim() !== '' &&
           newStock.value !== editingProduct.value.stock
})

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedCategory: selectedCategory.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('stockAdjustmentFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('stockAdjustmentFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedCategory.value = filterState.selectedCategory || ''
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const openEditPopup = (product) => {
    editingProduct.value = product
    newStock.value = product.stock
    adjustmentNotes.value = ''
    showEditPopup.value = true
}

const closeEditPopup = () => {
    showEditPopup.value = false
    editingProduct.value = {}
    newStock.value = 0
    adjustmentNotes.value = ''
}

const saveStockAdjustment = async () => {
    if (!isValidAdjustment.value) return

    try {
        loading.value = true

        // Call API to update stock
        await axios.post('/api/stock-adjustments', {
            product_id: editingProduct.value.id,
            previous_stock: editingProduct.value.stock,
            new_stock: newStock.value,
            notes: adjustmentNotes.value
        })

        Swal.fire({
            title: 'Success!',
            text: `Stock for ${editingProduct.value.name} has been updated.`,
            icon: 'success',
            confirmButtonText: 'OK'
        })

        closeEditPopup()
        fetchProducts()
    } catch (error) {
        console.error('Error adjusting stock:', error)

        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to adjust stock.',
            icon: 'error',
            confirmButtonText: 'OK'
        })
    } finally {
        loading.value = false
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
                per_page: perPage.value
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

const searchTimeout = ref(null)

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

const handleCategoryChange = () => {
    currentPage.value = 1
    fetchProducts()
}

const clearFilterState = () => {
    localStorage.removeItem('stockAdjustmentFilterState')
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
                category_id: selectedCategory.value || null
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

    const allProducts = await fetchAllProducts()

    const doc = new jsPDF()

    doc.setFontSize(18)
    doc.text('Stock Adjustment List', 14, 22)

    doc.setFontSize(11)
    let filterText = 'Filter: '
    if (selectedCategory.value) {
        const category = categories.value.find(c => c.id === selectedCategory.value)
        filterText += `Category: ${category ? category.name : 'Selected Category'}`
    } else {
        filterText += 'All Categories'
    }

    doc.text(filterText, 14, 30)

    const today = new Date()
    doc.text(`Date: ${today.toLocaleDateString()}`, 14, 38)

    const tableColumn = ["No", "Product", "Category", "Stock", "Barcode", "SKU"]
    const tableRows = allProducts.map((item, index) => [
        index + 1,
        item.name,
        item.category?.name || 'N/A',
        item.stock,
        item.barcode || 'N/A',
        item.sku || 'N/A'
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
            1: { cellWidth: 50 },
            2: { cellWidth: 35 },
            3: { cellWidth: 20 },
            4: { cellWidth: 35 },
            5: { cellWidth: 25 }
        }
    })

    Swal.close()
    doc.save('stock-adjustment-list.pdf')
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
        "No", "Product", "Category", "Stock", "Barcode", "SKU"
    ]])

    allProducts.forEach((item, index) => {
        XLSX.utils.sheet_add_aoa(worksheet, [[
            index + 1,
            item.name || '',
            item.category?.name || '',
            item.stock || 0,
            item.barcode || '',
            item.sku || ''
        ]], { origin: -1 })
    })

    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, "Stock Adjustment")

    let fileName = 'stock-adjustment-list'
    if (selectedCategory.value) {
        const category = categories.value.find(c => c.id === selectedCategory.value)
        if (category) {
            fileName = `${category.name.toLowerCase().replace(/\s+/g, '-')}-stock-adjustment`
        }
    }

    Swal.close()

    XLSX.writeFile(workbook, `${fileName}.xlsx`)
}
</script>
