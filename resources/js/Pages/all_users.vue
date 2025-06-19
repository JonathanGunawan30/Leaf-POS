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
                            placeholder="Search User"
                            class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:border-lp-green"
                        >
                        <i class="ri-search-line absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    <!-- KANAN: Activate Button dan Filter -->
                    <div class="flex gap-3 items-center">
                        <button @click="goToRestorePage"
                            class="flex items-center gap-2 px-4 py-2 border border-lp-green rounded-lg hover:bg-gray-50">
                            <RestoreIcon class="w-5 h-4 text-lp-green" />
                            <p class="text-lp-green font-medium">Restore</p>
                        </button>
                        <button @click="activateUser"
                            class="flex justify-start items-center flex-row gap-[5px] hover:bg-gray-50 py-1.5 px-3 bg-white border-solid border-[#2F8451] border rounded-lg">
                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.28307 2.82598L12.5 1L20.7169 2.82598C21.1745 2.92766 21.5 3.33347 21.5 3.80217V13.7889C21.5 15.795 20.4974 17.6684 18.8282 18.7812L12.5 23L6.1718 18.7812C4.50261 17.6684 3.5 15.795 3.5 13.7889V3.80217C3.5 3.33347 3.82553 2.92766 4.28307 2.82598ZM12.5 11C13.8807 11 15 9.88071 15 8.5C15 7.11929 13.8807 6 12.5 6C11.1193 6 10 7.11929 10 8.5C10 9.88071 11.1193 11 12.5 11ZM8.02746 16H16.9725C16.7238 13.75 14.8163 12 12.5 12C10.1837 12 8.27619 13.75 8.02746 16Z" fill="#2F8451"/>
                            </svg>
                            <span class="text-[#2F8451] text-sm text-center font-medium leading-7 tracking-[0.14px]">
                                Activate User
                              </span>
                        </button>

                        <select
                            v-model="selectedStatus"
                            @change="handleStatusChange"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 border-lp-green focus:outline-none">
                            <option value="" class="text-lp-green">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <div class="relative inline-block">
                            <button
                                @click="showRoleFilter = !showRoleFilter"
                                class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 relative"
                            >
                                <i class="ri-filter-3-line"></i>
                                Filter
                                <span
                                    v-if="selectedRoles.length"
                                    class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"
                                    title="Filters are active"
                                ></span>
                            </button>


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

                <div v-if="selectedRoles.length" class="flex flex-wrap gap-2 mt-4 mb-4">
                    <div class="text-sm text-gray-600 font-medium">Active Role Filters:</div>

                    <div
                        v-for="role in selectedRoles"
                        :key="role"
                        class="px-3 py-1 bg-gray-100 rounded-full text-sm flex items-center gap-1"
                    >
                        <span>{{ role }}</span>
                        <button @click="removeRole(role)" class="text-gray-500 hover:text-red-500">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    <button
                        @click="resetRoleFilter"
                        class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm hover:bg-red-200"
                    >
                        Clear All
                    </button>
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
                            <th class="px-4 py-3 text-left font-medium">EMAIL</th>
                            <th class="px-4 py-3 text-left font-medium">ROLE</th>
                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                            <th class="px-16 py-3 text-right font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in users" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">{{ item.name }}</td>
                            <td class="px-4 py-3">{{ item.email }}</td>
                            <td class="px-4 py-3">{{item.role}}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        item.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ item.status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-8 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <!-- Button Delete -->
                                    <button
                                        @click="deleteUser(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                                        <trash-icon class="" />
                                    </button>

                                    <!-- Button Edit -->
                                    <button
                                        @click="openEditModal(item)"
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
import PencilIcon from '/resources/assets/icons/pencil.svg'
import RestoreIcon from "../../assets/icons/restore.svg";
import InfoIcon from "../../assets/icons/info.svg";

const page = usePage()
const auth = page.props.auth
const users = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStatus = ref('active')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)
const showRoleFilter = ref(false)
const selectedRoles = ref([])
const availableRoles = ['Admin', 'Purchasing', 'Sales', 'Inventory', 'Finance', 'No Role']
const showEditModal = ref(false)
const roles = ref([])
const editUserData = reactive({
    id: null, name: '', email: '', role_id: '', status: ''
})

const fetchRoles = async () => {
    try {
        const res = await axios.get('/api/admin/roles')
        roles.value = res.data.data || []
    } catch (error) {
        console.error('Failed to fetch roles', error)
    }
}


const openEditModal = (user) => {
    console.log('Opening edit modal for user:', user)

    editUserData.id = user.id
    editUserData.name = user.name
    editUserData.email = user.email

    const matchedRole = roles.value.find(r => r.name === user.role)
    editUserData.role_id = matchedRole ? matchedRole.id : null

    editUserData.status = user.status
    showEditModal.value = true
}


const saveEditUser = async () => {
    try {
        await axios.patch(`/api/admin/users/${editUserData.id}`, {
            name: editUserData.name,
            email: editUserData.email,
            role_id: editUserData.role_id,
            status: editUserData.status
        })
        showEditModal.value = false
        fetchUsers()

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'User updated successfully.',
            timer: 1500,
            showConfirmButton: false,
        })
    } catch (error) {
        console.error('Update user failed', error)

        let message = 'Failed to update user.'
        if (error.response?.data?.errors?.message?.email) {
            message = error.response.data.errors.message.email[0] ?? message
        } else if (error.response?.data?.errors?.message) {
            message = error.response.data.errors.message
        }

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'OK'
        })
    }
}

const applyRoleFilter = () => {
    showRoleFilter.value = false
    const noRoleSelected = selectedRoles.value.includes('No Role')
    fetchUsers(noRoleSelected)
}

const resetRoleFilter = () => {
    selectedRoles.value = []
    showRoleFilter.value = false
    fetchUsers()
}

const removeRole = (role) => {
    selectedRoles.value = selectedRoles.value.filter(r => r !== role)
    fetchUsers()
}


const searchTimeout = ref(null)

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        selectedStatus: selectedStatus.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('userFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('userFilterState')
    if (savedState) {
        try {
            const filterState = JSON.parse(savedState)
            searchQuery.value = filterState.searchQuery || ''
            selectedStatus.value = filterState.selectedStatus || 'active'
            currentPage.value = filterState.currentPage || 1
        } catch (error) {
            console.error('Error restoring filter state:', error)
        }
    }
}

const goToDetail = (id) => {

    saveFilterState()

    localStorage.setItem('userReferrer', '/users/all')
    router.visit(`/users/${id}`)
}

const editUser = (id) => {
    saveFilterState()
    router.visit(`/users/${id}/edit`)
}

const deleteUser = async (id, userName) => {
    const result = await Swal.fire({
        title: 'Delete User',
        html: `Are you sure you want to delete <strong>${userName}</strong>?<br>The user will be moved to the restore page.`,
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
            await axios.delete(`/api/admin/users/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `${userName} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchUsers()
        } catch (error) {
            console.error('Error deleting user:', error)

            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.errors?.message || 'Failed to delete user.',
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

const fetchUsers = async () => {
    try {
        loading.value = true

        const filteredRoles = selectedRoles.value.filter(r => r !== 'No Role')

        const noRoleSelected = selectedRoles.value.includes('No Role')

        const params = {
            page: currentPage.value,
            search: searchQuery.value.trim(),
            status: selectedStatus.value,
            per_page: perPage.value,
        }

        if (filteredRoles.length > 0) {
            params.roles = filteredRoles
        }

        if (noRoleSelected) {
            params.no_role = true
        }

        const response = await axios.get('/api/admin/users', { params })

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

const activateUser = async () => {
    router.visit('/users/inactive')
}

const goToRestorePage = () => {
    router.visit('/users/restore')
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

watch(selectedRoles, () => {
    fetchUsers()
})


const handleStatusChange = () => {
    currentPage.value = 1
    fetchUsers()
}

const clearFilterState = () => {
    localStorage.removeItem('userFilterState')
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
    fetchRoles()
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
        fetchUsers()
    }
}
</script>
