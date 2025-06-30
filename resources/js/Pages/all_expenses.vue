<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditExpensePopup
                    :show="showEditModal"
                    :expense-id="editingExpenseId"
                    @close="closeEditModal"
                    @updated="handleExpenseUpdated"
                />

                <!-- Search and Filter Header -->
                <div class="flex justify-between items-center mb-6">
                    <!-- KIRI: Search -->
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Expense"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <!-- KANAN: Activate Button dan Filter -->
                    <div class="flex gap-3 items-center">

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

                        <button @click="goToRestorePage"
                                class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
                        </button>

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
                <!-- Active Filters Display -->
                <div v-if="isFilterActive" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Filters:</div>

                    <div v-if="startDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>Start Date: {{ formatDate(startDate) }}</span>
                        <button @click="clearFilter('startDate')" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <div v-if="endDate" class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1">
                        <span>End Date: {{ formatDate(endDate) }}</span>
                        <button @click="clearFilter('endDate')" class="text-gray-500 hover:text-red-500">
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




                <!-- Price Filter Modal -->
                <div v-show="showFilterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out" :class="{ 'opacity-0': !showFilterModal, 'opacity-100': showFilterModal }">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg transition-transform duration-300 ease-in-out transform" :class="{ 'scale-90 opacity-0': !showFilterModal, 'scale-100 opacity-100': showFilterModal }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Filter Expenses</h3>
                            <button @click="showFilterModal = false" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- expense date range -->
                            <div>
                                <h4 class="font-medium mb-2">Expense Date Range</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Start Date</label>
                                        <input
                                            v-model="startDate"
                                            type="date"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">End Date</label>
                                        <input
                                            v-model="endDate"
                                            type="date"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                        >
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



                </div>



                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">DESCRIPTION</th>
                            <th class="px-4 py-3 text-left font-medium">AMOUNT</th>
                            <th class="px-4 py-3 text-left font-medium">EXPENSE DATE</th>
                            <th class="px-4 py-3 text-left font-medium">CATEGORY</th>
                            <th class="px-4 py-3 text-left font-medium">CREATED BY</th>
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

                <!-- Users Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">DESCRIPTION</th>
                            <th class="px-4 py-3 text-left font-medium">AMOUNT</th>
                            <th class="px-4 py-3 text-left font-medium">EXPENSE DATE</th>
                            <th class="px-4 py-3 text-left font-medium">CATEGORY</th>
                            <th class="px-4 py-3 text-left font-medium">CREATED BY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in expenses" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.description }}</td>
                            <td class="px-4 py-3">
                                {{ new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(item.amount) }}
                            </td>
                            <td class="px-4 py-3">{{item.expense_date}}</td>
                            <td class="px-4 py-3">{{item.category.name}}</td>
                            <td class="px-4 py-3">{{item.user.name}}</td>

                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <!-- Button Delete -->
                                    <button
                                        @click="deleteExpense(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <!-- Button Edit -->
                                    <button
                                        @click="openEditModal(item.id)"
                                        class="p-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                                    >
                                        <pencil-icon class="" />
                                    </button>

                                    <!-- Button Info -->
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
import {ref, onMounted, computed, watch, reactive} from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import RestoreIcon from "../../assets/icons/restore.svg";
import InfoIcon from "../../assets/icons/info.svg";
import { jsPDF } from 'jspdf';
import 'jspdf-autotable';
import * as XLSX from 'xlsx';
import EditExpensePopup from "../Components/EditExpensePopup.vue";

const page = usePage()
const auth = page.props.auth
const expenses = ref([])

const loading = ref(true)
const searchQuery = ref('')
const searchTimeout = ref(null)


const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const showEditModal = ref(false);
const editingExpenseId = ref(null);
const showFilterModal = ref(false)
const startDate = ref('')
const endDate = ref('')

const isFilterActive = computed(() => {
    return startDate.value !== '' || endDate.value !== ''
})

const refreshData = () => {
    fetchExpenses();
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const openEditModal = (id) => {
    editingExpenseId.value = id;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
}

const handleExpenseUpdated = () => {
    fetchExpenses()
};
const goToDetail = (id) => {
    localStorage.setItem('expenseReferrer', `/expenses/all`)
    router.visit(`/expenses/${id}`)
}

const goToRestorePage = () => {
    localStorage.setItem('expenseReferrer', '/expenses/restore')
    router.visit('/expenses/restore')
}
const filters = ref({
    year: '',
    month: 'all',
    category_id: '',
    user_id: '',
})

const clearFilter = (filterType) => {
    if (filterType === 'startDate') {
        startDate.value = ''
    } else if (filterType === 'endDate') {
        endDate.value = ''
    }
    fetchExpenses()
}

const applyFilters = () => {
    currentPage.value = 1
    fetchExpenses()
    showFilterModal.value = false
}

const resetFilters = () => {
    startDate.value = ''
    endDate.value = ''
    currentPage.value = 1
    fetchExpenses()
    showFilterModal.value = false
}



const deleteExpense = async (id, expenseName) => {
    const result = await Swal.fire({
        title: 'Delete Expense',
        html: `Are you sure you want to delete <strong>${expenseName}</strong>?<br>The expense will be moved to the restore page.`,
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
            await axios.delete(`/api/expenses/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `${expenseName} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchExpenses()
        } catch (error){
            console.error('Error deleting expense: ', error)

            Swal.fire({
                title: 'Error',
                text: error.response?.data?.errors?.message || 'Failed to delete expense.',
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
const fetchExpenses = async () => {
    try {
        loading.value = true
        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            per_page: perPage.value,
            start_date: startDate.value,
            end_date: endDate.value
        }

        // Remove empty parameters
        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key]
            }
        })

        const response = await axios.get('/api/expenses', { params })

        const data = response.data
        expenses.value = data.data

        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page
    } catch (error) {
        console.error('API Error:', {
            status: error.response?.status,
            data: error.response?.data,
            headers: error.response?.headers
        })

        if (error.response?.status === 401) {
            localStorage.removeItem('X-API-TOKEN')
            router.visit('/')
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
        fetchExpenses()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchExpenses()
    } else {
        handleSearch()
    }
})
const fetchAllExpenses = async () => {
    try {
        const params = {
            search: searchQuery.value.trim(),
            start_date: startDate.value,
            end_date: endDate.value,
            per_page: 999999
        }

        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key]
            }
        })

        const response = await axios.get('/api/expenses', { params })
        return response.data.data || response.data
    } catch (error) {
        console.error('Error fetching all expenses:', error)
        throw error
    }
}

const safeFormatDate = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    } catch (e) {
        console.error('Date formatting error:', e);
        return dateString;
    }
};

const safeFormatCurrency = (amount) => {
    try {
        if (amount === null || amount === undefined) return 'IDR 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    } catch (e) {
        console.error('Currency formatting error:', e);
        return `IDR ${amount || 0}`;
    }
};

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
        let allExpenses = [];
        try {
            const response = await fetchAllExpenses();
            allExpenses = Array.isArray(response) ? response : [];
        } catch (error) {
            console.error('Failed to fetch expenses:', error);
            throw new Error('Failed to fetch expense data. Please try again.');
        }

        if (allExpenses.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no expenses to export with the current filters.',
                icon: 'info'
            });
            return;
        }

        const doc = new jsPDF({
            orientation: 'portrait'
        });

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Expense Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        let filterInfo = [];
        if (startDate.value) filterInfo.push(`From: ${safeFormatDate(startDate.value)}`);
        if (endDate.value) filterInfo.push(`To: ${safeFormatDate(endDate.value)}`);
        if (searchQuery.value.trim()) filterInfo.push(`Search: "${searchQuery.value.trim()}"`);

        const filterText = filterInfo.length > 0
            ? `Filters: ${filterInfo.join(', ')}`
            : 'All Expenses';

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${safeFormatDate(new Date())}`, 14, 36);
        doc.text(`Total Records: ${allExpenses.length}`, 14, 44);

        const headers = [
            'No',
            'Description',
            'Amount',
            'Date',
            'Category',
            'Created By'
        ];

        const tableData = allExpenses.map((expense, index) => [
            index + 1,
            expense.description || '-',
            safeFormatCurrency(expense.amount),
            safeFormatDate(expense.expense_date),
            expense.category?.name || '-',
            expense.user?.name || '-'
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
                1: { cellWidth: 40 },
                2: { cellWidth: 25 },
                3: { cellWidth: 25 },
                4: { cellWidth: 25 },
                5: { cellWidth: 40 }
            },
            didDrawPage: (data) => {
                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(`Page ${data.pageNumber} of ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
            }
        });

        await loadingSwal.close();

        let fileName = 'Expense_Report';
        if (startDate.value) fileName += `_from_${startDate.value}`;
        if (endDate.value) fileName += `_to_${endDate.value}`;
        fileName += `_${new Date().toISOString().slice(0, 10)}`;

        doc.save(`${fileName}.pdf`);

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();

        await Swal.fire({
            title: 'Export Failed',
            text: error.message || 'An error occurred while generating the PDF.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const exportToExcel = async () => {
    const loadingSwal = Swal.fire({
        title: 'Preparing Excel',
        html: 'Please wait while we prepare your report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        let allExpenses = [];
        try {
            const response = await fetchAllExpenses();
            allExpenses = Array.isArray(response) ? response : [];
        } catch (error) {
            console.error('Failed to fetch expenses:', error);
            throw new Error('Failed to fetch expense data. Please try again.');
        }

        if (allExpenses.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no expenses to export with the current filters.',
                icon: 'info'
            });
            return;
        }


        const wb = XLSX.utils.book_new();

        const headers = [
            'No',
            'Description',
            'Amount (IDR)',
            'Formatted Amount',
            'Expense Date',
            'Category',
            'Created By',
            'User Role',
            'Note'
        ];

        const wsData = [
            headers,
            ...allExpenses.map((expense, index) => [
                index + 1,
                expense.description || '',
                expense.amount || 0,
                safeFormatCurrency(expense.amount),
                safeFormatDate(expense.expense_date),
                expense.category?.name || '',
                expense.user?.name || '',
                expense.user?.role?.name || '',
                expense.note || '',
                safeFormatDate(expense.created_at)
            ])
        ];

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        XLSX.utils.book_append_sheet(wb, ws, 'Expenses');



        if (!ws['!cols']) ws['!cols'] = [];
        const columnWidths = [5, 30, 15, 20, 15, 20, 20, 15, 30, 20];
        columnWidths.forEach((w, i) => {
            ws['!cols'][i] = { wch: w };
        });

        let fileName = 'Expense_Report';
        if (startDate.value) fileName += `_from_${startDate.value}`;
        if (endDate.value) fileName += `_to_${endDate.value}`;
        fileName += `_${new Date().toISOString().slice(0, 10)}`;

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

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    fetchExpenses()
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
        fetchExpenses()
    }
}


</script>
