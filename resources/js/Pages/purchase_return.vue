<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditPurchaseReturnPopup
                    :show="showEditModal"
                    :purchase-return-id="editingPurchaseReturnId"
                    @close="closeEditModal"
                    @updated="handlePurchaseReturnUpdated"
                />

                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Purchase Return"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

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

                        <select
                            v-model="selectedStatus"
                            @change="handleStatusChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <select
                            v-model="selectedPaymentStatus"
                            @change="handlePaymentStatusChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Payment Status</option>
                            <option value="paid">Paid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="failed">Failed</option>
                        </select>

                        <select
                            v-model="selectedMethod"
                            @change="handleMethodChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Compensation</option>
                            <option value="refund">Refund</option>
                            <option value="replacement">Replacement</option>
                        </select>

                        <button @click="goToRestorePage"
                                class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
                        </button>

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
                                class="absolute z-50 right-0 mt-2 w-96 h-60 bg-white border rounded-lg shadow-lg p-4 transition-all duration-200" >
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-sm font-semibold">Filter by Return Date</h3>
                                    <button @click="showDateFilter = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Return Date</p>
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

                <div v-if="startDate || endDate" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Date Filters:</div>

                    <div v-if="startDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>From: {{ formatDate(startDate) }}</span>
                        <button @click="startDate = null; fetchPurchaseReturns()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="endDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>To: {{ formatDate(endDate) }}</span>
                        <button @click="endDate = null; fetchPurchaseReturns()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <button v-if="startDate || endDate" @click="resetDateFilter"
                            class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm hover:bg-red-200">
                        Clear All
                    </button>
                </div>

                <div v-if="loading" class="rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">PURCHASE INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">RETURN INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">RETURN DATE</th>
                            <th class="px-4 py-3 text-left font-medium">TOTAL AMOUNT</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">PAYMENT STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">COMPENSATION</th>
                            <th class="px-4 py-3 text-left font-medium">CREATED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="n in 5" :key="n" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="w-12 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-12 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-6 bg-gray-300 rounded-md animate-pulse"></div>
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
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class=" rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">PURCHASE INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">RETURN INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">RETURN DATE</th>
                            <th class="px-4 py-3 text-left font-medium">TOTAL AMOUNT</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">PAYMENT STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">COMPENSATION</th>
                            <th class="px-4 py-3 text-left font-medium">CREATED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in purchaseReturns" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.invoice_number }}</td>
                            <td class="px-4 py-3">{{ item.invoice_number_returns || 'N/A' }}</td>
                            <td class="px-4 py-3">{{ formatDate(item.return_date) }}</td>
                            <td class="px-4 py-3">{{ formatCurrency(item.total_amount) }}</td>
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
                            <td class="px-4 py-3">{{ item.compensation_method }}</td>
                            <td class="px-4 py-3">{{ item.user?.name || 'N/A' }}</td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button
                                        @click="deletePurchaseReturn(item.id, item.invoice_number)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <TrashIcon class="" />
                                    </button>

                                    <button
                                        @click="openEditModal(item.id)"
                                        class="p-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                                    >
                                        <PencilIcon class="" />
                                    </button>

                                    <button @click="goToDetail(item.id)" class="p-2 bg-lp-green text-white rounded-md hover:bg-lp-green-dark transition-colors">
                                        <InfoIcon class="" />
                                    </button>

                                    <div class="relative">
                                        <button
                                            @click="togglePrintDropdown(item.id)"
                                            class="p-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                            <PrinterIcon />
                                        </button>
                                        <div v-if="activePrintDropdown === item.id"
                                             v-click-outside="() => activePrintDropdown = null"
                                             class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                            <button
                                                @click="printDocument(item, 'return_invoice')"
                                                :disabled="!item.invoice_number_returns"
                                                :class="['block w-full text-left px-4 py-2 text-sm',
                                                         item.invoice_number_returns ? 'text-gray-700 hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed']">
                                                Print Return Invoice
                                            </button>
                                            <button
                                                @click="printDocument(item, 'delivery_note')"
                                                :disabled="!item.delivery_number_returns"
                                                :class="['block w-full text-left px-4 py-2 text-sm',
                                                         item.delivery_number_returns ? 'text-gray-700 hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed']">
                                                Print Delivery Note
                                            </button>
                                        </div>
                                    </div>
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
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import RestoreIcon from "../../assets/icons/restore.svg"
import InfoIcon from "../../assets/icons/info.svg"
import PrinterIcon from "../../assets/icons/printer-line.svg"
import EditPurchaseReturnPopup from "@/Components/EditPurchaseReturnPopup.vue"
import { jsPDF } from "jspdf"
import 'jspdf-autotable'
import * as XLSX from "xlsx"

const page = usePage()
const auth = page.props.auth
const purchaseReturns = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedPaymentStatus = ref('')
const selectedMethod = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const showDateFilter = ref(false)
const startDate = ref(null)
const endDate = ref(null)

const showEditModal = ref(false)
const editingPurchaseReturnId = ref(null)

const searchTimeout = ref(null)
const activePrintDropdown = ref(null)

const togglePrintDropdown = (id) => {
    activePrintDropdown.value = activePrintDropdown.value === id ? null : id
}

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        selectedPaymentStatus: selectedPaymentStatus.value,
        selectedMethod: selectedMethod.value, // Added this to save method filter
        currentPage: currentPage.value,
        startDate: startDate.value,
        endDate: endDate.value,
    }
    localStorage.setItem('purchaseReturnFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('purchaseReturnFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || ''
            selectedPaymentStatus.value = filterState.selectedPaymentStatus || ''
            selectedMethod.value = filterState.selectedMethod || '' // Restored method filter
            currentPage.value = filterState.currentPage || 1
            startDate.value = filterState.startDate || null
            endDate.value = filterState.endDate || null
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const formatDate = (dateString) => {
    if (!dateString) {
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
    if (!status || typeof status !== 'string') {
        return 'bg-gray-100 text-gray-800'
    }
    switch (status.toLowerCase()) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'approved':
            return 'bg-blue-100 text-blue-800'
        case 'rejected':
            return 'bg-red-100 text-red-800'
        case 'completed':
            return 'bg-green-100 text-green-800'
        case 'cancelled':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getPaymentStatusClass = (status) => {
    if (!status || typeof status !== 'string') {
        return 'bg-gray-100 text-gray-800'
    }
    switch (status.toLowerCase()) {
        case 'unpaid':
            return 'bg-red-100 text-red-800'
        case 'failed':
            return 'bg-red-100 text-red-800'
        case 'partially_paid':
            return 'bg-purple-100 text-purple-800'
        case 'paid':
            return 'bg-green-100 text-green-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}
const goToDetail = (id) => {
    saveFilterState()
    localStorage.setItem('purchaseReturnReferrer', '/purchase-returns/all')
    router.visit(`/purchase-returns/${id}`)
}

const openEditModal = (id) => {
    editingPurchaseReturnId.value = id
    showEditModal.value = true
}

const closeEditModal = () => {
    showEditModal.value = false
}

const handlePurchaseReturnUpdated = () => {
    fetchPurchaseReturns()
}

const goToRestorePage = () => {
    router.visit('/purchase-returns/restore')
}

const deletePurchaseReturn = async (id, invoiceNumber) => {
    const result = await Swal.fire({
        title: 'Delete Purchase Return',
        html: `Are you sure you want to delete purchase return <strong>${invoiceNumber}</strong>?<br>The return will be moved to the restore page.`,
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
            await axios.delete(`/api/purchase-returns/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `Purchase return ${invoiceNumber} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchPurchaseReturns()
        } catch (error) {
            console.error('Error deleting purchase return:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete purchase return.',
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

const fetchPurchaseReturns = async () => {
    try {
        loading.value = true

        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            status: selectedStatus.value,
            payment_status: selectedPaymentStatus.value,
            method: selectedMethod.value,
            per_page: perPage.value,
        }

        if (startDate.value) {
            params.start_date = startDate.value
        }

        if (endDate.value) {
            params.end_date = endDate.value
        }

        const response = await axios.get('/api/purchase-returns', { params })

        const data = response.data
        purchaseReturns.value = data.data // This is the main array for the table

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
        fetchPurchaseReturns()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchPurchaseReturns()
    } else {
        handleSearch()
    }
})

watch([selectedStatus, selectedPaymentStatus, selectedMethod], () => {
    currentPage.value = 1
    fetchPurchaseReturns()
})

const resetDateFilter = () => {
    startDate.value = null
    endDate.value = null
    showDateFilter.value = false
    fetchPurchaseReturns()
}

const applyDateFilter = () => {
    showDateFilter.value = false
    fetchPurchaseReturns()
}

function safeFormatCurrency(value) {
    if (value === undefined || value === null || isNaN(value)) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(Number(value));
}

function safeFormatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
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
        const response = await axios.get('/api/purchase-returns', {
            params: {
                search: searchQuery.value.trim(),
                status: selectedStatus.value,
                payment_status: selectedPaymentStatus.value,
                method: selectedMethod.value,
                start_date: startDate.value,
                end_date: endDate.value,
                per_page: 1000000
            }
        });

        const allPurchaseReturns = response.data.data;

        if (allPurchaseReturns.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no purchase returns to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const doc = new jsPDF({
            orientation: 'portrait'
        });

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Purchase Return Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        let filterInfo = [];
        if (startDate.value) filterInfo.push(`Return Date From: ${formatDate(startDate.value)}`);
        if (endDate.value) filterInfo.push(`Return Date To: ${formatDate(endDate.value)}`);
        if (searchQuery.value.trim()) filterInfo.push(`Search: "${searchQuery.value.trim()}"`);
        if (selectedStatus.value) filterInfo.push(`Status: ${selectedStatus.value}`);
        if (selectedPaymentStatus.value) filterInfo.push(`Payment Status: ${selectedPaymentStatus.value}`);
        if (selectedMethod.value) filterInfo.push(`Compensation Method: ${selectedMethod.value}`);

        const filterText = filterInfo.length > 0
            ? `Filters: ${filterInfo.join(', ')}`
            : 'All Purchase Returns';

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${formatDate(new Date())}`, 14, 36);
        doc.text(`Total Records: ${allPurchaseReturns.length}`, 14, 44);


        const headers = [
            'No',
            'Invoice',
            'Return Date',
            'Total Amount',
            'Status',
            'Payment Status',
            'Compensation',
            'Reason'
        ];


        const tableData = allPurchaseReturns.map((item, index) => [
            index + 1,
            item.invoice_number,
            formatDate(item.return_date),
            formatCurrency(item.total_amount),
            capitalizeFirstLetter(item.status),
            formatPaymentStatus(item.payment_status),
            item.compensation_method,
            item.reason || 'N/A'
        ]);

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
                fillColor: [47, 132, 81],
                textColor: 255,
                fontStyle: 'bold'
            },
            columnStyles: {
                0: { cellWidth: 10 },
                1: { cellWidth: 20 },
                2: { cellWidth: 20 },
                3: { cellWidth: 22 },
                4: { cellWidth: 20 },
                5: { cellWidth: 25 },
                6: { cellWidth: 'auto' },
                7: { cellWidth: 'auto' }
            },
            didDrawPage: (data) => {
                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        await loadingSwal.close();

        let fileName = 'Purchase_Return_Report';
        if (startDate.value) fileName += `_from_${startDate.value}`;
        if (endDate.value) fileName += `_to_${endDate.value}`;
        fileName += `_${new Date().toISOString().slice(0, 10)}`;

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
        html: 'Please wait while we prepare your purchase return report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let purchaseReturnsToExport = []; // Use a distinct variable name
        try {
            const response = await axios.get('/api/purchase-returns', {
                params: {
                    search: searchQuery.value.trim(),
                    status: selectedStatus.value,
                    payment_status: selectedPaymentStatus.value,
                    method: selectedMethod.value,
                    start_date: startDate.value,
                    end_date: endDate.value,
                    per_page: 1000000
                }
            });
            purchaseReturnsToExport = response.data.data;
        } catch (error) {
            console.error('Failed to fetch purchase returns:', error);
            throw new Error('Failed to fetch purchase return data. Please try again.');
        }

        if (purchaseReturnsToExport.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no purchase returns to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const wb = XLSX.utils.book_new();

        const headers = [
            'No',
            'Invoice Number',
            'Return Date',
            'Total Amount',
            'Total Tax',
            'Total Discount',
            'Shipping Amount',
            'Grand Total (Compensation Method)',
            'Status',
            'Payment Status',
            'Compensation Method',
            'Purchase ID',
            'User',
            'Reason',
        ];

        const wsData = [
            headers,
            ...purchaseReturnsToExport.map((item, index) => { // Use purchaseReturnsToExport here
                return [
                    index + 1,
                    item.invoice_number,
                    safeFormatDate(item.return_date),
                    safeFormatCurrency(item.total_amount),
                    safeFormatCurrency(item.total_tax),
                    safeFormatCurrency(item.total_discount),
                    safeFormatCurrency(item.shipping_amount),
                    safeFormatCurrency(item.grand_total),
                    item.status,
                    item.payment_status,
                    item.compensation_method,
                    item.purchase_id,
                    item.user?.name || '',
                    item.reason || '',
                ];
            })
        ];

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        XLSX.utils.book_append_sheet(wb, ws, 'PurchaseReturns');

        if (!ws['!cols']) ws['!cols'] = [];
        const columnWidths = [5, 20, 15, 20, 15, 15, 15, 20, 15, 15, 20, 15, 20, 30, 50, 50, 50]; // Adjust as needed
        columnWidths.forEach((w, i) => {
            ws['!cols'][i] = { wch: w };
        });

        let fileName = 'Purchase_Return_Report_' + new Date().toISOString().slice(0, 10);
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


const fetchPurchaseReturnDetailForPrint = async (purchaseReturnId) => {
    try {
        const response = await axios.get(`/api/purchase-returns/${purchaseReturnId}`);
        // FIX: Return the data directly, do not reassign to the global purchaseReturns ref
        return response.data.data;
    } catch (error) {
        console.error('Error fetching purchase return details for print:', error);
        throw new Error('Failed to fetch purchase return details for printing.');
    }
};

const printDocument = async (item, type) => {
    activePrintDropdown.value = null; // Close dropdown immediately

    const loadingSwal = Swal.fire({
        title: `Generating ${capitalizeFirstLetter(type.replace('_', ' '))} PDF`,
        html: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // Fetch the specific purchase return details for printing
        const purchaseReturnDetail = await fetchPurchaseReturnDetailForPrint(item.id);

        switch (type) {
            case 'return_invoice':
                await generatePurchaseReturnInvoicePDF(purchaseReturnDetail);
                break;
            case 'delivery_note':
                await generatePurchaseReturnDeliveryNotePDF(purchaseReturnDetail);
                break;
            default:
                console.warn('Unknown print document type:', type);
        }
    } catch (error) {
        console.error(`Error printing ${type}:`, error);
        Swal.fire({
            title: 'Print Failed',
            text: error.message || 'An error occurred while generating the document.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } finally {
        loadingSwal.close(); // Ensure loading Swal is closed
    }
};



const generatePurchaseReturnInvoicePDF = async (purchaseReturn) => {
    // purchaseReturn is now the single object passed directly
    if (!purchaseReturn) {
        throw new Error('Purchase return data not found for printing.');
    }

    const companyName = import.meta.env.VITE_COMPANY_NAME || 'Company Name';
    const companyAddress = import.meta.env.VITE_COMPANY_ADDRESS || 'Company Address';
    const companyPhone = import.meta.env.VITE_COMPANY_PHONE || 'Company Phone';
    const companyEmail = import.meta.env.VITE_COMPANY_EMAIL || 'Company Email';

    const doc = new jsPDF();
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 15;
    let y = margin;

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.text(companyName, margin, y);
    y += 7;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text(companyAddress, margin, y);
    y += 5;
    doc.text(`Phone: ${companyPhone} | Email: ${companyEmail}`, margin, y);
    y += 10;
    doc.setDrawColor(0);
    doc.setLineWidth(0.5);
    doc.line(margin, y, pageWidth - margin, y);
    y += 10;

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('PURCHASE RETURN INVOICE', pageWidth / 2, y, { align: 'center' });
    y += 10;

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text('Return Invoice No:', margin, y);
    doc.text(`${purchaseReturn.invoice_number_returns || 'N/A'}`, margin + 40, y);
    y += 6;
    doc.text('Original Invoice No:', margin, y);
    doc.text(`${purchaseReturn.invoice_number || 'N/A'}`, margin + 40, y);
    y += 6;
    doc.text('Return Date:', margin, y);
    doc.text(`${formatDate(purchaseReturn.return_date)}`, margin + 40, y);
    y += 6;
    doc.text('Compensation Method:', margin, y);
    doc.text(`${capitalizeFirstLetter(purchaseReturn.compensation_method)}`, margin + 40, y);
    y += 6;
    doc.text('Status:', margin, y);
    doc.text(`${capitalizeFirstLetter(purchaseReturn.status)}`, margin + 40, y);
    y += 6;
    doc.text('Payment Status:', margin, y);
    doc.text(`${formatPaymentStatus(purchaseReturn.payment_status)}`, margin + 40, y);
    y += 10;

    doc.setFont('helvetica', 'bold');
    doc.text('Supplier Information:', margin, y);
    y += 6;
    doc.setFont('helvetica', 'normal');

    const supplier = purchaseReturn.supplier;
    if (supplier) {
        doc.text(`Name: ${supplier.name || 'N/A'}`, margin, y);
        y += 5;
        doc.text(`Company: ${supplier.company_name || 'N/A'}`, margin, y);
        y += 5;
        doc.text(`Email: ${supplier.email || 'N/A'}`, margin, y);
        y += 5;
        doc.text(`Phone: ${supplier.phone || 'N/A'}`, margin, y);
        y += 5;
        doc.text(`Address: ${supplier.address || 'N/A'}`, margin, y);
        y += 5;
        doc.text(`City: ${supplier.city || 'N/A'}`, margin, y);
        y += 10;
    } else {
        doc.text('Supplier information not available.', margin, y);
        y += 10;
    }

    doc.setFont('helvetica', 'bold');
    doc.text('Returned Items:', margin, y);
    y += 5;

    const productDetails = purchaseReturn.purchase_details || [];

    const productHeaders = [['No', 'Product Name', 'Quantity', 'Unit', 'Unit Price', 'Sub Total']];
    const productData = productDetails.map((detail, index) => [
        index + 1,
        detail.product_name || 'N/A',
        detail.quantity || 0,
        detail.unit_code || 'N/A',
        formatCurrency(detail.unit_price || 0),
        formatCurrency(detail.sub_total || 0)
    ]);

    const tableWidth = pageWidth - 2 * margin;

    const colNo = 0.05 * tableWidth;
    const colProductName = 0.35 * tableWidth;
    const colQuantity = 0.10 * tableWidth;
    const colUnit = 0.10 * tableWidth;
    const colUnitPrice = 0.20 * tableWidth;
    const colSubTotal = 0.20 * tableWidth;

    doc.autoTable({
        startY: y,
        head: productHeaders,
        body: productData,
        theme: 'grid',
        headStyles: {
            fillColor: [47, 132, 81],
            textColor: 255,
            fontStyle: 'bold'
        },
        styles: {
            fontSize: 9,
            cellPadding: 2,
        },
        columnStyles: {
            0: { cellWidth: colNo, halign: 'center' },
            1: { cellWidth: colProductName, halign: 'left' },
            2: { cellWidth: colQuantity, halign: 'center' },
            3: { cellWidth: colUnit, halign: 'center' },
            4: { cellWidth: colUnitPrice, halign: 'right' },
            5: { cellWidth: colSubTotal, halign: 'right' }
        },
        didDrawPage: (data) => {
            if (data.cursor.y > doc.internal.pageSize.height - 70) {
                doc.addPage();
                data.cursor.y = margin;
            }
        }
    });

    y = doc.autoTable.previous.finalY + 10;

    const summary = [
        ['Total Returned Amount', formatCurrency(purchaseReturn.total_amount || 0)],
        ['Total Tax', formatCurrency(purchaseReturn.total_tax || 0)],
        ['Total Discount', formatCurrency(purchaseReturn.total_discount || 0)],
        ['Shipping Amount', formatCurrency(purchaseReturn.shipping_amount || 0)],
        ['Grand Total', formatCurrency(purchaseReturn.grand_total || 0)]
    ];

    doc.autoTable({
        startY: y,
        body: summary,
        theme: 'plain',
        styles: { fontSize: 10 },
        tableWidth: tableWidth,
        margin: { left: margin, right: margin },
        columnStyles: {
            0: { cellWidth: tableWidth * 0.75, halign: 'right', fontStyle: 'bold' },
            1: { cellWidth: tableWidth * 0.25, halign: 'right' }
        },
        didParseCell: (data) => {
            if (data.row.index === summary.length - 1) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [240, 240, 240];
            }
        }
    });

    y = doc.autoTable.previous.finalY + 10;

    if (purchaseReturn.reason) {
        doc.setFont('helvetica', 'bold');
        doc.text('Reason for Return:', margin, y);
        y += 5;
        doc.setFont('helvetica', 'normal');
        doc.text(purchaseReturn.reason, margin, y, { maxWidth: pageWidth - 2 * margin });
        y += 10;
    }

    y = Math.max(y, doc.internal.pageSize.height - 60);
    const signatureLabels = [`${companyName || 'Perwakilan Pembelian' }`, `${(purchaseReturn.supplier?.company_name) || 'Perwakilan Penjualan'}`];
    const signatureText = ['(Signature and Name)', '(Signature and Name)'];
    const centerAlign = { align: 'center' };

    const xPositions = [
        margin + (pageWidth - 2 * margin) * 0.25,
        margin + (pageWidth - 2 * margin) * 0.75
    ];

    signatureLabels.forEach((label, i) => {
        const x = xPositions[i];
        doc.setFont('helvetica', 'bold');
        doc.text(label, x, y, centerAlign);

        doc.setFont('helvetica', 'normal');
        doc.text(signatureText[i], x, y + 28, {
            ...centerAlign,
            lineHeightFactor: 1.2
        });
    });

    const printedAt = new Date();
    const formattedPrintedDate = printedAt.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }) + ' ' + printedAt.toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    const printedBy = purchaseReturn.user?.name || 'N/A';

    doc.setFontSize(8);
    doc.setTextColor(100);
    doc.text(`Printed Date: ${formattedPrintedDate}`, margin, doc.internal.pageSize.height - 10);
    doc.text(`Printed By: ${printedBy}`, margin, doc.internal.pageSize.height - 5);
    doc.text(`Page 1 of 1`, pageWidth - margin, doc.internal.pageSize.height - 5, { align: 'right' });


    doc.save(`Purchase_Return_Invoice_${purchaseReturn.invoice_number_returns || 'N/A'}.pdf`);
};

const generatePurchaseReturnDeliveryNotePDF = async (purchaseReturn) => {
    // purchaseReturn is now the single object passed directly
    if (!purchaseReturn) {
        throw new Error('Purchase return data not found for printing delivery note.');
    }

    const companyName = import.meta.env.VITE_COMPANY_NAME || 'PT Ambasing';
    const shippingOrigin = import.meta.env.SHIPPING_ORIGIN || 'Karawaci Tangerang'; // Assuming this env variable exists for your company's origin

    const doc = new jsPDF();
    let y = 20;

    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('SURAT JALAN', 105, y, { align: 'center' });
    y += 10;
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`NO: ${purchaseReturn.delivery_number_returns || 'N/A'}`, 105, y, { align: 'center' });
    y += 20;

    doc.setFontSize(10);
    doc.text('From:', 14, y);
    doc.text(companyName, 18, y + 5);
    doc.text(shippingOrigin, 18, y + 10);

    y += 25;

    doc.text('To:', 115, y - 20);

    const supplier = purchaseReturn.supplier;
    if (supplier) {
        doc.text(`Supplier: ${supplier.name || 'N/A'}`, 124, y - 20);
        doc.text(`Company: ${supplier.company_name || 'N/A'}`, 124, y - 15);
        doc.text(`Address: ${supplier.address || 'N/A'}`, 124, y - 10);
        doc.text(`City: ${supplier.city || 'N/A'}`, 124, y - 5);
        doc.text(`Phone: ${supplier.phone || 'N/A'}`, 124, y);
    } else {
        doc.text('Supplier information not available.', 124, y - 20);
    }
    y += 10;

    doc.text('Return Date:', 14, y);
    doc.text(`${formatDate(purchaseReturn.return_date)}`, 45, y);
    doc.text('Return No:', 120, y);
    doc.text(`${purchaseReturn.id || 'N/A'}`, 140, y);
    y += 10;

    const itemsHeaders = [['No', 'Nama Barang', 'Jumlah', 'Satuan', 'Catatan']];
    const itemsData = purchaseReturn.purchase_details.map((detail, index) => [
        index + 1,
        detail.product_name || 'N/A',
        detail.quantity,
        detail.unit_code || 'N/A',
        detail.note || '-'
    ]);

    doc.autoTable({
        startY: y,
        head: itemsHeaders,
        body: itemsData,
        theme: 'grid',
        styles: { fontSize: 9, cellPadding: 2, overflow: 'linebreak' },
        headStyles: { fillColor: [200, 200, 200], textColor: [0, 0, 0], fontStyle: 'bold' },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 80 },
            2: { cellWidth: 20, halign: 'right' },
            3: { cellWidth: 20 },
            4: { cellWidth: 50 }
        },
        didDrawPage: (data) => {
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(`Page ${data.pageNumber}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
        }
    });

    y = doc.autoTable.previous.finalY + 20;

    doc.text('Received By:', 30, y);
    doc.text('Delivered By:', 150, y);
    y += 20;
    doc.text('__________________', 23, y);
    doc.text('__________________', 143, y);
    y += 5;
    doc.text(`(Nama Penerima)`, 27, y);
    doc.text('(Nama Kurir)', 150, y);

    doc.save(`Delivery_Note_Purchase_Return_${purchaseReturn.delivery_number_returns || 'N/A'}.pdf`);
};

const clearFilterState = () => {
    localStorage.removeItem('purchaseReturnFilterState')
}

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    restoreFilterState()
    fetchPurchaseReturns()
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
        saveFilterState() // Save state on page change
        fetchPurchaseReturns()
    }
}
</script>
