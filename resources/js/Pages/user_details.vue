<template>

    <div v-if="loading" class="py-10 text-gray-500">
        <div class="bg-gray-100">
            <sidebar>
                <!-- Header -->
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">User Detail</span>
                </div>

                <!-- Skeleton Loading Detail -->
                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-white rounded-[10px] animate-pulse">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <!-- Name -->
                            <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="w-20 h-4 bg-gray-300 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="w-20 h-4 bg-gray-300 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="w-20 h-4 bg-gray-300 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="w-20 h-4 bg-gray-300 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button Skeleton -->
                        <div class="w-[186px] h-[48px] bg-gray-300 rounded-[10px]"></div>
                    </div>
                </div>



            </sidebar>
        </div>

    </div>

    <div v-else>

        <div class="bg-gray-100">

            <Sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">User Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">
                    <div
                        class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div
                                class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                <div
                                    class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div class="flex flex-1 justify-start items-start flex-row gap-2.5">
                                        <div
                                            class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div
                                                class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                <p
                                                    class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                    Name
                                                </p>
                                            </div>
                                            <div
                                                class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                                <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                                    {{user?.name ?? '-'}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div
                                        class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div
                                            class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                Email
                                            </p>
                                        </div>
                                        <div
                                            class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                          {{ user?.email ?? '-' }}
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div
                                        class="h-[74px] flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div
                                            class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                Role
                                            </p>
                                        </div>
                                        <div
                                            class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                          {{user?.role ?? '-'}}
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div
                                        class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div
                                            class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                Status
                                            </p>
                                        </div>
                                        <div
                                            class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                            <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                                {{ user?.status ?? '-'}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button
                            @click="$inertia.visit(referrer)"
                            class="flex justify-center items-center flex-row gap-2.5 py-[5px] px-[30px] bg-[#2F8451] rounded-[10px] w-[186px] h-[48px]"
                        >
                            <span class="text-[#FFFFFF] text-sm font-medium leading-6">Back</span>
                        </button>
                    </div>
                </div>
            </Sidebar>

        </div>

    </div>




</template>
<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from 'axios'
import {usePage, router} from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const page = usePage()
const user = ref(null)
const loading = ref(true)
const error = ref(null)
const referrer = ref('/users/all')

const userId = computed(() => {
    return page.props.user?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})

onMounted(async () => {
    try {
        if(document.referrer.includes('/users/restore')) {
            referrer.value = '/users/restore'
        } else if(document.referrer.includes('/users/all')){
            referrer.value = '/users/all'
        } else if (localStorage.getItem('userReferrer')){
            referrer.value = localStorage.getItem('userReferrer')
        }

        localStorage.setItem('userReferrer', referrer.value)

        if(!userId.value || isNaN(userId.value)){
            throw new Error('Invalid user ID')
        }

        const response = await axios.get(`/api/admin/users/${userId.value}`)
        console.log('API Response:', response)

        if(!response.data){
            throw new Error('User data not found in response')
        }

        user.value = response.data.data
    } catch (err) {
        error.value = err.message
        console.error('Error details:', {
            message: err.message,
            response: err.response?.data,
            status: err.response?.status
        })
        router.visit(referrer.value)
    } finally {
        loading.value = false
    }
})



</script>
