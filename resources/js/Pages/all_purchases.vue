<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditPurchasePopup
                    :show="showEditModal"
                    :purchase-id="editingPurchaseId"
                    @close="closeEditModal"
                    @updated="handlePurchaseUpdated"
                />

                <!-- Search and Filter Header -->
                <div class="flex justify-between items-center mb-6">
                    <!-- KIRI: Search -->
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Purchase"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <!-- KANAN: Create Button dan Filter -->
                    <div class="flex gap-3 items-center">

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

                        <!-- Status Filter -->
                        <select
                            v-model="selectedStatus"
                            @change="handleStatusChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <!-- Payment Status Filter -->
                        <select
                            v-model="selectedPaymentStatus"
                            @change="handlePaymentStatusChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Payment Status</option>
                            <option value="paid">Paid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>

                        <button @click="goToRestorePage"
                                class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
                        </button>

                        <!-- Date Range Filter -->
                        <div class="relative inline-block">
                            <button @click="showDateFilter = !showDateFilter" class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 relative">
                                <i class="ri-filter-3-line"></i>
                                Date Filter
                                <span v-if="startDate || endDate || dueDateStart || dueDateEnd" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full" title="Date filters are active">
                                </span>
                            </button>

                            <div
                                v-show="showDateFilter"
                                v-click-outside="() => showDateFilter = false"
                                class="absolute z-50 right-0 mt-2 w-96 h-80 bg-white border rounded-lg shadow-lg p-4 transition-all duration-200" >
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-sm font-semibold">Filter by Date</h3>
                                    <button @click="showDateFilter = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-4 mb-4">

                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Purchase Date</p>
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

                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Due Date</p>
                                        <div class="flex flex-row gap-3 items-center">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">Start</label>
                                                <input type="date" v-model="dueDateStart" class="w-full border rounded-md p-2 text-sm">
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">End</label>
                                                <input type="date" v-model="dueDateEnd" class="w-full border rounded-md p-2 text-sm">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex justify-end  gap-2">
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

                <!-- Active Filters Display -->
                <div v-if="startDate || endDate || dueDateStart || dueDateEnd" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Date Filters:</div>

                    <div v-if="startDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>From: {{ formatDate(startDate) }}</span>
                        <button @click="startDate = null; fetchPurchases()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="endDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>To: {{ formatDate(endDate) }}</span>
                        <button @click="endDate = null; fetchPurchases()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>


                    <div v-if="dueDateStart" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>From: {{ formatDate(dueDateStart) }}</span>
                        <button @click="dueDateStart = null; fetchPurchases()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="dueDateEnd" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>To: {{ formatDate(dueDateEnd) }}</span>
                        <button @click="dueDateEnd = null; fetchPurchases()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>



                    <button v-if="startDate || endDate || dueDateStart || dueDateEnd" @click="resetDateFilter"
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
                            <th class="px-4 py-3 text-left font-medium">INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">SUPPLIER</th>
                            <th class="px-4 py-3 text-left font-medium">DATE</th>
                            <th class="px-4 py-3 text-left font-medium">DUE DATE</th>
                            <th class="px-4 py-3 text-left font-medium">AMOUNT</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">PAYMENT</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Skeleton Rows -->
                        <tr v-for="n in 5" :key="n" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="w-12 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-32 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-20 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-16 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Purchases Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">SUPPLIER</th>
                            <th class="px-4 py-3 text-left font-medium">DATE</th>
                            <th class="px-4 py-3 text-left font-medium">DUE DATE</th>
                            <th class="px-4 py-3 text-left font-medium">AMOUNT</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">PAYMENT</th>
                            <th class="px-4 py-3 text-left font-medium">CREATED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in purchases" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.invoice_number }}</td>
                            <td class="px-4 py-3">
                                <div class="w-[480px] truncate" :title="item.supplier?.label || ''">
                                    {{ item.supplier?.label || 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ formatDate(item.purchase_date) }}</td>
                            <td class="px-4 py-3">{{ formatDate(item.due_date || 'N/A') }}</td>
                            <td class="px-4 py-3">{{ formatCurrency(item.grand_total) }}</td>
                            <td class="px-4 py-3">
                                <span :class="['inline-block px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap', getStatusClass(item.status)]" :title="capitalizeFirstLetter(item.status)">
                                {{ capitalizeFirstLetter(item.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="['inline-block px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap', getPaymentStatusClass(item.payment_status)]" :title="formatPaymentStatus(item.payment_status)">
                                    {{ formatPaymentStatus(item.payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{item.user.name}}</td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button
                                        @click="deletePurchase(item.id, item.invoice_number)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <button
                                        @click="openEditModal(item.id)"
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
import {ref, onMounted, computed, watch} from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import RestoreIcon from "../../assets/icons/restore.svg";
import InfoIcon from "../../assets/icons/info.svg";
import EditPurchasePopup from "@/Components/EditPurchasePopup.vue";
import {jsPDF} from "jspdf";
import 'jspdf-autotable';
import * as XLSX from "xlsx";

const page = usePage()
const auth = page.props.auth
const purchases = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedPaymentStatus = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const showDateFilter = ref(false)
const startDate = ref(null)
const endDate = ref(null)
const dueDateStart = ref(null);
const dueDateEnd = ref(null);

const showEditModal = ref(false)
const editingPurchaseId = ref(null)

const searchTimeout = ref(null)

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        selectedPaymentStatus: selectedPaymentStatus.value,
        currentPage: currentPage.value,
        startDate: startDate.value,
        endDate: endDate.value,
        startDueDate: dueDateStart.value,
        endDueDate: dueDateEnd.value

    }
    localStorage.setItem('purchaseFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('purchaseFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || ''
            selectedPaymentStatus.value = filterState.selectedPaymentStatus || ''
            currentPage.value = filterState.currentPage || 1
            startDate.value = filterState.startDate || null
            endDate.value = filterState.endDate || null
            dueDateStart.value = filterState.startDueDate || null
            dueDateEnd.value = filterState.endDueDate || null
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

const formatCurrency = (amount) => {
    if (amount === undefined || amount === null) return 'N/A'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount)
}

const capitalizeFirstLetter = (string) => {
    if (!string) return 'N/A'
    return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, ' ')
}

const formatPaymentStatus = (status) => {
    if (!status) return 'N/A'
    return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const getStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'confirmed':
            return 'bg-blue-100 text-blue-800'
        case 'shipped':
            return 'bg-purple-100 text-purple-800'
        case 'delivered':
            return 'bg-green-100 text-green-800'
        case 'cancelled':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getPaymentStatusClass = (status) => {
    switch (status) {
        case 'paid':
            return 'bg-green-100 text-green-800'
        case 'partially_paid':
            return 'bg-blue-100 text-blue-800'
        case 'unpaid':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const goToDetail = (id) => {
    saveFilterState()
    localStorage.setItem('purchaseReferrer', '/purchases/all')
    router.visit(`/purchases/${id}`)
}

const openEditModal = (id) => {
    editingPurchaseId.value = id
    showEditModal.value = true
}

const closeEditModal = () => {
    showEditModal.value = false
}

const handlePurchaseUpdated = () => {
    fetchPurchases()
}

const goToCreatePage = () => {
    router.visit('/purchases/create')
}

const goToRestorePage = () => {
    router.visit('/purchases/restore')
}

const deletePurchase = async (id, invoiceNumber) => {
    const result = await Swal.fire({
        title: 'Delete Purchase',
        html: `Are you sure you want to delete purchase <strong>${invoiceNumber}</strong>?<br>The purchase will be moved to the restore page.`,
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
            await axios.delete(`/api/purchases/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `Purchase ${invoiceNumber} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchPurchases()
        } catch (error) {
            console.error('Error deleting purchase:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete purchase.',
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

const fetchPurchases = async () => {
    try {
        loading.value = true

        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            status: selectedStatus.value,
            payment_status: selectedPaymentStatus.value,
            per_page: perPage.value,
        }

        if (startDate.value) {
            params.start_date = startDate.value
        }

        if (endDate.value) {
            params.end_date = endDate.value
        }

        if (dueDateStart.value) {
            params.start_due_date = dueDateStart.value
        }

        if (dueDateEnd.value) {
            params.end_due_date = dueDateEnd.value
        }

        const response = await axios.get('/api/purchases', { params })

        const data = response.data
        purchases.value = data.data



        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page

        return purchases.value;
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
        fetchPurchases()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchPurchases()
    } else {
        handleSearch()
    }
})

const handleStatusChange = () => {
    currentPage.value = 1
    fetchPurchases()
}

const handlePaymentStatusChange = () => {
    currentPage.value = 1
    fetchPurchases()
}

const resetDateFilter = () => {
    startDate.value = null
    endDate.value = null
    dueDateStart.value = null
    dueDateEnd.value = null
    showDateFilter.value = false
    fetchPurchases()
}

const applyDateFilter = () => {
    showDateFilter.value = false
    fetchPurchases()
}

function safeFormatCurrency(value) {
    if (!value) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(Number(value));
}

function safeFormatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}


const exportToPDF = async () => {
    const loadingSwal = Swal.fire({
        title: 'Preparing PDF',
        html: 'Please wait while we prepare your report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await axios.get('/api/purchases', {
            params: {
                search: searchQuery.value.trim(),
                status: selectedStatus.value,
                payment_status: selectedPaymentStatus.value,
                start_date: startDate.value,
                end_date: endDate.value,
                start_due_date: dueDateStart.value,
                end_due_date: dueDateEnd.value,
                per_page: 1000000
            }
        });

        const allPurchases = response.data.data;

        if (allPurchases.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no purchases to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Purchase Report', 14, 20);

        // Filter information
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        let filterInfo = [];
        if (startDate.value) filterInfo.push(`Purchase Date From: ${formatDate(startDate.value)}`);
        if (endDate.value) filterInfo.push(`Purchase Date To: ${formatDate(endDate.value)}`);
        if (dueDateStart.value) filterInfo.push(`Due Date From: ${formatDate(dueDateStart.value)}`);
        if (dueDateEnd.value) filterInfo.push(`Due Date To: ${formatDate(dueDateEnd.value)}`);
        if (searchQuery.value.trim()) filterInfo.push(`Search: "${searchQuery.value.trim()}"`);
        if (selectedStatus.value) filterInfo.push(`Status: ${selectedStatus.value}`);
        if (selectedPaymentStatus.value) filterInfo.push(`Payment Status: ${selectedPaymentStatus.value}`);

        const filterText = filterInfo.length > 0
            ? `Filters: ${filterInfo.join(', ')}`
            : 'All Purchases';

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${formatDate(new Date())}`, 14, 36);
        doc.text(`Total Records: ${allPurchases.length}`, 14, 44);

        // Table headers
        const headers = [
            'No',
            'Invoice',
            'Supplier',
            'Date',
            'Due Date',
            'Amount',
            'Status',
            'Payment Status'
        ];

        // Table data
        const tableData = allPurchases.map((purchase, index) => [
            index + 1,
            purchase.invoice_number,
            purchase.supplier?.label || 'N/A',
            formatDate(purchase.purchase_date),
            formatDate(purchase.due_date) || 'N/A',
            formatCurrency(purchase.grand_total),
            capitalizeFirstLetter(purchase.status),
            formatPaymentStatus(purchase.payment_status),
            purchase.user?.name || 'N/A'
        ]);

        // Generate table
        doc.autoTable({
            head: [headers],
            body: tableData,
            startY: 50,
            margin: { top: 50 },
            styles: {
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [47, 132, 81], // LP Green color
                textColor: 255,
                fontStyle: 'bold'
            },
            columnStyles: {
                0: { cellWidth: 10 },
                1: { cellWidth: 25 },
                2: { cellWidth: 30 },
                3: { cellWidth: 20 },
                4: { cellWidth: 22 },
                5: { cellWidth: 28 },
                6: { cellWidth: 20 },
                7: { cellWidth: 25 }
            },
            didDrawPage: (data) => {
                // Page footer
                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        await loadingSwal.close();

        // Generate filename
        let fileName = 'Purchase_Report';
        if (startDate.value) fileName += `_from_${startDate.value}`;
        if (endDate.value) fileName += `_to_${endDate.value}`;
        if (dueDateStart.value) fileName += `_due_from_${dueDateStart.value}`;
        if (dueDateEnd.value) fileName += `_due_to_${dueDateEnd.value}`;
        fileName += `_${new Date().toISOString().slice(0, 10)}`;

        // Save PDF
        doc.save(`${fileName}.pdf`);

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();

        await Swal.fire({
            title: 'Export Failed',
            text: error.response?.data?.message || 'An error occurred while generating the PDF.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const exportToExcel = async () => {
    const loadingSwal = Swal.fire({
        title: 'Preparing Excel',
        html: 'Please wait while we prepare your purchase report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let purchases = [];
        try {
            const response = await fetchPurchases();
            console.log(response);
            purchases = Array.isArray(response) ? response : [];
        } catch (error) {
            console.error('Failed to fetch purchases:', error);
            throw new Error('Failed to fetch purchase data. Please try again.');
        }

        if (purchases.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no purchases to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const wb = XLSX.utils.book_new();

        const headers = [
            'No',
            'Invoice Number',
            'Purchase Date',
            'Due Date',
            'Estimated Arrival Date',
            'Actual Arrival Date',
            'Total Amount',
            'Total Discount',
            'Shipping Amount',
            'Grand Total',
            'Status',
            'Payment Status',
            'Supplier',
            'Supplier Email',
            'Created By',
            'User Role',
            'Product List',
            'Payment List',
            'Tax List'
        ];

        const wsData = [
            headers,
            ...purchases.map((purchase, index) => {
                const products = purchase.purchase_details?.map(item =>
                    `(${item.quantity} x ${item.product_name} @ ${item.unit_price}) = ${item.sub_total}${item.note ? ' - ' + item.note : ''}`
                ).join('\n') || '';

                const payments = purchase.purchase_payments?.map(p =>
                    `${safeFormatDate(p.payment_date)}: ${safeFormatCurrency(p.amount)} (${p.payment_method}) - ${p.note || ''}`
                ).join('\n') || '';

                const taxes = purchase.taxes?.map(t =>
                    `${t.name} (${t.rate}%): ${safeFormatCurrency(t.amount)}`
                ).join('\n') || '';

                return [
                    index + 1,
                    purchase.invoice_number,
                    safeFormatDate(purchase.purchase_date),
                    safeFormatDate(purchase.due_date),
                    safeFormatDate(purchase.estimated_arrival_date),
                    safeFormatDate(purchase.actual_arrival_date),
                    safeFormatCurrency(purchase.total_amount),
                    safeFormatCurrency(purchase.total_discount),
                    safeFormatCurrency(purchase.shipping_amount),
                    safeFormatCurrency(purchase.grand_total),
                    purchase.status,
                    purchase.payment_status,
                    purchase.supplier?.label || '',
                    purchase.supplier?.email || '',
                    purchase.user?.name || '',
                    purchase.user?.role?.name || '',
                    products,
                    payments,
                    taxes
                ];
            })
        ];

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        XLSX.utils.book_append_sheet(wb, ws, 'Purchases');

        if (!ws['!cols']) ws['!cols'] = [];
        const columnWidths = [5, 20, 15, 15, 20, 20, 20, 20, 20, 20, 15, 15, 30, 30, 20, 20, 50, 50, 50];
        columnWidths.forEach((w, i) => {
            ws['!cols'][i] = { wch: w };
        });

        let fileName = 'Purchase_Report_' + new Date().toISOString().slice(0, 10);
        await loadingSwal.close();

        XLSX.writeFile(wb, `${fileName}.xlsx`);

    } catch (error) {
        console.error('Excel export error:', error);
        await loadingSwal.close();

        await Swal.fire({
            title: 'Export Failed',
            text: error.message || 'An error occurred while generating the Excel file.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};


const clearFilterState = () => {
    localStorage.removeItem('purchaseFilterState')
}

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    restoreFilterState()
    fetchPurchases()
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
        fetchPurchases()
    }
}
</script>
