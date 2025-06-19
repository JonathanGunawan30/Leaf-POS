<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <EditTaxPopup
                    :show="showEditPopup"
                    :tax-id="selectedTaxId"
                    @close="closeEditPopup"
                    @updated="handleTaxUpdated"
                />

                <!-- Search and Add Header -->
                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Tax"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="goToRestorePage"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
                        </button>

                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Rate</th>
                            <th class="pl-4 pr-9 py-3 text-right font-medium">Action</th>
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
                                <div class="w-24 h-6 bg-gray-300 rounded-md animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Categories Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Rate</th>
                            <th class="pl-4 pr-9 py-3 text-right font-medium">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in taxes" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex-1">{{ item.name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex-1">{{ parseFloat(item.rate).toFixed(0) }}%</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">
                                    <!-- Button Delete -->
                                    <button
                                        @click="deleteTax(item.id, item.name)"
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
                                    <button
                                        @click="goToDetail(item.id)"
                                        class="p-2 bg-lp-green text-white rounded-md hover:bg-lp-green-dark transition-colors">
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
<script setup >
import Sidebar from '../Components/Sidebar.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import RestoreIcon from '/resources/assets/icons/restore.svg'
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import InfoIcon from '/resources/assets/icons/info.svg'
import EditProductPopup from "@/Components/EditProductPopup.vue";
import EditTaxPopup from "@/Components/EditTaxPopup.vue";

const page = usePage()
const auth = page.props.auth
const taxes = ref([])
const loading = ref(true)
const searchQuery = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const searchTimeout = ref(null)

const showEditPopup = ref(false)
const selectedTaxId = ref(null)
const fetchTaxes = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/taxes', {
            params: {
                page: currentPage.value,
                search: searchQuery.value.trim(),
                per_page: perPage.value
            }
        });

        const data = response.data
        taxes.value = data.data
        currentPage.value = data.meta?.current_page || 1
        totalResults.value = data.meta?.total || 0
        perPage.value = data.meta?.per_page || 10

    } catch (error){
        if(error.response){
            console.error('API Error: ', {
                status: error.response.status,
                data: error.response.data,
                headers: error.response.headers
            })

            if(error.response.status === 401){
                localStorage.removeItem('X-API-TOKEN')
                route.visit('/')
            }
        } else if (error.request) {
            console.error('No response received: ', error.request)
        } else {
            console.error('Error setting up request: ', error.message)
        }
    } finally {
        loading.value = false;
    }
}

const deleteTax = async (id, taxName) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        html: `Are you sure you want to delete <strong>${taxName}</strong>?<br>The tax will be moved to the restore page.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    })

    if(result.isConfirmed) {
        try {
            loading.value = true

            await axios.delete(`/api/taxes/${id}`)

            Swal.fire({
                title: 'Success!',
                text: `${taxName} has been successfully deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200 '
                },
                buttonsStyling: false
            })

            fetchTaxes()
        } catch (error){
            console.error('Failed to delete tax:', error.response?.data || error.message)

            Swal.fire({
                title: 'Failed!',
                text: error.response?.data?.errors?.message || 'Failed to delete tax.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200 '
                },
                buttonsStyling: false
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
        fetchTaxes()
    }, 300)
}

const goToRestorePage = () => {
    router.visit('/taxes/restore')
}

const goToDetail = (id) => {
    router.visit(`/taxes/${id}`)
}

const openEditPopup = (id) => {
    selectedTaxId.value = id
    showEditPopup.value = true
}

const closeEditPopup = () => {
    showEditPopup.value = false
}

const handleTaxUpdated = () => {
    fetchTaxes()
}

watch(searchQuery, (newValue) => {

    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchTaxes()
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
    fetchTaxes()
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

const hasMorePages = computed(() => {
    return Math.ceil(totalResults.value / perPage.value) > 6
})

const handlePageChange = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        currentPage.value = page
        fetchTaxes()
    }
}
</script>
