<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create User</span></div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">User Information</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Full Name <span class="text-red-500">*</span></p>
                                </div>
                                <input v-model="userForm.name" placeholder="Enter full name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Email Address <span class="text-red-500">*</span></p>
                                </div>
                                <input v-model="userForm.email" type="email" placeholder="Enter email address"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Password <span class="text-red-500">*</span></p>
                                </div>
                                <input v-model="userForm.password" type="password" placeholder="Enter password"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Confirm Password <span class="text-red-500">*</span></p>
                                </div>
                                <input v-model="userForm.password_confirmation" type="password" placeholder="Confirm password"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-1 self-stretch justify-start items-start flex-col gap-4">
                        <div
                            class="flex self-stretch flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">User Role</p>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Role <span class="text-red-500">*</span></p></div>
                                <select v-model="userForm.role_id" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createUser"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create User</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref} from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";
import { router } from '@inertiajs/vue3'

const token = localStorage.getItem('X-API-TOKEN')

const api = axios.create({
    baseURL: '/api',
    timeout: 8000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    }
})

const userForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    status: ''
})

const roles = ref([])

const fetchRoles = async () => {
    try {
        const { data } = await api.get('/admin/roles')
        roles.value = data.data
    } catch (error) {
        console.error('Error fetching roles:', error)
    }
}

// Handle form submission
const createUser = async () => {
    try {
        // Validate password match
        if (userForm.value.password !== userForm.value.password_confirmation) {
            Swal.fire({
                title: 'Error!',
                text: 'Passwords do not match.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
            return
        }

        const response = await axios.post('/api/admin/users', userForm.value, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('User created:', response.data)

        // Reset form
        userForm.value = {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            role_id: '',
            status: ''
        }

        Swal.fire({
            title: 'Success!',
            text: 'User has been successfully created.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            }
        })

    } catch (error) {
        console.error('Failed to create user:', error.response?.data || error.message)


        let errorMessage = 'Failed to create user. Please check the data.';
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
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200 '
            }
        })
    }
}

onMounted(async () => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    await fetchRoles()
})
</script>
