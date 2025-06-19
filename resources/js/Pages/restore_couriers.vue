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
                            placeholder="Search Courier"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <!-- KANAN: Activate Button dan Filter -->
                    <div class="flex gap-3 items-center">
                        <button
                            @click="goToAllCouriers"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Couriers</p>
                        </button>

                        <select
                            v-model="selectedStatus"
                            @change="handleStatusChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Status</option>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>

                        <div class="relative inline-block">


                            <!-- Popup Filter -->
                            <div
                                v-show="showRoleFilter"
                                v-click-outside="() => showRoleFilter = false"
                                class="absolute z-50 right-0 mt-2 w-64 bg-white border rounded-lg shadow-lg p-4 transition-all duration-200"
                            >

                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-sm font-semibold">Filter by Role</h3>
                                    <button @click="showRoleFilter = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-2 mb-4 max-h-50 overflow-auto px-2 py-1">
                                    <label
                                        v-for="role in availableRoles"
                                        :key="role"
                                        class="flex items-center gap-2 text-sm text-gray-700"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="role"
                                            v-model="selectedRoles"
                                            class="accent-lp-green"
                                        />
                                        {{ role }}
                                    </label>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="resetRoleFilter"
                                        class="text-sm px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        @click="applyRoleFilter"
                                        class="text-sm px-3 py-1 bg-lp-green text-white rounded-md hover:bg-green-700"
                                    >
                                        Apply
                                    </button>
                                </div>
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
                            <th class="px-4 py-3 text-left font-medium">NAME</th>
                            <th class="px-4 py-3 text-left font-medium">PHONE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
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

                <div v-else-if="couriers.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Couriers</h3>
                    <p class="text-gray-500">There are no deleted couriers to restore.</p>
                </div>

                <!-- Users Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">NAME</th>
                            <th class="px-4 py-3 text-left font-medium">PHONE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in couriers" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.name }}</td>
                            <td class="px-4 py-3">{{ item.phone }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        item.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ item.status === 'available' ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <!-- Button Restore -->
                                    <button
                                        @click="restoreCourier(item.id)"
                                        class="p-2 bg-lp-green text-white rounded-md hover:bg-green-600 transition-colors"
                                        title="Restore User"
                                    >
                                        <RestoreIcon class="w-4 h-4 text-white" />
                                    </button>

                                    <!-- Button Hard Delete -->
                                    <button
                                        @click="hardDeleteCourier(item.id, item.name)"
                                        class="p-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors"
                                        title="Permanently Delete User"
                                    >
                                        <TrashIcon class="w-4 h-4 text-white" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="couriers.length > 0" class="flex justify-between items-center mt-4">
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

            <!-- Edit Modal BANG -->
            <div v-if="showEditModal"
                 class="fixed top-0 right-0 h-full bg-black bg-opacity-40 flex justify-end items-stretch z-50">
                <!-- Modal content -->
                <div class="bg-white p-6 w-[570px] max-h-full flex flex-col">

                    <!-- Content utama -->
                    <div class="flex flex-col gap-4 overflow-auto">
                        <!-- Header -->
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900 text-xl">EDIT USER</h3>
                            <button @click="showEditModal = false" aria-label="Close modal" class="text-gray-600 hover:text-gray-900 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <hr class="border-gray-300" />

                        <!-- Form Fields -->
                        <div class="flex flex-col gap-4 text-gray-800">
                            <label class="font-medium text-sm">Name</label>
                            <input v-model="editUserData.name" type="text" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600" />

                            <label class="font-medium text-sm">Email</label>
                            <input v-model="editUserData.email" type="email" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600" />

                            <label class="font-medium text-sm">Role</label>
                            <select v-model="editUserData.role_id" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                <option disabled value="">-- Select Role --</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                    {{ role.name }}
                                </option>
                            </select>

                            <label class="font-medium text-sm">Status</label>
                            <select v-model="editUserData.status" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons  -->
                    <div class="mt-auto flex justify-center gap-6 py-4 border-t border-gray-300">
                        <button @click="showEditModal = false"
                                class="px-8 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition">
                            Cancel
                        </button>
                        <button @click="saveEditUser"
                                class="px-8 py-2 bg-lp-green text-white rounded-md transition">
                            Save
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
import RestoreIcon from "../../assets/icons/restore-white.svg";

const page = usePage()
const auth = page.props.auth
const couriers = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('available')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const showEditModal = ref(false)

const editUserData = reactive({
    id: null, name: '', phone:'', status: 'available'
})

const goToAllCouriers = () => {
    router.visit("/couriers/all")
}

const searchTimeout = ref(null)

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        currentPage: currentPage.value,
    }
    localStorage.setItem('courierFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('courierFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || 'available'
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const restoreCourier = async (id) => {
    try {
        loading.value = true
        await axios.patch(`/api/couriers/${id}/restore`)

        Swal.fire({
            title: 'Success!',
            text: 'Courier has been restored successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        fetchCouriers()

    } catch (error){
        console.error('Error restoring Courier:', error)

        let errorMessage = 'Failed to restore courier. Please check the data or re-login.';
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

const hardDeleteCourier = async (id, name) => {
    const result = await Swal.fire({
        title: 'Permanently Delete Courier',
        html: `<div class="text-left">
            <p class="mb-2">Are you sure you want to <strong>permanently delete</strong> <strong>${name}</strong>?</p>
            <p class="text-red-600 font-bold">Warning: This action cannot be undone!</p>
            <p>The courier will be permanently removed from the system and cannot be recovered.</p>
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
            await axios.delete(`/api/couriers/${id}/force`)

            Swal.fire({
                title: 'Permanently Deleted!',
                text: `${name} has been permanently deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchCouriers()
        } catch (error) {
            console.error('Error hard deleting courier:', error)

            let errorMessage = 'Failed to hard delete courier. Please check the data or re-login.';
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

const fetchCouriers = async () => {
    try {
        loading.value = true

        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            status: selectedStatus.value,
            per_page: perPage.value,
        }

        const response = await axios.get('/api/couriers/trashed', { params })

        const data = response.data
        couriers.value = data.data

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
        fetchCouriers()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchCouriers()
    } else {
        handleSearch()
    }
})

const handleStatusChange = () => {
    currentPage.value = 1
    fetchCouriers()
}


onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    fetchCouriers()
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
        fetchCouriers()
    }
}
</script>
