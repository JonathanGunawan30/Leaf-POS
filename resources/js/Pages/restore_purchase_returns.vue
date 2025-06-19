<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <div class="flex justify-between items-center mb-6">
                    <div  class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Purchase Return"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>


                    <div class="flex gap-3 items-center">
                        <button @click="goToAllPurchaseReturns"
                                class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Purchase Returns</p>
                        </button>
                    </div>

                </div>


                <div v-if="loading" class="overflow-x-auto rounded-lg">
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
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else-if="purchaseReturns.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Purchase Return</h3>
                    <p class="text-gray-500">There are no deleted purchase return to restore.</p>
                </div>

                <div v-else class="overflow-x-auto rounded-lg">
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
                                <span
                                    :class="['inline-block px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap', getStatusClass(item.status)]"
                                    :title="capitalizeFirstLetter(item.status)">
                                {{ capitalizeFirstLetter(item.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    :class="['inline-block px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap', getPaymentStatusClass(item.payment_status)]"
                                    :title="formatPaymentStatus(item.payment_status)">
                                    {{ formatPaymentStatus(item.payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ item.compensation_method }}</td>
                            <td class="px-4 py-3">{{ item.user.name }}</td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button
                                        @click="deletePermanently(item.id, item.invoice_number)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <button
                                        @click="restorePurchaseReturn(item.id, item.invoice_number)"
                                        class="p-1.5 bg-lp-green text-white rounded-md hover:bg-green-600 transition-colors">
                                        <restore-icon class=""/>
                                    </button>

                                    <button @click="goToDetail(item.id)"
                                            class="p-2 bg-blue-500 text-white rounded-md  transition-colors">
                                        <InfoIcon class=""/>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="purchaseReturns.length > 0" class="flex justify-between items-center mt-4">
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
import {router, usePage} from '@inertiajs/vue3'
import {ref, onMounted, computed, watch} from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import TrashIcon from '/resources/assets/icons/trash.svg'
import RestoreIcon from "../../assets/icons/restore-white.svg"
import InfoIcon from "../../assets/icons/info.svg";

import 'jspdf-autotable';


const page = usePage()
const auth = page.props.auth
const purchaseReturns = ref([])
const loading = ref(true)
const searchQuery = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const searchTimeout = ref(null)

const goToAllPurchaseReturns = () => {
    router.visit('/purchase-returns/all')
}

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        currentPage: currentPage.value,
    }
    localStorage.setItem('purchaseReturnFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('purchaseReturnFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
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
    localStorage.setItem('purchaseReturnReferrer', '/purchase-returns/restore')
    router.visit(`/purchase-returns/${id}`)
}

const restorePurchaseReturn = async (id, invoiceNumber) => {
    const result = await Swal.fire({
        title: 'Restore Purchase return',
        html: `Are you sure you want to restore purchase return <strong>${invoiceNumber}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2F8451',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, restore it!',
        cancelButtonText: 'Cancel'
    })

    if (result.isConfirmed) {
        try {
            loading.value = true
            await axios.patch(`/api/purchase-returns/${id}/restore`)

            Swal.fire({
                title: 'Restored!',
                text: `Purchase return ${invoiceNumber} has been restored.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchPurchaseReturns()
        } catch (error) {
            console.error('Error restoring purchase return:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to restore purchase.',
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

const deletePermanently = async (id, invoiceNumber) => {
    const result = await Swal.fire({
        title: 'Delete Permanently',
        html: `Are you sure you want to permanently delete purchase return <strong>${invoiceNumber}</strong>?<br>This action cannot be undone!`,
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
            await axios.delete(`/api/purchase-returns/${id}/force`)

            Swal.fire({
                title: 'Deleted!',
                text: `Purchase return ${invoiceNumber} has been permanently deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchPurchaseReturns()
        } catch (error) {
            console.error('Error deleting purchase return permanently:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete purchase return permanently.',
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
            per_page: perPage.value,
        }

        const response = await axios.get('/api/purchase-returns/trashed', {params})

        const data = response.data
        purchaseReturns.value = data.data

        currentPage.value = data.meta.current_page
        totalResults.value = data.meta.total
        perPage.value = data.meta.per_page

        return purchaseReturns.value;

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
        fetchPurchaseReturns()
    }
}
</script>
