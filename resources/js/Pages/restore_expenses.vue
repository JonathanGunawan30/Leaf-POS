<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

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

                        <button
                            @click="goToAllExpenses"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Expenses</p>
                        </button>

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

                <div v-else-if="expenses.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Expenses</h3>
                    <p class="text-gray-500">There are no deleted expenses to restore.</p>
                </div>

                <!-- Expenses Table -->
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
                                        @click="hardDeleteExpense(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <!-- Button Restore -->
                                    <button
                                        @click="restoreExpense(item.id)"
                                        class="p-1.5 bg-lp-green text-white rounded-md  transition-colors"
                                    >
                                        <RestoreIcon class=""/>
                                    </button>

                                    <!-- Button Info -->
                                    <button @click="goToDetail(item.id)" class="p-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                                        <info-icon class="" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="expenses.length > 0" class="flex justify-between items-center mt-4">
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
import RestoreIcon from '/resources/assets/icons/restore-white.svg'
import InfoIcon from "../../assets/icons/info.svg";

const page = usePage()
const auth = page.props.auth
const expenses = ref([])

const loading = ref(true)
const searchQuery = ref('')
const searchTimeout = ref(null)


const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const goToAllExpenses = () => {
    router.visit('/expenses/all')
}
const goToDetail = (id) => {
    localStorage.setItem('expenseReferrer', `/expenses/restore`)
    router.visit(`/expenses/${id}`)
}

const restoreExpense = async (id) => {
    try {
        loading.value = true
        await axios.patch(`/api/expenses/${id}/restore`)

        Swal.fire({
            title: 'Success!',
            text: 'Expense has been restored successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        fetchExpenses()
    }catch (error){
        console.error('Error restoring expense: ', error)

        let errorMessage = 'Failed to restore expense. Please check the data or re-login.';
        let useHtml = false;

        if (error.response?.data?.errors?.message) {
            const messages = error.response.data.errors.message;
            const formattedErrors = Object.entries(messages)
                .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                .join('<br>');

            if (formattedErrors) {
                errorMessage = formattedErrors;
                useHtml = true;
            }
        }

        Swal.fire({
            title: 'Failed!',
            html: useHtml ? errorMessage : undefined,
            text: useHtml ? undefined : errorMessage,
            icon: 'error'
        });
    } finally {
        loading.value = false
    }
}
const hardDeleteExpense = async (id, expenseName) => {
    const result = await Swal.fire({
        title: 'Permanently Delete Expense',
        html: `<div class="text-left">
            <p class="mb-2">Are you sure you want to <strong>permanently delete</strong> <strong>${expenseName}</strong>?</p>
            <p class="text-red-600 font-bold">Warning: This action cannot be undone!</p>
            <p>The expense will be permanently removed from the system and cannot be recovered.</p>
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
            await axios.delete(`/api/expenses/${id}/force`)

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

            let errorMessage = 'Failed to hard delete expense. Please check the data or re-login.';
            let useHtml = false;

            if (error.response?.data?.errors?.message) {
                const messages = error.response.data.errors.message;
                const formattedErrors = Object.entries(messages)
                    .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                    .join('<br>');

                if (formattedErrors) {
                    errorMessage = formattedErrors;
                    useHtml = true;
                }
            }

            Swal.fire({
                title: 'Failed!',
                html: useHtml ? errorMessage : undefined,
                text: useHtml ? undefined : errorMessage,
                icon: 'error'
            });
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
        }

        const response = await axios.get('/api/expenses/trashed', {params})

        const data = response.data
        expenses.value = data.data

        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page
    } catch (error) {
        console.error('API Error:', {
            status: error.response.status,
            data: error.response.data,
            headers: error.response.headers
        })

        if (error.response.status === 401) {
            localStorage.removeItem('X-API-TOKEN')
            router.visit('/')
        }else if (error.request) {
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


const exportToPDF = async () => {
    Swal.fire({
        title: 'Exporting...',
        text: 'Preparing your PDF file',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    })

    const allCustomers = await fetchAllCustomers()

    let customersToExport = allCustomers
    if (locationFilter.value === 'jabodetabek') {
        customersToExport = allCustomers.filter(customer => {
            return jabodetabekCities.some(city =>
                customer.city && customer.city.toLowerCase().includes(city)
            )
        })
    } else if (locationFilter.value === 'non-jabodetabek') {
        customersToExport = allCustomers.filter(customer => {
            return !jabodetabekCities.some(city =>
                customer.city && customer.city.toLowerCase().includes(city)
            )
        })
    }

    const doc = new jsPDF()

    doc.setFontSize(18)
    doc.text('Customer List', 14, 22)

    doc.setFontSize(11)
    let filterText = 'Filter: '
    if (locationFilter.value === 'all') {
        filterText += 'All Locations'
    } else if (locationFilter.value === 'jabodetabek') {
        filterText += 'Jabodetabek Area'
    } else if (locationFilter.value === 'non-jabodetabek') {
        filterText += 'Non-Jabodetabek Area'
    }
    doc.text(filterText, 14, 30)

    const today = new Date()
    const dateStr = today.toLocaleDateString()
    doc.text(`Date: ${dateStr}`, 14, 38)

    const tableColumn = [
        "No", "Name", "Company", "Email", "Phone", "City"
    ];
    const tableRows = []

    customersToExport.forEach((item, index) => {
        const rowData = [
            index + 1,
            item.name,
            item.company_name,
            item.email,
            item.phone,
            item.city
        ]
        tableRows.push(rowData)
    })

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 45,
        styles: {
            fontSize: 10,
            cellPadding: 3,
            overflow: 'linebreak'
        },
        columnStyles: {
            0: { cellWidth: 12 },
            1: { cellWidth: 30 },
            2: { cellWidth: 30 },
            3: { cellWidth: 50 },
            4: { cellWidth: 30 },
            5: { cellWidth: 25 }
        }
    })

    Swal.close()

    doc.save('customer-list.pdf')
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

    const allCustomers = await fetchAllCustomers()

    let customersToExport = allCustomers
    if (locationFilter.value === 'jabodetabek') {
        customersToExport = allCustomers.filter(customer => {
            return jabodetabekCities.some(city =>
                customer.city && customer.city.toLowerCase().includes(city)
            )
        })
    } else if (locationFilter.value === 'non-jabodetabek') {
        customersToExport = allCustomers.filter(customer => {
            return !jabodetabekCities.some(city =>
                customer.city && customer.city.toLowerCase().includes(city)
            )
        })
    }

    const worksheet = XLSX.utils.aoa_to_sheet([[
        "No", "Name", "Company", "Email", "Phone", "Address", "City", "Province",
        "Postal Code", "Country", "Bank Account", "Bank Name", "NPWP",
        "SIUP", "NIB", "Business Type", "Note"
    ]])

    customersToExport.forEach((item, index) => {
        XLSX.utils.sheet_add_aoa(worksheet, [[
            index + 1,
            item.name || '',
            item.company_name || '',
            item.email || '',
            item.phone || '',
            item.address || '',
            item.city || '',
            item.province || '',
            item.postal_code || '',
            item.country || '',
            item.bank_account || '',
            item.bank_name || '',
            item.npwp_number || '',
            item.siup_number || '',
            item.nib_number || '',
            item.business_type || '',
            item.note || ''
        ]], { origin: -1 })
    })


    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, "Customers")

    let fileName = 'customer-list'
    if (locationFilter.value === 'jabodetabek') {
        fileName = 'jabodetabek-customers'
    } else if (locationFilter.value === 'non-jabodetabek') {
        fileName = 'non-jabodetabek-customers'
    }

    Swal.close()

    XLSX.writeFile(workbook, `${fileName}.xlsx`)
}

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
