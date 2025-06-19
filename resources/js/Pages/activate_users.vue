<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Search Header -->
                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search User"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <!-- Filter Status Disabled since fixed to inactive -->
                    <div class="flex gap-3 items-center">
                        <button
                            @click="goToAllUsers"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50"
                        >
                            <i class="ri-arrow-left-line text-lp-green"></i>
                            <p class="text-lp-green font-medium">Back to Users</p>
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
                            <th class="px-4 py-3 text-left font-medium">EMAIL</th>
                            <th class="px-4 py-3 text-left font-medium">ROLE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-8 py-3 text-right font-medium">ACTION</th>
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
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
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
                            <th class="px-4 py-3 text-left font-medium">EMAIL</th>
                            <th class="px-4 py-3 text-left font-medium">ROLE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-8 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in users" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.name }}</td>
                            <td class="px-4 py-3">{{ item.email }}</td>
                            <td class="px-4 py-3">{{ item.role }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                >
                                    Inactive
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button
                                    @click="activateUser(item.id, item.name)"
                                    class="px-3 py-1 bg-lp-green text-white rounded-md transition-colors"
                                >
                                    Activate
                                </button>
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
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const page = usePage()
const auth = page.props.auth
const users = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('inactive')  // Fixed inactive
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const searchTimeout = ref(null)

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('inactiveUserFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('inactiveUserFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || 'inactive'
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const activateUser = async (id, userName) => {
    const result = await Swal.fire({
        title: 'Activate User',
        html: `Are you sure you want to activate <strong>${userName}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2F8451',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, activate',
        cancelButtonText: 'Cancel'
    })

    if (result.isConfirmed) {
        try {
            loading.value = true
            await axios.patch(`/api/admin/users/${id}/status`, {
                status: 'active'
            })

            Swal.fire({
                title: 'Activated!',
                text: `${userName} has been activated.`,
                icon: 'success',
                confirmButtonText: 'OK'
            })

            fetchUsers()
        } catch (error) {
            console.error('Error activating user:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || 'Failed to activate user.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
        } finally {
            loading.value = false
        }
    }
}

const goToAllUsers = () => {
    router.visit('/users/all')
}

const fetchUsers = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/admin/users', {
            params: {
                page: currentPage.value,
                search: searchQuery.value.trim(),
                status: selectedStatus.value,
                per_page: perPage.value
            }
        })
        const data = response.data
        users.value = data.data

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
        fetchUsers()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchUsers()
    } else {
        handleSearch()
    }
})

const handleStatusChange = () => {
    // Disabled in UI, but keep just in case
    currentPage.value = 1
    fetchUsers()
}

const clearFilterState = () => {
    localStorage.removeItem('inactiveUserFilterState')
}

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    restoreFilterState()

    fetchUsers()
})

const totalPages = computed(() => Math.ceil(totalResults.value / perPage.value))

const from = computed(() => {
    if (totalResults.value === 0) return 0
    return (currentPage.value - 1) * perPage.value + 1
})

const to = computed(() => {
    let end = currentPage.value * perPage.value
    return end > totalResults.value ? totalResults.value : end
})

// Pagination logic to show max 5 page numbers with ellipsis if needed
const displayedPages = computed(() => {
    let pages = []
    if (totalPages.value <= 5) {
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i)
        }
    } else {
        if (currentPage.value <= 3) {
            pages = [1, 2, 3, 4, '...', totalPages.value]
        } else if (currentPage.value >= totalPages.value - 2) {
            pages = [1, '...', totalPages.value - 3, totalPages.value - 2, totalPages.value - 1, totalPages.value]
        } else {
            pages = [1, '...', currentPage.value - 1, currentPage.value, currentPage.value + 1, '...', totalPages.value]
        }
    }
    return pages
})

const handlePageChange = (page) => {
    if (page < 1 || page > totalPages.value || page === currentPage.value) return
    currentPage.value = page
    fetchUsers()
    saveFilterState()
}
</script>
