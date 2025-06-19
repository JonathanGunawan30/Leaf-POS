<template>
    <div class="bg-gray-100">
        <Sidebar :auth="auth">
            <div>
                <!-- Header -->
                <div class="flex justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px] mb-4">
                    <span class="text-[#000000] font-medium leading-6">Expense Detail</span>
                </div>

                <!-- Content -->
                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">

                    <!-- SKELETON MODE -->
                    <template v-if="loading">
                        <div class="flex flex-col gap-[30px] w-full">
                            <div class="flex flex-col gap-2.5 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>
                            <div class="flex flex-col gap-2.5 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>
                            <div class="flex flex-col gap-2.5 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>
                            <div class="flex flex-col gap-2.5 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>
                            <div class="flex flex-col gap-2.5 w-full">
                                <div class="h-[18px] w-24 bg-gray-200 animate-pulse rounded"></div>
                                <div class="h-[45px] bg-gray-200 animate-pulse rounded-[10px] w-full"></div>
                            </div>

                            <!-- Skeleton for Button -->
                            <div class="w-[186px] h-[48px] bg-gray-200 animate-pulse rounded-[10px]"></div>
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
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Expense Amount</p>
                                                </div>

                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{ new Intl.NumberFormat('id-ID', {
                                                        style: 'currency',
                                                        currency: 'IDR',
                                                        minimumFractionDigits: 0
                                                    }).format(expense.amount) }}</p>
                                                </div>

                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Description</p>
                                                </div>

                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{expense.description}}</p>
                                                </div>

                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Expense Date</p>
                                                </div>

                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{expense.expense_date}}</p>
                                                </div>

                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Expense Notes</p>
                                                </div>

                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{expense.note}}</p>
                                                </div>

                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Expense Category</p>
                                                </div>

                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{expense.category.name}}</p>
                                                </div>

                                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                                    <p class="self-stretch text-[#000000] text-sm font-medium leading-6">Expense Created By</p>
                                                </div>

                                                <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-[#AEADAD] rounded-[10px]">
                                                    <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">{{expense.user.name}} - {{expense.user.role.name}}</p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Close Button -->
                            <div @click="$inertia.visit(referrer)"
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
import {ref, onMounted, computed} from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const page = usePage()
const auth = page.props.auth
const expense = ref({ name: 'Loading...' })
const loading = ref(true)
const referrer = ref('/expenses/all')
const expenseId = computed(() => {
    return page.props.customer?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})
const fetchExpenseDetails = async () => {
    try {
        loading.value = true

        const expenseId = window.location.pathname.split('/').pop()

        const response = await axios.get(`/api/expenses/${expenseId}`)
        expense.value = response.data.data
    } catch (error) {
        console.error('Error fetching expense details:', error)

        const errors = error.response?.data?.errors?.message;
        let errorHtml = '<p>Failed to fetching expense details.</p>';

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

        goBack()
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    try {
        if(document.referrer.includes('/expenses/restore')) {
            referrer.value = '/expenses/restore'
        } else if(document.referrer.includes('/expenses/all')) {
            referrer.value = '/expenses/all'
        } else if (localStorage.getItem('expenseReferrer')){
            referrer.value = localStorage.getItem('expenseReferrer')
        }

        localStorage.setItem('expenseReferrer', referrer.value)

        if(!expenseId.value || isNaN(expenseId.value)) {
            throw new Error('Invalid expense ID.')
        }

        const token = localStorage.getItem('X-API-TOKEN')

        const response = await axios.get(`/api/expenses/${expenseId.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('API Response:', response)
        if(!response.data){
            throw new Error('Expense data not found in response')
        }

        expense.value = response.data.data


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
