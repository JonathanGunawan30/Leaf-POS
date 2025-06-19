<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Courier</span></div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Courier Name & Phone</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Courier Name <span class="text-red-500">*</span></p>
                                    <p class="text-gray-500 text-xs">Please enter the courier's full name.</p>
                                </div>
                                <input v-model="courierForm.name" placeholder="Enter courier name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Phone Number <span class="text-red-500">*</span></p>
                                    <p class="text-gray-500 text-xs">Please enter the courier's phone number, e.g., 081234567890.</p>
                                </div>
                                <input v-model="courierForm.phone" type="text" placeholder="Enter courier phone number"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createCourier"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create Courier</span>
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

const courierForm = ref({
    name: '',
    phone: '',
    status: 'available'
})

const createCourier = async () => {
    try {

        const response = await axios.post('/api/couriers', courierForm.value, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('Courier created:', response.data)

        courierForm.value = {
            name: '',
            phone: '',
        }

        Swal.fire({
            title: 'Success!',
            text: 'Courier has been successfully created.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
            buttonsStyling: false
        })
    } catch(error) {
        console.error('Failed to create courier:', error.response?.data || error.message)

        let errorMessage = 'Failed to create courier. Please check the data.';
        let useHtml = false;

        if(error.response?.data?.errors?.message) {
            const messages = error.response.data.errors.message;

            const formattedErrors = Object.entries(messages)
                .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                .join('<br>');

            if(formattedErrors) {
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
            },
            buttonsStyling: false
        })
    }
}

onMounted (async () => {
    const token = localStorage.getItem('X-API-TOKEN')
    if(!token) {
        router.visit('/')
    }
})
</script>
