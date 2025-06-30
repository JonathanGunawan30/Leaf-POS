<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditStockOpnamePopup
                    :show="showEditModal"
                    :stock-opname-id="editingStockOpnameId"
                    @close="closeEditModal"
                    @updated="handleStockOpnameUpdated"
                />

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
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
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
                                class="absolute z-50 right-0 mt-2 w-96 h-auto bg-white border rounded-lg shadow-lg p-4 transition-all duration-200" >
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-sm font-semibold">Filter by Opname Date</h3>
                                    <button @click="showDateFilter = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-800 font-medium mb-1.5">Opname Date</p>
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
                                    <div class="w-8 h-8 bg-gray-300 rounded-md"></div>
                                    <div class="w-8 h-8 bg-gray-300 rounded-md"></div>
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
                            <td class="px-4 py-3">{{ item.approved_by || 'N/A' }}</td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button
                                        @click="deleteStockOpname(item.id, item.id)"
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

                                    <button
                                        @click="printStockOpname(item.id)"
                                        class="p-1.5 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                                    >
                                        <printer-icon />
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
import PencilIcon from '/resources/assets/icons/pencil.svg'
import RestoreIcon from "../../assets/icons/restore.svg";
import InfoIcon from "../../assets/icons/info.svg";
import PrinterIcon from "../../assets/icons/printer-line.svg";

import EditStockOpnamePopup from "@/Components/EditStockOpnamePopup.vue";
import {jsPDF} from "jspdf";
import 'jspdf-autotable';
import * as XLSX from "xlsx";

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

const showEditModal = ref(false)
const editingStockOpnameId = ref(null)
const printDropdownOpen = ref(null);

const searchTimeout = ref(null)

const refreshData = () => {
    fetchStockOpnames();
};

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        currentPage: currentPage.value,
        startDate: startDate.value,
        endDate: endDate.value,
    }
    localStorage.setItem('stockOpnameFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('stockOpnameFilterState')
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

const goToDetail = (id) => {
    saveFilterState()
    localStorage.setItem('inventoryReferrer', '/stock-opname/all')
    router.visit(`/stock-opname/${id}`)
}

const openEditModal = (id) => {
    editingStockOpnameId.value = id
    showEditModal.value = true
}

const closeEditModal = () => {
    showEditModal.value = false
}

const handleStockOpnameUpdated = () => {
    fetchStockOpnames()
}

const goToRestorePage = () => {
    router.visit('/stock-opname/restore')
}

const deleteStockOpname = async (id, displayId) => {
    const result = await Swal.fire({
        title: 'Delete Stock Opname',
        html: `Are you sure you want to delete stock opname ID <strong>${displayId}</strong>?<br>The stock opname will be moved to the restore page.`,
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
            await axios.delete(`/api/stock-opnames/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `Stock opname ID ${displayId} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchStockOpnames()
        } catch (error) {
            console.error('Error deleting stock opname:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete stock opname.',
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

        const response = await axios.get('/api/stock-opnames', { params })

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
        const response = await axios.get('/api/stock-opnames', {
            params: {
                search: searchQuery.value.trim(),
                status: selectedStatus.value,
                start_date: startDate.value,
                end_date: endDate.value,
                per_page: 1000000
            }
        });

        const allStockOpnames = response.data.data;

        if (allStockOpnames.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no stock opnames to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const doc = new jsPDF({
            orientation: 'portrait'
        });

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Stock Opname Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        let filterInfo = [];
        if (startDate.value) filterInfo.push(`Opname Date From: ${formatDate(startDate.value)}`);
        if (endDate.value) filterInfo.push(`Opname Date To: ${formatDate(endDate.value)}`);
        if (searchQuery.value.trim()) filterInfo.push(`Search: "${searchQuery.value.trim()}"`);
        if (selectedStatus.value) filterInfo.push(`Status: ${selectedStatus.value}`);

        const filterText = filterInfo.length > 0
            ? `Filters: ${filterInfo.join(', ')}`
            : 'All Stock Opnames';

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${formatDate(new Date())}`, 14, 36);
        doc.text(`Total Records: ${allStockOpnames.length}`, 14, 44);

        const headers = [
            'No',
            'User',
            'Opname Date',
            'Status',
            'Notes',
            'Location',
            'Approved By'
        ];

        const tableData = allStockOpnames.map((item, index) => [
            index + 1,
            item.user?.name || 'N/A',
            formatDate(item.opname_date),
            capitalizeFirstLetter(item.status),
            item.notes || 'N/A',
            item.location || 'N/A',
            item.approved_by || 'N/A'
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
                1: { cellWidth: 30 },
                2: { cellWidth: 20 },
                3: { cellWidth: 20 },
                4: { cellWidth: 40 },
                5: { cellWidth: 20 },
                6: { cellWidth: 25 }
            },
            didDrawPage: (data) => {

                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        await loadingSwal.close();

        let fileName = 'Stock_Opname_Report';
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
        html: 'Please wait while we prepare your stock opname report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let stockOpnamesData = [];
        try {
            const response = await axios.get('/api/stock-opnames', {
                params: {
                    search: searchQuery.value.trim(),
                    status: selectedStatus.value,
                    start_date: startDate.value,
                    end_date: endDate.value,
                    with: 'user,approved_by,stock_opname_items.product',
                    per_page: 1000000
                }
            });
            stockOpnamesData = response.data.data;
        } catch (error) {
            console.error('Failed to fetch stock opnames:', error);
            throw new Error('Failed to fetch stock opname data. Please try again.');
        }

        if (stockOpnamesData.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no stock opnames to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const wb = XLSX.utils.book_new();

        const mainHeaders = [
            'No',
            'ID',
            'User',
            'Opname Date',
            'Status',
            'Notes',
            'Location',
            'Approved By',
            'Created At',
            'Updated At',
            'Deleted At',
        ];

        const mainWsData = [
            mainHeaders,
            ...stockOpnamesData.map((item, index) => {
                const approvedByName = item.approved_by ? item.approved_by.name : 'N/A';
                return [
                    index + 1,
                    item.id,
                    item.user?.name || 'N/A',
                    safeFormatDate(item.opname_date),
                    item.status,
                    item.notes || '',
                    item.location || '',
                    approvedByName,
                    safeFormatDate(item.created_at),
                    safeFormatDate(item.updated_at),
                    safeFormatDate(item.deleted_at),
                ];
            })
        ];

        const mainWs = XLSX.utils.aoa_to_sheet(mainWsData);
        XLSX.utils.book_append_sheet(wb, mainWs, 'Stock Opnames Summary');

        if (!mainWs['!cols']) mainWs['!cols'] = [];
        const mainColumnWidths = [5, 10, 20, 15, 15, 40, 20, 20, 20, 20, 20];
        mainColumnWidths.forEach((w, i) => {
            mainWs['!cols'][i] = { wch: w };
        });

        const allItems = [];
        stockOpnamesData.forEach(opname => {
            if (opname.stock_opname_items && opname.stock_opname_items.length > 0) {
                opname.stock_opname_items.forEach(item => {
                    allItems.push({
                        stock_opname_id: opname.id,
                        opname_date: opname.opname_date,
                        product_id: item.product_id,
                        product_name: item.product?.name || 'N/A',
                        system_stock: item.system_stock,
                        actual_stock: item.actual_stock,
                        difference: item.difference,
                        notes: item.notes || '',
                    });
                });
            }
        });

        if (allItems.length > 0) {
            const itemHeaders = [
                'Stock Opname ID',
                'Opname Date',
                'Product ID',
                'Product Name',
                'System Stock',
                'Actual Stock',
                'Difference',
                'Notes',
            ];

            const itemWsData = [
                itemHeaders,
                ...allItems.map(item => [
                    item.stock_opname_id,
                    safeFormatDate(item.opname_date),
                    item.product_id,
                    item.product_name,
                    item.system_stock,
                    item.actual_stock,
                    item.difference,
                    item.notes,
                ])
            ];

            const itemWs = XLSX.utils.aoa_to_sheet(itemWsData);
            XLSX.utils.book_append_sheet(wb, itemWs, 'Stock Opname Items');

            if (!itemWs['!cols']) itemWs['!cols'] = [];
            const itemColumnWidths = [15, 15, 15, 30, 15, 15, 15, 40];
            itemColumnWidths.forEach((w, i) => {
                itemWs['!cols'][i] = { wch: w };
            });
        }


        let fileName = 'Stock_Opname_Report_' + new Date().toISOString().slice(0, 10);
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
    localStorage.removeItem('stockOpnameFilterState')
}


const currentUser = computed(() => {
    return usePage().props.auth.user || { name: 'Admin' };
});

const printStockOpname = async (id) => {

    const stockOpname = stockOpnames.value.find(opname => Number(opname.id) === Number(id));

    if (!stockOpname) {
        Swal.fire('Error', 'Data Stock Opname tidak ditemukan untuk pembuatan PDF.', 'error');
        return;
    }
    const doc = new jsPDF();
    let y = 20;

    doc.setFontSize(16);
    doc.setFont('helvetica', 'bold');
    doc.text('HASIL STOCK OPNAME', 105, y, { align: 'center' });
    y += 10;

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`Lokasi: ${stockOpname.location || 'N/A'}`, 14, y);
    y += 5;
    doc.text(`Tanggal Opname: ${formatDate(stockOpname.opname_date)}`, 14, y);
    y += 15;

    const itemsHeaders = [['No', 'Kode Barang', 'Nama Barang', 'SKU', 'Stock Onhand', 'Hasil Scan', 'Selisih', 'Ket.']];

    const items = stockOpname.stock_opname_items ||
        stockOpname.items ||
        stockOpname.stockOpnameItems ||
        stockOpname.data?.items ||
        [];

    if (!items || items.length === 0) {
        console.log('No items found, trying to fetch from API...');

        try {
            const response = await axios.get(`/api/stock-opnames/${id}`);
            console.log('API Detail Response:', response.data);

            const detailData = response.data.data || response.data;
            const apiItems = detailData.stock_opname_items ||
                detailData.items ||
                [];

            console.log('Items from API:', apiItems);

            if (apiItems && apiItems.length > 0) {

                const itemsData = apiItems.map((item, index) => {
                    console.log(`Processing API item ${index}:`, item);
                    return [
                        index + 1,
                        item.product?.barcode || 'N/A',
                        item.product?.name || item.product_name || '-',
                        item.product?.sku || item.product?.code || item.product_sku || '-',
                        item.system_stock || 0,
                        item.actual_stock || 0,
                        item.difference || 0,
                        item.notes || '-'
                    ];
                });

                generatePDF(doc, itemsHeaders, itemsData, stockOpname, y);
                return;
            }
        } catch (error) {
            console.error('Error fetching detail:', error);
        }
    }

    const itemsData = items.map((item, index) => {
        console.log(`Processing item ${index}:`, item);
        return [
            index + 1,
            item.product?.barcode || 'N/A',
            item.product?.name || item.product_name || '-',
            item.product?.sku || item.product?.code || item.product_sku || '-',
            item.system_stock || 0,
            item.actual_stock || 0,
            item.difference || 0,
            item.notes || '-'
        ];
    });

    console.log('Final items data for table:', itemsData);

    generatePDF(doc, itemsHeaders, itemsData, stockOpname, y);
};

const generatePDF = (doc, itemsHeaders, itemsData, stockOpname, y) => {
    if (itemsData.length === 0) {
        itemsData.push(['No data', 'No items found', '-', '-', '0', '0', '0', '-']);
    }
    doc.autoTable({
        startY: y,
        head: itemsHeaders,
        body: itemsData,
        theme: 'grid',
        styles: { fontSize: 8, cellPadding: 3, overflow: 'linebreak' },
        headStyles: { fillColor: [47, 132, 81], textColor: 255, fontStyle: 'bold' },
        margin: { left: 14, right: 14 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 25 },
            2: { cellWidth: 40 },
            3: { cellWidth: 30 },
            4: { cellWidth: 18},
            5: { cellWidth: 18},
            6: { cellWidth: 18},
            7: { cellWidth: 20}
        },
        tableWidth: 'wrap',
        didDrawPage: (data) => {
            doc.setFontSize(8);
            doc.setTextColor(150);
            const pageCount = doc.internal.getNumberOfPages();
            doc.text(`Halaman ${data.pageNumber} dari ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
        }
    });

    const filename = `Laporan_Stock_Opname_${stockOpname.id}_${new Date().toISOString().slice(0, 10)}.pdf`;
    doc.save(filename);
};


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
