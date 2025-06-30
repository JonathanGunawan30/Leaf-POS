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
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none"
                        >
                            <option value="" class="text-lp-green">All Status</option>
                            <option value="all_except_indent">All except Indent</option>

                            <!-- loop status -->
                            <option
                                v-for="status in statuses"
                                :key="status"
                                :value="status"
                            >
                                {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                            </option>
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

                        <button @click="goToRestorePage"
                                class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
                        </button>

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

                        <button
                            @click="refreshData"
                            :disabled="loading"
                            class="px-5 py-2.5 bg-lp-green text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
                        >
                            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="text-sm">{{ loading ? 'Loading...' : 'Sync' }}</span>
                        </button>
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
                                <div class="flex justify-end gap-1 relative ">
                                    <button
                                        @click="deleteSale(item.id, item.invoice_number)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon />
                                    </button>

                                    <button
                                        @click="openEditModal(item.id)"
                                        class="p-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                        <pencil-icon />
                                    </button>

                                    <button
                                        @click="goToDetail(item.id)"
                                        class="p-2 bg-lp-green text-white rounded-md hover:bg-lp-green-dark transition-colors">
                                        <info-icon />
                                    </button>

                                    <div class="relative">
                                        <button
                                            @click="togglePrintDropdown(item.id)"
                                            class="p-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                            <PrinterIcon />
                                        </button>
                                        <div v-if="activePrintDropdown === item.id"
                                             v-click-outside="() => activePrintDropdown = null"
                                             class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10">
                                            <button
                                                @click="printDocument(item, 'invoice')"
                                                :disabled="!item.invoice_number"
                                                :class="['block w-full text-left px-4 py-2 text-sm',
                                                         item.invoice_number ? 'text-gray-700 hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed']">
                                                Print Invoice
                                            </button>
                                            <button
                                                @click="printDocument(item, 'downpayment')"
                                                :disabled="!item.invoice_downpayment_number"
                                                :class="['block w-full text-left px-4 py-2 text-sm',
                                                         item.invoice_downpayment_number ? 'text-gray-700 hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed']">
                                                Print DP Invoice
                                            </button>
                                            <button
                                                @click="printDocument(item, 'delivery_order')"
                                                :disabled="!item.delivery_number"
                                                :class="['block w-full text-left px-4 py-2 text-sm',
                                                         item.delivery_number ? 'text-gray-700 hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed']">
                                                Print Delivery Order
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
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { router, usePage } from '@inertiajs/vue3';
import Sidebar from '../Components/Sidebar.vue';
import TrashIcon from '/resources/assets/icons/trash.svg';
import PencilIcon from '/resources/assets/icons/pencil.svg';
import RestoreIcon from "../../assets/icons/restore.svg";
import InfoIcon from "../../assets/icons/info.svg";
import PrinterIcon from "../../assets/icons/printer-line.svg";
import EditSalePopup from "@/Components/EditSalePopup.vue";
import { jsPDF } from "jspdf";
import 'jspdf-autotable';
import * as XLSX from "xlsx";
import vClickOutside from 'click-outside-vue3';


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
const activePrintDropdown = ref(null)

const searchTimeout = ref(null)

const totalShippingCost = ref(0)
const statuses= ['indent', 'pending', 'confirmed', 'shipped', 'delivered', 'cancelled']
const vOutside = vClickOutside.directive;

const refreshData = () => {
    fetchSales();
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
        title: 'Delete Sale',
        html: `Are you sure you want to delete sale <strong>${invoiceNumber || ''}</strong>?<br>The sale will be moved to the restore page.`,
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
            await axios.delete(`/api/sales/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `Sale ${invoiceNumber} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchSales()
        } catch (error) {
            console.error('Error deleting sale:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete sale.',
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

        const response = await axios.get('/api/sales', { params })

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

const fetchCurrentUser = async () => {
    try {
        const response = await axios.get('/api/users/current')
        currentUser.value = response.data.data
    } catch (error) {
        console.error('Error fetching user:', error)
        router.visit('/')
    }
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
        const response = await axios.get('/api/sales', {
            params: {
                search: searchQuery.value.trim(),
                status: selectedStatus.value,
                payment_status: selectedPaymentStatus.value,
                sale_date_start: saleDateStart.value,
                sale_date_end: saleDateEnd.value,
                due_date_start: dueDateStart.value,
                due_date_end: dueDateEnd.value,
                downpayment_issue_date_start: downpaymentIssueDateStart.value,
                downpayment_issue_date_end: downpaymentIssueDateEnd.value,
                per_page: 1000000
            }
        });

        const allSales = response.data.data;

        if (allSales.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no sales to export with the current filters.',
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
        doc.text('Sales Report', 14, 20);

        // Filter information
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        let filterInfo = [];
        if (saleDateStart.value) filterInfo.push(`Sale Date From: ${formatDate(saleDateStart.value)}`);
        if (saleDateEnd.value) filterInfo.push(`Sale Date To: ${formatDate(saleDateEnd.value)}`);
        if (dueDateStart.value) filterInfo.push(`Due Date From: ${formatDate(dueDateStart.value)}`);
        if (dueDateEnd.value) filterInfo.push(`Due Date To: ${formatDate(dueDateEnd.value)}`);
        if (downpaymentIssueDateStart.value) filterInfo.push(`DP Issue Date From: ${formatDate(downpaymentIssueDateStart.value)}`);
        if (downpaymentIssueDateEnd.value) filterInfo.push(`DP Issue Date To: ${formatDate(downpaymentIssueDateEnd.value)}`);
        if (searchQuery.value.trim()) filterInfo.push(`Search: "${searchQuery.value.trim()}"`);
        if (selectedStatus.value) filterInfo.push(`Status: ${selectedStatus.value}`);
        if (selectedPaymentStatus.value) filterInfo.push(`Payment Status: ${selectedPaymentStatus.value}`);

        const filterText = filterInfo.length > 0
            ? `Filters: ${filterInfo.join(', ')}`
            : 'All Sales';

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${formatDate(new Date())}`, 14, 36);
        doc.text(`Total Records: ${allSales.length}`, 14, 44);

        const headers = [
            'No',
            'Main Invoice',
            'DP Invoice',
            'Customer',
            'Sale Date',
            'Grand Total',
            'Status',
            'Payment Status',
            'Created By'
        ];

        const tableData = allSales.map((sale, index) => [
            index + 1,
            sale.invoice_number || 'N/A',
            sale.invoice_downpayment_number || 'N/A',
            sale.customer?.label || 'N/A',
            formatDate(sale.sale_date),
            formatCurrency(sale.grand_total),
            capitalizeFirstLetter(sale.status),
            formatPaymentStatus(sale.payment_status),
            sale.user?.name || 'N/A'
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
                1: { cellWidth: 22 },
                2: { cellWidth: 22 },
                3: { cellWidth: 28 },
                4: { cellWidth: 18 },
                5: { cellWidth: 25 },
                6: { cellWidth: 18 },
                7: { cellWidth: 20 },
                8: { cellWidth: 20 }
            },
            didDrawPage: (data) => {

                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        await loadingSwal.close();

        let fileName = 'Sales_Report';
        if (saleDateStart.value) fileName += `_sale_from_${saleDateStart.value}`;
        if (saleDateEnd.value) fileName += `_sale_to_${saleDateEnd.value}`;
        if (dueDateStart.value) fileName += `_due_from_${dueDateStart.value}`;
        if (dueDateEnd.value) fileName += `_due_to_${dueDateEnd.value}`;
        if (downpaymentIssueDateStart.value) fileName += `_dp_issue_from_${downpaymentIssueDateStart.value}`;
        if (downpaymentIssueDateEnd.value) fileName += `_dp_issue_to_${downpaymentIssueDateEnd.value}`;
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
        html: 'Please wait while we prepare your sales report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let sales = [];
        try {
            const response = await axios.get('/api/sales', {
                params: {
                    search: searchQuery.value.trim(),
                    status: selectedStatus.value,
                    payment_status: selectedPaymentStatus.value,
                    sale_date_start: saleDateStart.value,
                    sale_date_end: saleDateEnd.value,
                    due_date_start: dueDateStart.value,
                    due_date_end: dueDateEnd.value,
                    downpayment_issue_date_start: downpaymentIssueDateStart.value,
                    downpayment_issue_date_end: downpaymentIssueDateEnd.value,
                    per_page: 1000000
                }
            });
            sales = response.data.data;
        } catch (error) {
            console.error('Failed to fetch sales for Excel:', error);
            throw new Error('Failed to fetch sales data. Please try again.');
        }

        if (sales.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no sales to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const wb = XLSX.utils.book_new();

        const headers = [
            'No',
            'Main Invoice Number',
            'DP Invoice Number',
            'Invoice Issue Date',
            'DP Issue Date',
            'Delivery Number',
            'Sale Date',
            'Due Date',
            'Total Amount',
            'Total Tax',
            'Total Discount',
            'Grand Total',
            'Status',
            'Payment Status',
            'Customer',
            'Customer Email',
            'Created By',
            'User Role',
            'Product List',
            'Payment List',
            'Shipment Info'
        ];

        const wsData = [
            headers,
            ...sales.map((sale, index) => {
                const products = sale.sale_details?.map(item =>
                    `(${item.quantity} x ${item.product_name} @ ${safeFormatCurrency(item.unit_price)}) = ${safeFormatCurrency(item.sub_total)}${item.note ? ' - ' + item.note : ''}`
                ).join('\n') || '';

                const payments = sale.sale_payments?.map(p =>
                    `${safeFormatDate(p.payment_date)}: ${safeFormatCurrency(p.amount)} (${p.payment_method}) - ${p.note || ''}`
                ).join('\n') || '';

                const shipmentInfo = sale.shipments?.map(s =>
                    `Courier: ${s.courier?.label || 'N/A'}, Vehicle: ${s.vehicle_type} (${s.vehicle_number}), Shipping Date: ${safeFormatDate(s.shipping_date)}, Est. Arrival: ${safeFormatDate(s.estimated_arrival_date)}, Actual Arrival: ${safeFormatDate(s.actual_arrival_date)}, Status: ${s.status}, Cost: ${safeFormatCurrency(s.shipping_cost)}`
                ).join('\n') || '';


                return [
                    index + 1,
                    sale.invoice_number,
                    sale.invoice_downpayment_number || '',
                    safeFormatDate(sale.invoice_issue_date),
                    safeFormatDate(sale.invoice_downpayment_issue_date),
                    sale.delivery_number || '',
                    safeFormatDate(sale.sale_date),
                    safeFormatDate(sale.due_date),
                    safeFormatCurrency(sale.total_amount),
                    safeFormatCurrency(sale.total_tax),
                    safeFormatCurrency(sale.total_discount),
                    safeFormatCurrency(sale.grand_total),
                    sale.status,
                    sale.payment_status,
                    sale.customer?.label || '',
                    sale.customer?.email || '',
                    sale.user?.name || '',
                    sale.user?.role?.name || '',
                    products,
                    payments,
                    shipmentInfo
                ];
            })
        ];

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        XLSX.utils.book_append_sheet(wb, ws, 'Sales');

        if (!ws['!cols']) ws['!cols'] = [];
        const columnWidths = [
            5,
            20,
            20,
            15,
            15,
            20,
            15,
            15,
            20,
            15,
            15,
            20,
            15,
            15,
            30,
            30,
            20,
            20,
            50,
            50,
            70
        ];
        columnWidths.forEach((w, i) => {
            ws['!cols'][i] = { wch: w };
        });

        let fileName = 'Sales_Report_' + new Date().toISOString().slice(0, 10);
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

const togglePrintDropdown = (saleId) => {
    if (activePrintDropdown.value === saleId) {
        activePrintDropdown.value = null;
    } else {
        activePrintDropdown.value = saleId;
    }
}

const printDocument = async (item, type) => {
    activePrintDropdown.value = null;

    const loadingSwal = Swal.fire({
        title: `Generating ${capitalizeFirstLetter(type).replace(/_/g, ' ')}`,
        html: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let fileName = 'document';
        switch (type) {
            case 'invoice':
                await generateInvoicePDF(item);
                fileName = `Invoice_${item.invoice_number || 'N/A'}`;
                break;
            case 'downpayment':
                await generateDownpaymentInvoicePDF(item);
                fileName = `DP_Invoice_${item.invoice_downpayment_number || 'N/A'}`;
                break;
            case 'delivery_order':
                await generateDeliveryNotePDF(item);
                fileName = `Delivery_Order${item.delivery_number || 'N/A'}`;
                break;
            default:
                throw new Error('Unknown document type');
        }
        loadingSwal.close();
        Swal.fire('Success', `${capitalizeFirstLetter(type).replace(/_/g, ' ')} generated successfully!`, 'success');

    } catch (error) {
        console.error(`Error generating ${type}:`, error);
        loadingSwal.close();
        Swal.fire('Error', `Failed to generate ${capitalizeFirstLetter(type).replace(/_/g, ' ')}. ${error.message || ''}`, 'error');
    }
};

const fetchSaleDetailForPrint = async (saleId) => {
    try {
        const response = await axios.get(`/api/sales/${saleId}`);
        return response.data.data;
    } catch (error) {
        console.error('Error fetching sale detail for print:', error);
        throw new Error('Could not fetch sale details for printing.');
    }
}

const generateInvoicePDF = async (saleItem) => {
    const sale = await fetchSaleDetailForPrint(saleItem.id);
    const companyName = import.meta.env.VITE_COMPANY_NAME || 'Nama Perusahaan';

    const doc = new jsPDF();
    let y = 20;

    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('INVOICE', 105, y, { align: 'center' });
    y += 10;

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`NO: ${sale.invoice_number || 'N/A'}`, 105, y, { align: 'center' });
    y += 10;

    doc.text('Customer:', 14, y);
    y += 5;

    let leftY = y;
    let rightY = y;

    doc.text('Nama', 18, leftY);
    doc.text(`: ${sale.customer?.name || 'N/A'}`, 45, leftY);
    doc.text('Tanggal', 130, rightY);
    doc.text(`: ${formatDate(sale.invoice_issue_date)}`, 160, rightY);
    leftY += 5;
    rightY += 5;

    doc.text('Email', 18, leftY);
    doc.text(`: ${sale.customer?.email || 'N/A'}`, 45, leftY);
    doc.text('No Order', 130, rightY);
    doc.text(`: ${sale.id || 'N/A'}`, 160, rightY);
    leftY += 5;
    rightY += 5;

    doc.text('Perusahaan', 18, leftY);
    doc.text(`: ${sale.customer?.company_name || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('Alamat', 18, leftY);
    doc.text(`: ${sale.customer?.address || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('Kota', 18, leftY);
    doc.text(`: ${sale.customer?.city || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('Telepon', 18, leftY);
    doc.text(`: ${sale.customer?.phone || 'N/A'}`, 45, leftY);
    leftY += 10;

    doc.text('Bank', 18, leftY);
    doc.text(`: ${sale.customer?.bank_name || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('No. Rekening', 18, leftY);
    doc.text(`: ${sale.customer?.bank_account || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('No. SIUP', 18, leftY);
    doc.text(`: ${sale.customer?.siup_number || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('No. NIB', 18, leftY);
    doc.text(`: ${sale.customer?.nib_number || 'N/A'}`, 45, leftY);
    leftY += 5;

    doc.text('No. NPWP', 18, leftY);
    doc.text(`: ${sale.customer?.npwp_number || 'N/A'}`, 45, leftY);

    y = leftY + 10;


    const productsHeaders = [['Nama Barang', 'Jumlah', 'Satuan', 'Harga', 'Total']];
    const productsData = sale.sale_details.map(detail => [
        detail.product.name || 'N/A',
        detail.quantity,
        detail.product.unit.code || 'N/A',
        formatCurrency(detail.unit_price),
        formatCurrency(detail.sub_total)
    ]);

    doc.autoTable({
        startY: y,
        head: productsHeaders,
        body: productsData,
        theme: 'grid',
        styles: { fontSize: 9, cellPadding: 2, overflow: 'linebreak' },
        headStyles: { fillColor: [200, 200, 200], textColor: [0, 0, 0], fontStyle: 'bold' },
        columnStyles: {
            0: { cellWidth: 70 },
            1: { cellWidth: 20 },
            2: { cellWidth: 20, halign: 'right' },
            3: { cellWidth: 30, halign: 'right' },
            4: { cellWidth: 40, halign: 'right' }
        },
        didDrawPage: (data) => {
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(`Page ${data.pageNumber}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
        }
    });

    y = doc.autoTable.previous.finalY + 10;

    const totalShippingCost = (sale.shipments || []).reduce((sum, shipment) => {
        return sum + parseFloat(shipment.shipping_cost || 0);
    }, 0);

    const summaryData = [
        ['Sub Total', formatCurrency(sale.total_amount)],
        ['Discount', formatCurrency(sale.total_discount)],
        ['Shipping Amount', formatCurrency(totalShippingCost)],
        ['Total Tax', formatCurrency(sale.total_tax)],
        ['Grand Total', formatCurrency(sale.grand_total)]
    ];

    doc.autoTable({
        startY: y,
        body: summaryData,
        theme: 'plain',
        styles: { fontSize: 10, cellPadding: 2, overflow: 'linebreak' },
        columnStyles: {
            0: { cellWidth: 140, fontStyle: 'bold', halign: 'right' },
            1: { cellWidth: 40, halign: 'right' }
        },
        didParseCell: (data) => {
            if (data.row.index === summaryData.length - 1) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [240, 240, 240];
            }
        }
    });

    y = doc.autoTable.previous.finalY + 30;

    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20;
    const center = pageWidth / 2;

    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text(sale.customer?.company_name || 'Customer Company', margin, y);
    doc.setFont('helvetica', 'normal');
    doc.text('(Perwakilan Pembelian)', margin, y + 30);

    doc.setFont('helvetica', 'bold');
    doc.text(companyName, pageWidth - margin, y, { align: 'right' });
    doc.setFont('helvetica', 'normal');
    doc.text('(Perwakilan Penjualan)', pageWidth - margin, y + 30, { align: 'right' });

    doc.save(`Invoice_${sale.invoice_number || 'Sale'}.pdf`);
};

const generateDownpaymentInvoicePDF = async (saleItem) => {
    const sale = await fetchSaleDetailForPrint(saleItem.id);

    const companyName = import.meta.env.VITE_COMPANY_NAME || 'Nama Perusahaan';
    const operatingUnit = import.meta.env.VITE_OPERATING_UNIT || 'N/A';
    const termOfPayment = import.meta.env.VITE_TERM_OF_PAYMENT || 'N/A';
    const bankName = import.meta.env.VITE_BANK_NAME || 'N/A';
    const bankAccount = import.meta.env.VITE_BANK_ACCOUNT || 'N/A';

    const doc = new jsPDF();
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20;

    let leftY = 35;
    let rightY = 35;

    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('PERMOHONAN PEMBAYARAN', pageWidth / 2, 20, { align: 'center' });

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`NO: ${sale.invoice_downpayment_number || 'N/A'}`, pageWidth / 2, 28, { align: 'center' });

    const leftX = margin;
    const rightX = pageWidth / 2 + 10;

    doc.setFontSize(10);
    doc.setFont('helvetica', 'bold');
    doc.text('Customer:', leftX, leftY);
    doc.text('Diajukan Oleh:', rightX, rightY);
    leftY += 5;
    rightY += 5;

    doc.setFont('helvetica', 'normal');

    const leftInfo = [
        ['Nama', sale.customer?.name],
        ['Email', sale.customer?.email],
        ['No Order', sale.id],
        ['Perusahaan', sale.customer?.company_name],
        ['Alamat', sale.customer?.address],
        ['Kota', sale.customer?.city],
        ['Telepon', sale.customer?.phone],
        ['Bank', sale.customer?.bank_name],
        ['No. Rekening', sale.customer?.bank_account],
        ['No. SIUP', sale.customer?.siup_number],
        ['No. NIB', sale.customer?.nib_number],
        ['No. NPWP', sale.customer?.npwp_number],
    ];

    const rightInfo = [
        ['Nama Perusahaan', companyName],
        ['Operating Unit', operatingUnit],
        ['Term of Payment', termOfPayment],
        ['Bank Name', bankName],
        ['Bank Account', bankAccount],
    ];

    leftInfo.forEach(([label, value]) => {
        doc.text(label, leftX + 4, leftY);
        doc.text(`: ${value || 'N/A'}`, leftX + 30, leftY);
        leftY += 5;
    });

    rightInfo.forEach(([label, value]) => {
        doc.text(label, rightX + 4, rightY);
        doc.text(`: ${value || 'N/A'}`, rightX + 40, rightY);
        rightY += 5;
    });

    let y = Math.max(leftY, rightY) + 5;

    const productHeaders = [['Nama Barang', 'Jumlah', 'Satuan', 'Harga', 'Total']];
    const productData = sale.sale_details.map(item => [
        item.product.name,
        item.quantity,
        item.product.unit.code,
        formatCurrency(item.unit_price),
        formatCurrency(item.sub_total)
    ]);

    doc.autoTable({
        startY: y,
        head: [['Nama Barang', 'Jumlah', 'Satuan', 'Harga', 'Total']],
        body: sale.sale_details.map(item => [
            item.product.name,
            item.quantity,
            item.product.unit.code,
            formatCurrency(item.unit_price),
            formatCurrency(item.sub_total)
        ]),
        theme: 'grid',
        headStyles: {
            fillColor: [220, 220, 220],
            textColor: 20,
            fontStyle: 'bold',
        },
        styles: {
            fontSize: 9,
            cellPadding: 2,
        },
        columnStyles: {
            0: { cellWidth: 70 },
            1: { cellWidth: 20 },
            2: { cellWidth: 20, halign: 'right' },
            3: { cellWidth: 30, halign: 'right' },
            4: { cellWidth: 40, halign: 'right' }
        }
    });


    y = doc.autoTable.previous.finalY + 10;

    const totalShippingCost = (sale.shipments || []).reduce((sum, s) => sum + parseFloat(s.shipping_cost || 0), 0);

    const summary = [
        ['Sub Total', formatCurrency(sale.total_amount)],
        ['Discount', formatCurrency(sale.total_discount)],
        ['Shipping Amount', formatCurrency(totalShippingCost)],
        ['Total Tax', formatCurrency(sale.total_tax)],
        ['Grand Total', formatCurrency(sale.grand_total)]
    ];

    doc.autoTable({
        startY: y,
        body: summary,
        theme: 'plain',
        styles: { fontSize: 10 },
        columnStyles: {
            0: { cellWidth: 140, halign: 'right', fontStyle: 'bold' },
            1: { cellWidth: 40, halign: 'right' }
        },
        didParseCell: (data) => {
            if (data.row.index === summary.length - 1) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [240, 240, 240];
            }
        }
    });

    y = doc.autoTable.previous.finalY + 30;

    const signatureLabels = ['Dibuat oleh', 'Diperiksa oleh', 'Disetujui oleh', 'Diterima oleh'];
    const colWidth = (pageWidth - 2 * margin) / 4;
    const signatureText = ['(Tanda tangan dan', 'Nama jelas)']; // pisah manual untuk kontrol posisi
    const centerAlign = { align: 'center' };

    signatureLabels.forEach((label, i) => {
        const x = margin + (i + 0.5) * colWidth;
        doc.setFont('helvetica', 'bold');
        doc.text(label, x, y, centerAlign);

        doc.setFont('helvetica', 'normal');
        doc.text(signatureText, x, y + 28, {
            ...centerAlign,
            lineHeightFactor: 1.2
        });
    });

    const printedAt = new Date();
    const printedDate = `${printedAt.getDate().toString().padStart(2, '0')}/${(printedAt.getMonth() + 1).toString().padStart(2, '0')}/${printedAt.getFullYear()}`;
    const printedBy = computed(() => currentUser.value?.name || 'N/A')


    doc.setFontSize(8);
    doc.setTextColor(100);
    doc.text(`Tanggal Cetak: ${printedDate}`, margin, doc.internal.pageSize.height - 20);
    doc.text(`Dicetak oleh: ${printedBy.value}`, margin, doc.internal.pageSize.height - 15);
    doc.text(`Halaman 1 dari 1`, pageWidth - margin, doc.internal.pageSize.height - 15, { align: 'right' });

    doc.save(`Permohonan_Pembayaran_${sale.invoice_number || 'N/A'}.pdf`);
};

const generateDeliveryNotePDF = async (saleItem) => {
    const sale = await fetchSaleDetailForPrint(saleItem.id);

    const doc = new jsPDF();
    let y = 20;

    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('SURAT JALAN', 105, y, { align: 'center' });
    y += 10;
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`NO: ${sale.delivery_number || 'N/A'}`, 105, y, { align: 'center' });
    y += 20;

    doc.setFontSize(10);
    doc.text('From:', 14, y);
    doc.text(import.meta.env.VITE_COMPANY_NAME || 'PT Ambasing', 18, y + 5);
    doc.text(import.meta.env.SHIPPING_ORIGIN || 'Karawaci Tangerang', 18, y + 10);

    y += 25;

    doc.text('To:', 115, y - 20);
    doc.text(`Customer: ${sale.customer?.name || 'N/A'}`, 124, y - 20);
    doc.text(`Perusahaan: ${sale.customer?.company_name || 'N/A'}`, 124, y - 15);
    doc.text(`Address: ${sale.customer?.address || 'N/A'}`, 124, y - 10);
    doc.text(`City: ${sale.customer?.city || 'N/A'}`, 124, y - 5);
    doc.text(`Phone: ${sale.customer?.phone || 'N/A'}`, 124, y);
    y += 10;

    doc.text('Delivery Date:', 14, y);
    doc.text(`${formatDate(sale.shipments?.[0]?.shipping_date || sale.sale_date)}`, 45, y); // Use shipment date if available, else sale date
    doc.text('Order No:', 120, y);
    doc.text(`${sale.id || 'N/A'}`, 140, y);
    y += 10;

    const itemsHeaders = [['No', 'Nama Barang', 'Jumlah', 'Satuan', 'Catatan']];
    const itemsData = sale.sale_details.map((detail, index) => [
        index + 1,
        detail.product.name || 'N/A',
        detail.quantity,
        detail.product.unit.code || 'N/A',
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
    doc.text('Shipped By:', 150, y);
    y += 20;
    doc.text('__________________', 23, y);
    doc.text('__________________', 143, y);
    y += 5;
    doc.text(`(Nama Penerima)`, 27, y);
    doc.text('(Nama Kurir)', 150, y);

    doc.save(`Delivery_Order_${sale.delivery_number || 'Sale'}.pdf`);
};


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


