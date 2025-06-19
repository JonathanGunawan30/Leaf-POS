<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div>
                <!-- Header -->
                <div class="flex justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px] mb-4">
                    <span class="text-[#000000] font-medium leading-6">Tax Detail</span>
                </div>

                <!-- Content -->
                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">

                    <!-- SKELETON MODE -->
                    <template v-if="loading">
                        <!-- Simulate 2 fields (tax name & rate) with placeholder -->
                        <div class="flex flex-col gap-6 w-full">
                            <div class="flex flex-col gap-2 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>
                        </div>
                    </template>

                    <!-- DATA MODE -->
                    <template v-else>
                        <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                        <div class="flex flex-1 justify-start items-start flex-row gap-2.5">
                                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Tax</p>
                                                </div>

                                                <!-- tax -->
                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{ tax.name }}</p>
                                                </div>

                                                <!-- rate -->
                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px] mt-4">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Rate</p>
                                                </div>
                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                                        {{ parseFloat(tax.rate).toFixed(0) }}%
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Button -->
                            <div @click="goBack"
                                 class="w-[186px] h-[48px] flex justify-center cursor-pointer items-center flex-row gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:bg-[#256b41] transition-colors duration-200 rounded-[10px]">
                                <button class="text-[#FFFFFF] text-sm font-medium leading-6">Close</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </Sidebar>
    </div>
</template>


<script setup>
import Sidebar from '../Components/Sidebar.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const page = usePage()
const auth = page.props.auth
const tax = ref({ name: 'Loading...' })
const loading = ref(true)

const goBack = () => {
    router.visit('/taxes/all')
}

const fetchTaxDetails = async () => {
    try {
        loading.value = true

        const taxId = window.location.pathname.split('/').pop()

        const response = await axios.get(`/api/taxes/${taxId}`)
        tax.value = response.data.data
    } catch (error) {
        console.error('Error fetching tax details:', error)

        Swal.fire({
            title: 'Error',
            text: error.response?.data?.message || 'Failed to load tax details.',
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200 '
            },
            buttonsStyling: false
        })

        goBack()
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    fetchTaxDetails()
})
</script>
