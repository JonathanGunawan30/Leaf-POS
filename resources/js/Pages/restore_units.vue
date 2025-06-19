<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <!-- Search and Add Header -->
                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Unit"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="goToAllUnits"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Units</p>
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">Unit Name</th>
                            <th class="px-4 py-3 text-left font-medium">Unit Code</th>
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

                <div v-else-if="units.length === 0" class="text-center py-10">
                    <div class="text-gray-400 mb-4">
                        <i class="ri-delete-bin-line text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Deleted Units</h3>
                    <p class="text-gray-500">There are no deleted units to restore.</p>
                </div>

                <!-- Units Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">Unit Name</th>
                            <th class="px-4 py-3 text-left font-medium">Unit Code</th>
                            <th class="pl-4 pr-9 py-3 text-right font-medium">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in units" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex-1">{{ item.name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex-1">{{ item.code }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">

                                    <!-- Button Restore -->
                                    <button
                                        @click="restoreUnit(item.id)"
                                        class="p-2 bg-lp-green text-white rounded-md hover:bg-green-600 transition-colors"
                                        title="Restore Category"
                                    >
                                        <RestoreIcon class="w-4 h-4 text-white" />
                                    </button>

                                    <!-- Button Delete -->
                                    <button
                                        @click="hardDeleteUnit(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class=""/>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="units.length > 0" class="flex justify-between items-center mt-4">
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
import {router, usePage} from '@inertiajs/vue3'
import {ref, onMounted, computed, watch} from 'vue'
import axios from 'axios'
import RestoreIcon from '/resources/assets/icons/restore-white.svg'
import TrashIcon from '/resources/assets/icons/trash.svg'
import Swal from 'sweetalert2'

const page = usePage()
const auth = page.props.auth
const units = ref([])
const loading = ref(true)
const searchQuery = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const searchTimeout = ref(null)

const goToAllUnits = () => {
    router.visit('/units/all')
}

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('restoreUnitFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('restoreUnitFilterState')
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
const hardDeleteUnit = async (id, unitName) => {
    const result = await Swal.fire({
        title: 'Permanently Delete Unit',
        html: `<div class="text-left">
            <p class="mb-2">Are you sure you want to <strong>permanently delete</strong> <strong>${unitName}</strong>?</p>
            <p class="text-red-600 font-bold">Warning: This action cannot be undone!</p>
            <p>The unit will be permanently removed from the system and cannot be recovered.</p>
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

            await axios.delete(`/api/units/${id}/force`)

            Swal.fire({
                title: 'Permanently Deleted!',
                text: `${unitName} has been permanently deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchUnits()
        } catch (error) {
            console.error("Error hard deleting unit: ", error)

            const errors = error.response?.data?.errors?.message;
            let errorHtml = '<p>Failed to hard delete unit.</p>';

            if (errors && typeof errors === 'object') {
                const errorList = Object.entries(errors).map(([field, messages]) => {
                    const capitalizedField = field.charAt(0).toUpperCase() + field.slice(1);
                    return `<p><strong>${capitalizedField}:</strong> ${messages.join(', ')}</p>`;
                });
                errorHtml = errorList.join('');
            }

            Swal.fire({
                title: 'Error',
                html: errorHtml,
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            });
        } finally {
            loading.value = false
        }
    }
}

const restoreUnit = async (id) => {
    try {
        loading.value = true
        await axios.patch(`/api/units/${id}/restore`)

        Swal.fire({
            title: 'Restored!',
            text: 'Unit has been restored.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },

        })

        fetchUnits()
    } catch (error) {
        console.error("Error restoring unit: ", error)

        const errors = error.response?.data?.errors?.message;
        let errorHtml = '<p>Failed to restore unit.</p>';

        if (errors && typeof errors === 'object') {
            const errorList = Object.entries(errors).map(([field, messages]) => {
                const capitalizedField = field.charAt(0).toUpperCase() + field.slice(1);
                return `<p><strong>${capitalizedField}:</strong> ${messages.join(', ')}</p>`;
            });
            errorHtml = errorList.join('');
        }

        Swal.fire({
            title: 'Error',
            html: errorHtml,
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

const fetchUnits = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/units/trashed', {
            params: {
                page: currentPage.value,
                search: searchQuery.value.trim(), // Trim whitespace
                per_page: perPage.value
            }
        })
        const data = response.data
        units.value = data.data

        currentPage.value = data.meta?.current_page || 1
        totalResults.value = data.meta?.total || 0
        perPage.value = data.meta?.per_page || 10
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
        fetchUnits()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchUnits()
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

    fetchUnits()
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
        fetchUnits()
    }
}
</script>







