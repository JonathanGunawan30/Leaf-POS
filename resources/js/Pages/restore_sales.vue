<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditSalePopup
                    :show="showEditModal"
                    :sale-id="editingSaleId"
                    @close="closeEditModal"
                    @updated="handleSaleUpdated"
                />

                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Sales"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <div class="flex gap-3 items-center">


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

                        <div class="flex gap-3 items-center">
                            <button @click="goToAllSales"
                                    class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                                <i class="ri-arrow-left-line text-lp-green"></i>
                                <p class="text-lp-green font-medium">Back to Sales</p>
                            </button>
                        </div>

                        <div class="relative inline-block">
                            <button @click="showDateFilter = !showDateFilter" class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 relative">
                                <i class="ri-filter-3-line"></i>
                                Date Filter
                                <span v-if="saleDateStart || saleDateEnd || dueDateStart || dueDateEnd || downpaymentIssueDateStart || downpaymentIssueDateEnd" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full" title="Date filters are active">
                                </span>
                            </button>

                            <div
                                v-show="showDateFilter"
                                v-click-outside="() => showDateFilter = false"
                                class="absolute z-50 right-0 mt-2 w-96 h-100 bg-white border rounded-lg shadow-lg p-4 transition-all duration-200" >
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-sm font-semibold">Filter by Date</h3>
                                    <button @click="showDateFilter = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-4 mb-4">

                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Sale Date</p>
                                        <div class="flex flex-row gap-3 items-center">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">Start</label>
                                                <input type="date" v-model="saleDateStart" class="w-full border rounded-md p-2 text-sm">
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">End</label>
                                                <input type="date" v-model="saleDateEnd" class="w-full border rounded-md p-2 text-sm">
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

                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Downpayment Issue Date</p>
                                        <div class="flex flex-row gap-3 items-center">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">Start</label>
                                                <input type="date" v-model="downpaymentIssueDateStart" class="w-full border rounded-md p-2 text-sm">
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-0.5">End</label>
                                                <input type="date" v-model="downpaymentIssueDateEnd" class="w-full border rounded-md p-2 text-sm">
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

                <div v-if="saleDateStart || saleDateEnd || dueDateStart || dueDateEnd || downpaymentIssueDateStart || downpaymentIssueDateEnd" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Date Filters:</div>

                    <div v-if="saleDateStart" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Sale From: {{ formatDate(saleDateStart) }}</span>
                        <button @click="saleDateStart = null; fetchSales()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="saleDateEnd" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Sale To: {{ formatDate(saleDateEnd) }}</span>
                        <button @click="saleDateEnd = null; fetchSales()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="dueDateStart" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Due From: {{ formatDate(dueDateStart) }}</span>
                        <button @click="dueDateStart = null; fetchSales()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="dueDateEnd" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Due To: {{ formatDate(dueDateEnd) }}</span>
                        <button @click="dueDateEnd = null; fetchSales()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="downpaymentIssueDateStart" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>DP Issue From: {{ formatDate(downpaymentIssueDateStart) }}</span>
                        <button @click="downpaymentIssueDateStart = null; fetchSales()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="downpaymentIssueDateEnd" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>DP Issue To: {{ formatDate(downpaymentIssueDateEnd) }}</span>
                        <button @click="downpaymentIssueDateEnd = null; fetchSales()" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>


                    <button v-if="saleDateStart || saleDateEnd || dueDateStart || dueDateEnd || downpaymentIssueDateStart || downpaymentIssueDateEnd" @click="resetDateFilter"
                            class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm hover:bg-red-200">
                        Clear All
                    </button>
                </div>

                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">MAIN INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">DP INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">CUSTOMER</th>
                            <th class="px-4 py-3 text-left font-medium">SALE DATE</th>
                            <th class="px-4 py-3 text-left font-medium">GRAND TOTAL</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">PAYMENT</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="n in 5" :key="n" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="w-12 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-24 h-6 bg-gray-300 rounded-md animate-pulse"></div>
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

                <div v-else-if="sales.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Sales</h3>
                    <p class="text-gray-500">There are no deleted sale to restore.</p>
                </div>

                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">MAIN INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">DP INVOICE</th>
                            <th class="px-4 py-3 text-left font-medium">CUSTOMER</th>
                            <th class="px-4 py-3 text-left font-medium">SALE DATE</th>
                            <th class="px-4 py-3 text-left font-medium">GRAND TOTAL</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-4 py-3 text-left font-medium">PAYMENT</th>
                            <th class="px-4 py-3 text-left font-medium">CREATED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in sales" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.invoice_number || 'N/A' }}</td>
                            <td class="px-4 py-3">{{ item.invoice_downpayment_number || 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <div class="w-[480px] truncate" :title="item.customer?.label || ''">
                                    {{ item.customer?.label || 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ formatDate(item.sale_date) }}</td>
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
                                <div class="flex justify-end gap-1 relative">

                                    <!-- Button Restore -->
                                    <button
                                        @click="restoreSales(item.id, item.invoice_number)"
                                        class="p-1.5 bg-lp-green text-white rounded-md hover:bg-green-600 transition-colors">
                                        <restore-icon class="" />
                                    </button>

                                    <button
                                        @click="deleteSale(item.id, item.invoice_number)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon />
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
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { router, usePage } from '@inertiajs/vue3';
import Sidebar from '../Components/Sidebar.vue';
import TrashIcon from '/resources/assets/icons/trash.svg';
import RestoreIcon from "../../assets/icons/restore-white.svg"
import EditSalePopup from "@/Components/EditSalePopup.vue";
import 'jspdf-autotable';


const page = usePage()
const auth = page.props.auth
const sales = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedPaymentStatus = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const showDateFilter = ref(false)
const saleDateStart = ref(null)
const saleDateEnd = ref(null)
const dueDateStart = ref(null);
const dueDateEnd = ref(null);
const downpaymentIssueDateStart = ref(null);
const downpaymentIssueDateEnd = ref(null);
const currentUser = ref(null)
const showEditModal = ref(false)
const editingSaleId = ref(null)


const searchTimeout = ref(null)

const totalShippingCost = ref(0)

const goToAllSales = () => {
    router.visit('/sales/all')
}

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        selectedPaymentStatus: selectedPaymentStatus.value,
        currentPage: currentPage.value,
        saleDateStart: saleDateStart.value,
        saleDateEnd: saleDateEnd.value,
        dueDateStart: dueDateStart.value,
        dueDateEnd: dueDateEnd.value,
        downpaymentIssueDateStart: downpaymentIssueDateStart.value,
        downpaymentIssueDateEnd: downpaymentIssueDateEnd.value
    }
    localStorage.setItem('saleFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('saleFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || ''
            selectedPaymentStatus.value = filterState.selectedPaymentStatus || ''
            currentPage.value = filterState.currentPage || 1
            saleDateStart.value = filterState.saleDateStart || null
            saleDateEnd.value = filterState.saleDateEnd || null
            dueDateStart.value = filterState.dueDateStart || null
            dueDateEnd.value = filterState.dueDateEnd || null
            downpaymentIssueDateStart.value = filterState.downpaymentIssueDateStart || null
            downpaymentIssueDateEnd.value = filterState.downpaymentIssueDateEnd || null
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const restoreSales = async (id, invoiceNumber) => {

        try {
            loading.value = true
            await axios.patch(`/api/sales/${id}/restore`)

            Swal.fire({
                title: 'Restored!',
                text: `Sale ${invoiceNumber} has been restored.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchSales()
        } catch (error) {
            console.error('Error restoring sale:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to restore sale.',
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
        case 'failed':
            return 'bg-gray-400 text-white'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const goToDetail = (id) => {
    saveFilterState()
    localStorage.setItem('saleReferrer', '/sales/all')
    router.visit(`/sales/${id}`)
}

const openEditModal = (id) => {
    editingSaleId.value = id
    showEditModal.value = true
}

const closeEditModal = () => {
    showEditModal.value = false
}

const handleSaleUpdated = () => {
    fetchSales()
}

const goToRestorePage = () => {
    router.visit('/sales/restore')
}

const deleteSale = async (id, invoiceNumber) => {
    const result = await Swal.fire({
        title: 'Delete Permanently',
        html: `Are you sure you want to permanently delete sales <strong>${invoiceNumber}</strong>?<br>This action cannot be undone!`,
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
            await axios.delete(`/api/sales/${id}/force`)

            Swal.fire({
                title: 'Deleted!',
                text: `Sales ${invoiceNumber} has been permanently deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchSales()
        } catch (error) {
            console.error('Error deleting sales permanently:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete sales permanently.',
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

const fetchSales = async () => {
    try {
        loading.value = true

        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            status: selectedStatus.value,
            payment_status: selectedPaymentStatus.value,
            per_page: perPage.value,
        }

        if (saleDateStart.value) {
            params.sale_date_start = saleDateStart.value
        }

        if (saleDateEnd.value) {
            params.sale_date_end = saleDateEnd.value
        }

        if (dueDateStart.value) {
            params.due_date_start = dueDateStart.value
        }

        if (dueDateEnd.value) {
            params.due_date_end = dueDateEnd.value
        }

        if (downpaymentIssueDateStart.value) {
            params.downpayment_issue_date_start = downpaymentIssueDateStart.value
        }

        if (downpaymentIssueDateEnd.value) {
            params.downpayment_issue_date_end = downpaymentIssueDateEnd.value
        }

        const response = await axios.get('/api/sales/trashed', { params })

        const data = response.data
        sales.value = data.data

        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page

        totalShippingCost.value = sales.value.reduce((total, sale) => {
            const saleShippingCost = sale.shipments?.reduce((sum, shipment) => {
                return sum + parseFloat(shipment.shipping_cost || 0)
            }, 0)
            return total + saleShippingCost
        }, 0)


        return sales.value;
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
        fetchSales()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchSales()
    } else {
        handleSearch()
    }
})

const handleStatusChange = () => {
    currentPage.value = 1
    fetchSales()
}

const handlePaymentStatusChange = () => {
    currentPage.value = 1
    fetchSales()
}

const resetDateFilter = () => {
    saleDateStart.value = null
    saleDateEnd.value = null
    dueDateStart.value = null
    dueDateEnd.value = null
    downpaymentIssueDateStart.value = null
    downpaymentIssueDateEnd.value = null
    showDateFilter.value = false
    fetchSales()
}

const applyDateFilter = () => {
    showDateFilter.value = false
    fetchSales()
}


const fetchCurrentUser = async () => {
    try {
        const response = await axios.get('/api/users/current')
        currentUser.value = response.data.data
    } catch (error) {
        console.error('Error fetching user:', error)
        router.visit('/')
    }
}



const clearFilterState = () => {
    localStorage.removeItem('saleFilterState')
}

onMounted( () => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    fetchCurrentUser()
    restoreFilterState()
    fetchSales()
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
        fetchSales()
    }
}
</script>


