<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Stock Opname (Notes/Location)"
                            class="pl-10 pr-4 py-2 border rounded-lg w-96 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <div class="flex gap-3 items-center">


                        <button
                            @click="goToAllStockOpnames"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Stock Opname</p>
                        </button>

                    </div>
                </div>

                <div v-if="startDate || endDate" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Date Filters:</div>

                    <div v-if="startDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>From: {{ formatDate(startDate) }}</span>
                        <button @click="startDate = null; fetchStockOpnames()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="endDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>To: {{ formatDate(endDate) }}</span>
                        <button @click="endDate = null; fetchStockOpnames()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <button v-if="startDate || endDate" @click="resetDateFilter"
                            class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm hover:bg-red-200">
                        Clear All
                    </button>
                </div>

                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">ID.</th>
                            <th class="px-4 py-3 text-left font-medium">USER</th>
                            <th class="px-4 py-3 text-left font-medium">OPNAME DATE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">NOTES</th>
                            <th class="px-4 py-3 text-left font-medium">LOCATION</th>
                            <th class="px-4 py-3 text-left font-medium">APPROVED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="n in 5" :key="n" class="border-b hover:bg-gray-50 animate-pulse">
                            <td class="px-4 py-3">
                                <div class="w-6 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-6 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-4 bg-gray-300 rounded-full"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-40 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-4 bg-gray-300 rounded"></div>
                            </td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <div class="w-8 h-8 bg-gray-300 rounded-md"></div>
                                    <div class="w-8 h-8 bg-gray-300 rounded-md"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else-if="stockOpnames.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Stock Opnames</h3>
                    <p class="text-gray-500">There are no deleted stock opnames to restore.</p>
                </div>


                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">ID.</th>
                            <th class="px-4 py-3 text-left font-medium">USER</th>
                            <th class="px-4 py-3 text-left font-medium">OPNAME DATE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">NOTES</th>
                            <th class="px-4 py-3 text-left font-medium">LOCATION</th>
                            <th class="px-4 py-3 text-left font-medium">APPROVED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in stockOpnames" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.id }}</td>
                            <td class="px-4 py-3">{{ item.user?.name || 'N/A' }}</td>
                            <td class="px-4 py-3">{{ formatDate(item.opname_date) }}</td>
                            <td class="px-4 py-3">
                                <span :class="['inline-block px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap', getStatusClass(item.status)]" :title="capitalizeFirstLetter(item.status)">
                                {{ capitalizeFirstLetter(item.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-[300px] truncate" :title="item.notes || ''">
                                    {{ item.notes || 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ item.location || 'N/A' }}</td>
                            <td class="px-4 py-3">{{ item.approved_by?.name || 'N/A' }}</td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button
                                        @click="restoreStockOpname(item.id, item.id)"
                                        class="p-1.5 bg-lp-green text-white rounded-md hover:bg-green-800 transition-colors">
                                        <RestoreIcon class="w-5 h-5"/>
                                    </button>

                                    <button
                                        @click="forceDeleteStockOpname(item.id, item.id)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <TrashIcon class="w-5 h-5"/>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="stockOpnames.length > 0" class="flex justify-between items-center mt-4">
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

                <div id="print-container" class="hidden"></div>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import Sidebar from '../Components/Sidebar.vue'
import {router, usePage} from '@inertiajs/vue3'
import {computed, onMounted, ref, watch} from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import TrashIcon from '/resources/assets/icons/trash.svg'
import RestoreIcon from '/resources/assets/icons/restore-white.svg'
import 'jspdf-autotable';


const page = usePage()
const auth = page.props.auth
const stockOpnames = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('')
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
        selectedStatus: selectedStatus.value,
        currentPage: currentPage.value,
        startDate: startDate.value,
        endDate: endDate.value,
    }
    localStorage.setItem('trashedStockOpnameFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('trashedStockOpnameFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || ''
            currentPage.value = filterState.currentPage || 1
            startDate.value = filterState.startDate || null
            endDate.value = filterState.endDate || null
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const formatDate = (dateString) => {
    if (!dateString || (typeof dateString === 'string' && dateString.trim() === '')) {
        return 'N/A'
    }

    const date = new Date(dateString)

    if (isNaN(date.getTime())) {
        return 'N/A'
    }

    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const capitalizeFirstLetter = (string) => {
    if (!string) return 'N/A'
    return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, ' ')
}

const getStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'submitted':
            return 'bg-blue-100 text-blue-800'
        case 'approved':
            return 'bg-green-100 text-green-800'
        case 'rejected':
            return 'bg-red-100 text-red-800'
        case 'draft':
            return 'bg-yellow-100 text-yellow-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const goToAllStockOpnames = () => {
    router.visit('/stock-opname/all')
}

const restoreStockOpname = async (id, displayId) => {
        try {
            loading.value = true;
            const token = localStorage.getItem('X-API-TOKEN');
            if (!token) {
                router.visit('/');
                return;
            }

            await axios.post(`/api/stock-opnames/${id}/restore`, {}, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-HTTP-Method-Override': 'PATCH'
                }
            });

            Swal.fire({
                title: 'Restored!',
                text: `Stock opname ID ${displayId} has been restored successfully.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            });

            fetchStockOpnames();
        } catch (error) {
            console.error('Error restoring stock opname:', error);

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to restore stock opname.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            });
        } finally {
            loading.value = false;
        }
};


const forceDeleteStockOpname = async (id, displayId) => {
    const result = await Swal.fire({
        title: 'Permanently Delete Stock Opname',
        html: `<div class="text-left">
            <p class="mb-2">Are you sure you want to <strong>permanently delete</strong> <strong>${displayId}</strong>?</p>
            <p class="text-red-600 font-bold">Warning: This action cannot be undone!</p>
            <p>The stock opname will be permanently removed from the system and cannot be recovered.</p>
        </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete permanently!',
        cancelButtonText: 'Cancel'
    })

    if (result.isConfirmed) {
        try {
            loading.value = true
            const token = localStorage.getItem('X-API-TOKEN');
            if (!token) {
                router.visit('/');
                return;
            }

            await axios.delete(`/api/stock-opnames/${id}/force`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            Swal.fire({
                title: 'Permanently Deleted!',
                text: `Stock opname ID ${displayId} has been permanently deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchStockOpnames()
        } catch (error) {
            console.error('Error permanently deleting stock opname:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to permanently delete stock opname.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })
        } finally {
            loading.value = false
        }
    }
}

const fetchStockOpnames = async () => {
    try {
        loading.value = true

        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            status: selectedStatus.value,
            per_page: perPage.value,
            with: 'user,approved_by,stock_opname_items.product',
        }

        if (startDate.value) {
            params.start_date = startDate.value;
        }

        if (endDate.value) {
            params.end_date = endDate.value;
        }

        const response = await axios.get('/api/stock-opnames/trashed', { params })

        const data = response.data
        stockOpnames.value = data.data

        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page

        return stockOpnames.value;
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
        fetchStockOpnames()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchStockOpnames()
    } else {
        handleSearch()
    }
})

const handleStatusChange = () => {
    currentPage.value = 1
    fetchStockOpnames()
}

const resetDateFilter = () => {
    startDate.value = null
    endDate.value = null
    showDateFilter.value = false
    fetchStockOpnames()
}

const applyDateFilter = () => {
    showDateFilter.value = false
    fetchStockOpnames()
}

function safeFormatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}


onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    restoreFilterState()
    fetchStockOpnames()
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
        fetchStockOpnames()
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-container, #print-container * {
        visibility: visible;
    }
    #print-container {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
