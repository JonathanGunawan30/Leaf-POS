<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Tax</span></div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Tax Name & Rate</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Name <span class="text-red-500">*</span></p>
                                    <p class="text-gray-500 text-xs">Please enter a clear and unique tax name, e.g., PPN - 10% or PPN - 11%.</p>
                                </div>
                                <input v-model="taxForm.name" placeholder="Enter tax name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Rate (%) <span class="text-red-500">*</span></p>
                                </div>
                                <input v-model="taxForm.rate" type="number" placeholder="Enter tax rate"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createTax"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create Tax</span>
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

const taxForm = ref({
    name: '',
    rate: '',
})

const createTax = async () => {
    try {
        const response = await axios.post('/api/taxes', taxForm.value, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('Tax created:', response.data)

        taxForm.value = {
            name: '',
            rate: '',
        }

        Swal.fire({
            title: 'Success!',
            text: 'Tax has been successfully created.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
            buttonsStyling: false
        })
    } catch(error) {
        console.error('Failed to create tax:', error.response?.data || error.message)

        let errorMessage = 'Failed to create tax. Please check the data.';
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
