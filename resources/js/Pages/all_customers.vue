<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditCustomerPopup
                    :show="showEditPopup"
                    :customer-id="selectedCustomerId"
                    @close="closeEditPopup"
                    @updated="handleCustomerUpdated"
                />

                <!-- Search and Filter Header -->
                <div class="flex justify-between items-center mb-6">
                    <!-- KIRI: Search -->
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Customer"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <!-- KANAN: Activate Button dan Filter -->
                    <div class="flex gap-3 items-center">
                        <!-- Filter dropdown -->
                        <div class="relative">
                            <select
                                v-model="locationFilter"
                                @change="handleFilterChange"
                                class="px-4 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                            >
                                <option value="all">All Locations</option>
                                <option value="jabodetabek">Jabodetabek</option>
                                <option value="non-jabodetabek">Non-Jabodetabek</option>
                            </select>
                        </div>

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
                            @click="goToAddPage"
                            class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                        >
                            <i class="ri-add-line"></i>
                            Add Customer
                        </button>

                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">NAME</th>
                            <th class="px-4 py-3 text-left font-medium">COMPANY NAME</th>
                            <th class="px-4 py-3 text-left font-medium">EMAIL</th>
                            <th class="px-4 py-3 text-left font-medium">PHONE</th>
                            <th class="px-4 py-3 text-left font-medium">CITY</th>
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
                            <th class="px-4 py-3 text-left font-medium">NAME</th>
                            <th class="px-4 py-3 text-left font-medium">COMPANY NAME</th>
                            <th class="px-4 py-3 text-left font-medium">EMAIL</th>
                            <th class="px-4 py-3 text-left font-medium">PHONE</th>
                            <th class="px-4 py-3 text-left font-medium">CITY</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in filteredCustomers" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.name }}</td>
                            <td class="px-4 py-3">{{ item.company_name }}</td>
                            <td class="px-4 py-3">{{item.email}}</td>
                            <td class="px-4 py-3">{{item.phone}}</td>
                            <td class="px-4 py-3">{{item.city}}</td>

                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <!-- Button Delete -->
                                    <button
                                        @click="deleteCustomer(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <!-- Button Edit -->
                                    <button
                                        @click="openEditPopup(item.id)"
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
import EditCustomerPopup from "@/Components/EditCustomerPopup.vue";
import { jsPDF } from 'jspdf';
import 'jspdf-autotable';
import * as XLSX from 'xlsx';

const page = usePage()
const auth = page.props.auth
const customers = ref([])
const filteredCustomers = ref([])
const loading = ref(true)
const searchQuery = ref('')
const searchTimeout = ref(null)
const locationFilter = ref('all')

const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const jabodetabekCities = ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi']

const showEditPopup = ref(false)
const selectedCustomerId = ref(null)

const openEditPopup = (id) => {
    selectedCustomerId.value = id
    showEditPopup.value = true
}

const closeEditPopup = () => {
    showEditPopup.value = false
}

const handleCustomerUpdated = () => {
    fetchCustomers()
}

const goToRestorePage = () => {
    localStorage.setItem('customerReferrer', '/customers/restore')
    router.visit('/customers/restore')
}

const goToAddPage = () => {
    localStorage.setItem('customerReferrer', '/customers/all')
    router.visit('/customers/create')
}

const goToDetail = (id) => {
    localStorage.setItem('customerReferrer', '/customers/all')
    router.visit(`/customers/${id}`)
}
const fetchCustomers = async () => {
    try {
        loading.value = true
        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            per_page: perPage.value,
        }

        const response = await axios.get('/api/customers', {params})

        const data = response.data
        customers.value = data.data

        applyLocationFilter()

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

const applyLocationFilter = () => {
    if (locationFilter.value === 'all') {
        filteredCustomers.value = customers.value
    } else if (locationFilter.value === 'jabodetabek') {
        filteredCustomers.value = customers.value.filter(customer => {
            return jabodetabekCities.some(city =>
                customer.city && customer.city.toLowerCase().includes(city)
            )
        })
    } else if (locationFilter.value === 'non-jabodetabek') {
        filteredCustomers.value = customers.value.filter(customer => {
            return !jabodetabekCities.some(city =>
                customer.city && customer.city.toLowerCase().includes(city)
            )
        })
    }
}

const handleFilterChange = () => {
    applyLocationFilter()
}

const deleteCustomer = async (id, customerName) => {
    const result = await Swal.fire({
        title: 'Delete Customer',
        html: `Are you sure you want to delete <strong>${customerName}</strong>?<br>The customer will be moved to the restore page.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    })

    if(result.isConfirmed) {
        try {
            loading.value = true
            await axios.delete(`/api/customers/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `${customerName} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchCustomers()
        } catch (error){

            console.error('Error deleting customer: ', error)

            Swal.fire({
                title: 'Error',
                text: error.response?.data?.errors?.message || 'Failed to delete customer.',
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

const handleSearch = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }

    searchTimeout.value = setTimeout(() => {
        currentPage.value = 1
        fetchCustomers()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchCustomers()
    } else {
        handleSearch()
    }
})


const fetchAllCustomers = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/customers', {
            params: {
                per_page: 9999
            }
        })
        return response.data.data
    } catch (error) {
        console.error('Error fetching all customers:', error)
        Swal.fire({
            title: 'Error',
            text: 'Failed to fetch all customers for export.',
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
        headStyles: {
            fillColor: [47, 132, 81],
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

    fetchCustomers()

    filteredCustomers.value = customers.value
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
        fetchCustomers()
    }
}


</script>
