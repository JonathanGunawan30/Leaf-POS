<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Add Category Modal -->
                <div v-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out" :class="{ 'opacity-0': !showAddModal, 'opacity-100': showAddModal }">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg transition-transform duration-300 ease-in-out transform" :class="{ 'scale-90 opacity-0': !showAddModal, 'scale-100 opacity-100': showAddModal }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Add Category</h3>
                            <button @click="showAddModal = false" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Category Name</label>
                                <input
                                    v-model="newCategory.name"
                                    type="text"
                                    placeholder="Enter category name"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                >
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="showAddModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="addCategory"
                                class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                                :disabled="isSubmitting"
                            >
                                {{ isSubmitting ? 'Adding...' : 'Add Category' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Edit Category Modal -->
                <div v-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out" :class="{ 'opacity-0': !showEditModal, 'opacity-100': showEditModal }">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg transition-transform duration-300 ease-in-out transform" :class="{ 'scale-90 opacity-0': !showEditModal, 'scale-100 opacity-100': showEditModal }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Edit Category</h3>
                            <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Category Name</label>
                                <input
                                    v-model="editCategory.name"
                                    type="text"
                                    placeholder="Enter category name"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                >
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="showEditModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="updateCategory"
                                class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                                :disabled="isSubmitting"
                            >
                                {{ isSubmitting ? 'Updating...' : 'Update Category' }}
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Search and Add Header -->
                <div class="flex justify-between items-center mb-6">
                    <div class="relative border-lp-green">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Category"
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
                        <button
                            @click="showAddModal = true"
                            class="flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                        >
                            <i class="ri-add-line"></i>
                            Add Category
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

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">Category</th>
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
                            <th class="px-4 py-3 text-left font-medium">Category</th>
                            <th class="pl-4 pr-9 py-3 text-right font-medium">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in categories" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ ((currentPage - 1) * perPage) + index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex-1">{{ item.name }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex gap-1 justify-end">
                                    <!-- Button Delete -->
                                    <button
                                        @click="deleteCategory(item.id, item.name)"
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

<script setup>
import Sidebar from '../Components/Sidebar.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import RestoreIcon from '/resources/assets/icons/restore.svg'
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import InfoIcon from '/resources/assets/icons/info.svg'

const page = usePage()
const auth = page.props.auth
const categories = ref([])
const loading = ref(true)
const searchQuery = ref('')
const currentPage = ref(1)
const totalResults = ref(0)
const perPage = ref(10)

const showAddModal = ref(false)
const showEditModal = ref(false)
const isSubmitting = ref(false)
const newCategory = ref({ name: '' })
const editCategory = ref({ id: null, name: '' })

const searchTimeout = ref(null)

const refreshData = () => {
    fetchCategories();
}

const saveFilterState = () => {
    const filterState = {
        searchQuery: searchQuery.value,
        currentPage: currentPage.value
    }
    localStorage.setItem('categoryFilterState', JSON.stringify(filterState))
}

const restoreFilterState = () => {
    const savedState = localStorage.getItem('categoryFilterState')
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

const goToRestorePage = () => {
    router.visit('/expense-categories/restore')
}

const goToDetail = (id) => {
    saveFilterState()
    localStorage.setItem('categoryExpenseReferrer', '/expense-categories/all')
    router.visit(`/expense-categories/${id}`)
}

const openEditModal = (category) => {
    editCategory.value = { ...category }
    showEditModal.value = true
}

const addCategory = async () => {
    if (!newCategory.value.name.trim()) {
        Swal.fire({
            title: 'Error',
            text: 'Category name is required',
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })
        return
    }

    try {
        isSubmitting.value = true
        await axios.post('/api/expense-categories', newCategory.value)

        Swal.fire({
            icon: 'success',
            title: 'Category Expense added successfully',
            text: 'Category Expense has been added successfully.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        newCategory.value = { name: '' }
        showAddModal.value = false

        fetchCategories()
    } catch (error) {
        console.error('Error adding category:', error)

        const errors = error.response?.data?.errors?.message;
        let errorHtml = '<p>Failed to add category.</p>';

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
        isSubmitting.value = false
    }
}

const updateCategory = async () => {
    if (!editCategory.value.name.trim()) {
        Swal.fire({
            title: 'Error',
            text: 'Category name is required',
            icon: 'error',
            confirmButtonText: 'OK'
        })
        return
    }

    try {
        isSubmitting.value = true
        await axios.patch(`/api/expense-categories/${editCategory.value.id}`, {
            name: editCategory.value.name
        })

        Swal.fire({
            title: 'Success',
            text: 'Category Expense has been updated successfully',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        showEditModal.value = false

        fetchCategories()
    } catch (error) {
        console.error('Error updating category:', error)

        const errors = error.response?.data?.errors?.message;
        let errorHtml = '<p>Failed to update category.</p>';

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
        isSubmitting.value = false
    }
}

const deleteCategory = async (id, categoryName) => {
    const result = await Swal.fire({
        title: 'Delete Category',
        html: `Are you sure you want to delete <strong>${categoryName}</strong>?<br>The category will be moved to the restore page.`,
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
            await axios.delete(`/api/expense-categories/${id}`)

            Swal.fire({
                title: 'Deleted!',
                text: `${categoryName} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
                },
            })

            fetchCategories()
        } catch (error) {
            console.error('Error deleting category:', error)

            Swal.fire({
                title: 'Error',
                text: error.response?.data?.errors?.message || 'Failed to delete category.',
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

const fetchCategories = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/expense-categories', {
            params: {
                page: currentPage.value,
                search: searchQuery.value.trim(),
                per_page: perPage.value
            }
        })
        const data = response.data
        categories.value = data.data

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
        fetchCategories()
    }, 300)
}

watch(searchQuery, (newValue) => {
    if (newValue === '') {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }
        currentPage.value = 1
        fetchCategories()
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

    fetchCategories()
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
        fetchCategories()
    }
}
</script>
