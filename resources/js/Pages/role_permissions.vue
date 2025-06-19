<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Page Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-semibold text-gray-800">Role & Permissions</h1>
                    <button
                        @click="showAddRoleModal = true"
                        class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700 flex items-center gap-2"
                    >
                        <i class="ri-add-line"></i>
                        Add New Role
                    </button>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">ROLE NAME</th>
                            <th class="px-4 py-3 text-left font-medium">DESCRIPTION</th>
                            <th class="px-4 py-3 text-left font-medium">USERS</th>
                            <th class="px-4 py-3 text-left font-medium">ACTION</th>
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
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                    <div class="w-10 h-10 bg-gray-300 rounded-md animate-pulse"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Roles Table -->
                <div v-else class="overflow-x-auto rounded-lg">
                    <table class="w-full">
                        <thead class="bg-lp-green bg-opacity-20">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                            <th class="px-4 py-3 text-left font-medium">ROLE NAME</th>
                            <th class="px-4 py-3 text-left font-medium">DESCRIPTION</th>
                            <th class="px-4 py-3 text-left font-medium">USERS</th>
                            <th class="px-4 py-3 text-left font-medium">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in roles" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                            <td class="px-4 py-3">{{ item.description || 'No description' }}</td>
                            <td class="px-4 py-3">{{ item.users_count || 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <!-- Button Edit -->
                                    <button
                                        @click="editRole(item)"
                                        class="p-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                                    >
                                        <pencil-icon class="" />
                                    </button>

                                    <!-- Button Delete -->
                                    <button
                                        @click="deleteRole(item.id, item.name)"
                                        class="p-1.5 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors"
                                        :disabled="item.users_count > 0"
                                        :class="{ 'opacity-50 cursor-not-allowed': item.users_count > 0 }"
                                    >
                                        <trash-icon class="" />
                                    </button>

                                    <!-- Button Permissions -->
                                    <button
                                        @click="managePermissions(item)"
                                        class="p-1.5 bg-lp-green text-white rounded-md hover:bg-lp-green-dark transition-colors"
                                    >
                                        <key-icon class="" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Add/Edit Role Modal -->
                <div v-if="showAddRoleModal || showEditRoleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">{{ showEditRoleModal ? 'Edit Role' : 'Add New Role' }}</h3>
                            <button @click="closeRoleModal" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Role Name <span class="text-red-500">*</span></label>
                                <input
                                    v-model="roleForm.name"
                                    type="text"
                                    placeholder="Enter role name"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Description</label>
                                <textarea
                                    v-model="roleForm.description"
                                    placeholder="Enter role description"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-lp-green h-24 resize-none"
                                ></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="closeRoleModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="saveRole"
                                class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                            >
                                {{ showEditRoleModal ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Permissions Modal -->
                <div v-if="showPermissionsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-[600px] shadow-lg max-h-[80vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Manage Permissions for {{ selectedRole?.name }}</h3>
                            <button @click="closePermissionsModal" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div v-if="permissionsLoading" class="flex justify-center py-8">
                                <div class="w-8 h-8 border-4 border-lp-green border-t-transparent rounded-full animate-spin"></div>
                            </div>
                            <div v-else>
                                <div v-for="(group, groupName) in groupedPermissions" :key="groupName" class="mb-6">
                                    <h4 class="font-medium text-gray-700 mb-2 border-b pb-1">{{ formatGroupName(groupName) }}</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div v-for="permission in group" :key="permission.id" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :id="`permission-${permission.id}`"
                                                v-model="selectedPermissions"
                                                :value="permission.id"
                                                class="w-4 h-4 text-lp-green focus:ring-lp-green rounded"
                                            >
                                            <label :for="`permission-${permission.id}`" class="ml-2 text-sm text-gray-700">
                                                {{ formatPermissionName(permission.name) }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                @click="closePermissionsModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="savePermissions"
                                class="px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-green-700"
                                :disabled="permissionsLoading"
                            >
                                Save Permissions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import Sidebar from '../Components/Sidebar.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import TrashIcon from '/resources/assets/icons/trash.svg'
import PencilIcon from '/resources/assets/icons/pencil.svg'
import KeyIcon from '/resources/assets/icons/key.svg'

const page = usePage()
const auth = page.props.auth
const roles = ref([])
const loading = ref(true)

// Role form
const roleForm = ref({
    name: '',
    description: ''
})

// Modal states
const showAddRoleModal = ref(false)
const showEditRoleModal = ref(false)
const showPermissionsModal = ref(false)

// Selected role for editing or managing permissions
const selectedRole = ref(null)
const selectedRoleId = ref(null)

// Permissions
const permissions = ref([])
const selectedPermissions = ref([])
const permissionsLoading = ref(false)

// Group permissions by module
const groupedPermissions = computed(() => {
    const groups = {}

    permissions.value.forEach(permission => {
        // Extract module name from permission name (e.g., "users.create" -> "users")
        const moduleName = permission.name.split('.')[0]

        if (!groups[moduleName]) {
            groups[moduleName] = []
        }

        groups[moduleName].push(permission)
    })

    return groups
})

// Format permission name for display
const formatPermissionName = (name) => {
    // Convert "users.create" to "Create"
    const parts = name.split('.')
    if (parts.length > 1) {
        // Capitalize first letter
        return parts[1].charAt(0).toUpperCase() + parts[1].slice(1)
    }
    return name
}

// Format group name for display
const formatGroupName = (name) => {
    // Capitalize first letter
    return name.charAt(0).toUpperCase() + name.slice(1)
}

// Function to fetch roles
const fetchRoles = async () => {
    try {
        loading.value = true
        const token = localStorage.getItem('X-API-TOKEN')
        const response = await axios.get('/api/admin/roles', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        roles.value = response.data.data
    } catch (error) {
        console.error('Error fetching roles:', error)

        if (error.response?.status === 401) {
            localStorage.removeItem('X-API-TOKEN')
            router.visit('/')
        }
    } finally {
        loading.value = false
    }
}

// Function to fetch permissions
const fetchPermissions = async () => {
    try {
        permissionsLoading.value = true
        const token = localStorage.getItem('X-API-TOKEN')
        const response = await axios.get('/api/admin/permissions', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        permissions.value = response.data.data
    } catch (error) {
        console.error('Error fetching permissions:', error)
    } finally {
        permissionsLoading.value = false
    }
}

// Function to fetch role permissions
const fetchRolePermissions = async (roleId) => {
    try {
        permissionsLoading.value = true
        const token = localStorage.getItem('X-API-TOKEN')
        const response = await axios.get(`/api/admin/roles/${roleId}/permissions`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        selectedPermissions.value = response.data.data.map(p => p.id)
    } catch (error) {
        console.error('Error fetching role permissions:', error)
    } finally {
        permissionsLoading.value = false
    }
}

// Function to edit a role
const editRole = (role) => {
    selectedRole.value = role
    selectedRoleId.value = role.id
    roleForm.value = {
        name: role.name,
        description: role.description || ''
    }
    showEditRoleModal.value = true
}

// Function to manage permissions for a role
const managePermissions = async (role) => {
    selectedRole.value = role
    selectedRoleId.value = role.id
    showPermissionsModal.value = true

    // Fetch permissions if not already loaded
    if (permissions.value.length === 0) {
        await fetchPermissions()
    }

    // Fetch role permissions
    await fetchRolePermissions(role.id)
}

// Function to delete a role
const deleteRole = async (id, roleName) => {
    // Show confirmation dialog
    const result = await Swal.fire({
        title: 'Delete Role',
        html: `Are you sure you want to delete <strong>${roleName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    })

    // If user confirms deletion
    if (result.isConfirmed) {
        try {
            loading.value = true
            const token = localStorage.getItem('X-API-TOKEN')
            // Make API call to delete the role
            await axios.delete(`/api/admin/roles/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })

            // Show success message
            Swal.fire({
                title: 'Deleted!',
                text: `${roleName} has been deleted.`,
                icon: 'success',
                confirmButtonText: 'OK'
            })

            // Refresh the roles list
            fetchRoles()
        } catch (error) {
            console.error('Error deleting role:', error)

            // Show error message
            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || 'Failed to delete role.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
        } finally {
            loading.value = false
        }
    }
}

// Function to save a role (create or update)
const saveRole = async () => {
    try {
        const token = localStorage.getItem('X-API-TOKEN')

        if (showEditRoleModal.value) {
            // Update existing role
            await axios.put(`/api/admin/roles/${selectedRoleId.value}`, roleForm.value, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })

            Swal.fire({
                title: 'Updated!',
                text: 'Role has been updated successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            })
        } else {
            // Create new role
            await axios.post('/api/admin/roles', roleForm.value, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })

            Swal.fire({
                title: 'Created!',
                text: 'Role has been created successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            })
        }

        // Close modal and refresh roles
        closeRoleModal()
        fetchRoles()
    } catch (error) {
        console.error('Error saving role:', error)

        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to save role.',
            icon: 'error',
            confirmButtonText: 'OK'
        })
    }
}

// Function to save permissions for a role
const savePermissions = async () => {
    try {
        const token = localStorage.getItem('X-API-TOKEN')

        await axios.post(`/api/roles/${selectedRoleId.value}/permissions`, {
            permissions: selectedPermissions.value
        }, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })

        Swal.fire({
            title: 'Updated!',
            text: 'Permissions have been updated successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        })

        closePermissionsModal()
    } catch (error) {
        console.error('Error saving permissions:', error)

        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to save permissions.',
            icon: 'error',
            confirmButtonText: 'OK'
        })
    }
}

// Function to close role modal
const closeRoleModal = () => {
    showAddRoleModal.value = false
    showEditRoleModal.value = false
    selectedRole.value = null
    selectedRoleId.value = null
    roleForm.value = {
        name: '',
        description: ''
    }
}

// Function to close permissions modal
const closePermissionsModal = () => {
    showPermissionsModal.value = false
    selectedRole.value = null
    selectedRoleId.value = null
    selectedPermissions.value = []
}

// On component mount, fetch the roles
onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')  // Redirect to login if no token exists
        return
    }

    fetchRoles()
})
</script>
