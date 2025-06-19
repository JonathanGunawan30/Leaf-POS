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
                            placeholder="Search by Product or Notes"
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
                            v-model="selectedMovementType"
                            @change="handleMovementTypeChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none"
                        >
                            <option value="" class="text-lp-green">All Movement Types</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="sale">Sale</option>
                            <option value="purchase">Purchase</option>
                            <option value="purchase_return">Purchase Return</option>
                            <option value="return">Return</option>
                        </select>

                        <div class="relative inline-block">
                            <button @click="showDateFilter = !showDateFilter" class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 relative">
                                <i class="ri-filter-3-line"></i>
                                Date Filter
                                <span v-if="startDate || endDate" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full" title="Date filters are active">
                                </span>
                            </button>

                            <div
                                v-show="showDateFilter"
                                v-click-outside="() => showDateFilter = false"
                                class="absolute z-50 right-0 mt-2 w-96 h-auto bg-white border rounded-lg shadow-lg p-4 transition-all duration-200" >
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-sm font-semibold">Filter by Movement Date</h3>
                                    <button @click="showDateFilter = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Movement Date</p>
                                        <div class="flex flex-row gap-3 items-center">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">Start</label>
                                                <input type="date" v-model="startDate" class="w-full border rounded-md p-2 text-sm">
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">End</label>
                                                <input type="date" v-model="endDate" class="w-full border rounded-md p-2 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button @click="resetDateFilter" class="text-sm px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100">
                                        Reset
                                    </button>
                                    <button @click="applyDateFilter" class="text-sm px-3 py-1 bg-lp-green text-white rounded-md hover:bg-green-700">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Date Filters Display -->
                <div v-if="startDate || endDate" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Date Filters:</div>

                    <div v-if="startDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>From: {{ formatDate(startDate) }}</span>
                        <button @click="startDate = null; fetchStockMovements()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="endDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>To: {{ formatDate(endDate) }}</span>
                        <button @click="endDate = null; fetchStockMovements()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <button v-if="startDate || endDate" @click="resetDateFilter"
                            class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm hover:bg-red-200">
                        Clear All
                    </button>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">DATE</th>
                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                            <th class="px-4 py-3 text-left font-medium">USER</th>
                            <th class="px-4 py-3 text-left font-medium">TYPE</th>
                            <th class="px-4 py-3 text-left font-medium">PREVIOUS STOCK</th>
                            <th class="px-4 py-3 text-left font-medium">NEW STOCK</th>
                            <th class="px-4 py-3 text-left font-medium">QUANTITY</th>
                            <th class="px-4 py-3 text-left font-medium">NOTES</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Skeleton Rows -->
                        <tr v-for="n in 5" :key="n" class="border-b hover:bg-gray-50 animate-pulse">
                            <td class="px-4 py-3">
                                <div class="w-6 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-32 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-40 h-4 bg-gray-300 rounded"></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Stock Movements Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">DATE</th>
                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                            <th class="px-4 py-3 text-left font-medium">USER</th>
                            <th class="px-4 py-3 text-left font-medium">TYPE</th>
                            <th class="px-4 py-3 text-left font-medium">PREVIOUS STOCK</th>
                            <th class="px-4 py-3 text-left font-medium">NEW STOCK</th>
                            <th class="px-4 py-3 text-left font-medium">QUANTITY</th>
                            <th class="px-4 py-3 text-left font-medium">NOTES</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in stockMovements" :key="item.id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ formatDateTime(item.created_at) }}</td>
                            <td class="px-4 py-3">{{ item.product?.name || 'N/A' }}</td>
                            <td class="px-4 py-3">{{ item.user?.name || 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span :class="getMovementTypeClass(item.movement_type)">
                                    {{ capitalizeFirstLetter(item.movement_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ item.previous_stock }}</td>
                            <td class="px-4 py-3">{{ item.new_stock }}</td>
                            <td class="px-4 py-3" :class="getQuantityClass(item.quantity)">
                                {{ item.quantity > 0 ? '+' + item.quantity : item.quantity }}
                            </td>
                            <td class="px-4 py-3">
                                <div class=" " :title="item.notes || ''">
                                    {{ item.notes || 'N/A' }}
                                </div>
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
import { jsPDF } from 'jspdf'
import 'jspdf-autotable'
import * as XLSX from 'xlsx'

const page = usePage()
const auth = page.props.auth
const stockMovements = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedMovementType = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const showDateFilter = ref(false)
const startDate = ref(null)
const endDate = ref(null)

const searchTimeout = ref(null)

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedMovementType: selectedMovementType.value,
        startDate: startDate.value,
        endDate: endDate.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('stockMovementFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('stockMovementFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedMovementType.value = filterState.selectedMovementType || ''
            startDate.value = filterState.startDate || null
            endDate.value = filterState.endDate || null
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const capitalizeFirstLetter = (string) => {
    if (!string) return 'N/A'
    return string.charAt(0).toUpperCase() + string.slice(1)
}

const getMovementTypeClass = (type) => {
    switch (type) {
        case 'adjustment':
            return 'inline-block px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800'
        case 'sale':
            return 'inline-block px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800'
        case 'purchase':
            return 'inline-block px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'
        case 'return':
            return 'inline-block px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
        case 'purchase_return':
            return 'inline-block px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
        default:
            return 'inline-block px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
    }
}

const getQuantityClass = (quantity) => {
    if (quantity > 0) {
        return 'text-green-600'
    } else if (quantity < 0) {
        return 'text-red-600'
    }
    return ''
}

const fetchStockMovements = async () => {
    try {
        loading.value = true
        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            movement_type: selectedMovementType.value,
            per_page: perPage.value
        }

        if (startDate.value) {
            params.start_date = startDate.value
        }

        if (endDate.value) {
            params.end_date = endDate.value
        }

        const response = await axios.get('/api/stock-movements', { params })

        const paginated = response.data.data
        stockMovements.value = paginated.data

        console.log('STOCK MOVEMENT VALUE ',stockMovements.value)

        currentPage.value = paginated.current_page
        totalResults.value = paginated.total
        perPage.value = paginated.per_page

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
        fetchStockMovements()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchStockMovements()
    } else {
        handleSearch()
    }
})

const handleMovementTypeChange = () => {
    currentPage.value = 1
    fetchStockMovements()
}

const resetDateFilter = () => {
    startDate.value = null
    endDate.value = null
    showDateFilter.value = false
    fetchStockMovements()
}

const applyDateFilter = () => {
    showDateFilter.value = false
    fetchStockMovements()
}

const clearFilterState = () => {
    localStorage.removeItem('stockMovementFilterState')
}

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    restoreFilterState()
    fetchStockMovements()
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
        fetchStockMovements()
    }
}

const fetchAllStockMovements = async () => {
    try {
        loading.value = true
        const params = {
            per_page: 9999,
            movement_type: selectedMovementType.value || null
        }

        if (startDate.value) {
            params.start_date = startDate.value
        }

        if (endDate.value) {
            params.end_date = endDate.value
        }

        if (searchQuery.value.trim()) {
            params.search = searchQuery.value.trim()
        }

        const response = await axios.get('/api/stock-movements', { params })
        return response.data.data
    } catch (error) {
        console.error('Error fetching all stock movements:', error)
        Swal.fire({
            title: 'Error',
            text: 'Failed to fetch all stock movements for export.',
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

    const allStockMovements = await fetchAllStockMovements()

    const doc = new jsPDF()

    doc.setFontSize(18)
    doc.text('Stock Movement History', 14, 22)

    doc.setFontSize(11)
    let filterText = 'Filter: '
    if (selectedMovementType.value) {
        filterText += `Type: ${capitalizeFirstLetter(selectedMovementType.value)}`
    } else {
        filterText += 'All Movement Types'
    }

    if (startDate.value || endDate.value) {
        filterText += ' | Date: '
        if (startDate.value) filterText += `From ${formatDate(startDate.value)}`
        if (startDate.value && endDate.value) filterText += ' '
        if (endDate.value) filterText += `To ${formatDate(endDate.value)}`
    }

    doc.text(filterText, 14, 30)

    const today = new Date()
    doc.text(`Generated: ${formatDate(today)}`, 14, 38)

    const tableColumn = ["No", "Date", "Product", "User", "Type", "Previous", "New", "Quantity", "Notes"]
    const tableRows = allStockMovements.map((item, index) => [
        index + 1,
        formatDateTime(item.created_at),
        item.product?.name || 'N/A',
        item.user?.name || 'N/A',
        capitalizeFirstLetter(item.movement_type),
        item.previous_stock,
        item.new_stock,
        item.quantity > 0 ? '+' + item.quantity : item.quantity,
        item.notes || 'N/A'
    ])

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 45,
        styles: {
            fontSize: 8,
            cellPadding: 2,
            overflow: 'linebreak',
            valign: 'middle'
        },
        headStyles: {
            fillColor: [47, 132, 81],
            textColor: 255,
            fontStyle: 'bold'
        },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 30 },
            2: { cellWidth: 30 },
            3: { cellWidth: 25 },
            4: { cellWidth: 20 },
            5: { cellWidth: 15 },
            6: { cellWidth: 15 },
            7: { cellWidth: 15 },
            8: { cellWidth: 30 }
        },
        didDrawPage: (data) => {
            doc.setFontSize(8)
            doc.setTextColor(150)
            const pageCount = doc.internal.getNumberOfPages()
            doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10)
        }
    })

    Swal.close()
    doc.save('stock-movement-history.pdf')
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

    const allStockMovements = await fetchAllStockMovements()

    const worksheet = XLSX.utils.aoa_to_sheet([[
        "No", "Date", "Product", "User", "Type", "Previous Stock", "New Stock", "Quantity", "Notes"
    ]])

    allStockMovements.forEach((item, index) => {
        XLSX.utils.sheet_add_aoa(worksheet, [[
            index + 1,
            formatDateTime(item.created_at),
            item.product?.name || 'N/A',
            item.user?.name || 'N/A',
            capitalizeFirstLetter(item.movement_type),
            item.previous_stock,
            item.new_stock,
            item.quantity,
            item.notes || ''
        ]], { origin: -1 })
    })

    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, "Stock Movements")

    let fileName = 'stock-movement-history'
    if (selectedMovementType.value) {
        fileName = `${selectedMovementType.value}-stock-movements`
    }
    if (startDate.value) {
        fileName += `-from-${startDate.value}`
    }
    if (endDate.value) {
        fileName += `-to-${endDate.value}`
    }

    Swal.close()
    XLSX.writeFile(workbook, `${fileName}.xlsx`)
}
</script>
