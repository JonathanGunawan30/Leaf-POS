<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Expense</span></div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Basic Information</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Amount <span class="text-red-500">*</span></p>
                                </div>
                                <input
                                    v-model="formattedAmount"
                                    @input="onAmountInput"
                                    placeholder="Enter amount"
                                    type="text"
                                    inputmode="numeric"
                                    class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                    required
                                />

                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Description</p>
                                </div>
                                <textarea
                                    v-model="expenseForm.description"
                                    placeholder="Enter description"
                                    maxlength="500"
                                    class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[120px] text-[#000000] text-[15px] leading-6 outline-none resize-none"
                                ></textarea>
                                <p class="text-xs text-gray-500 self-end">Max 500 characters</p>
                            </div>

                        </div>
                    </div>
                    <div class="flex flex-1 self-stretch justify-start items-start flex-col gap-4">
                        <div class="flex self-stretch flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">

                            <p class="self-stretch text-[#000000] font-medium leading-6">Expense Detail</p>

                            <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">

                                <!-- Category -->
                                <div class="flex self-stretch justify-start items-start flex-row gap-4 w-full">
                                    <!-- Category -->
                                    <div class="flex flex-col gap-[5px] w-full">
                                        <p class="text-[#000000] text-xs leading-6">
                                            Category<span class="text-red-500">*</span>
                                        </p>
                                        <select
                                            v-model="expenseForm.category_id"
                                            class="w-full py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none appearance-none"
                                            required
                                        >
                                            <option value="" disabled selected>Select a category</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Expense Date -->
                                    <div class="flex flex-col gap-[5px] w-full">
                                        <p class="text-[#000000] text-xs leading-6">
                                            Expense Date<span class="text-red-500">*</span>
                                        </p>
                                        <input
                                            type="date"
                                            v-model="expenseForm.expense_date"
                                            class="w-full py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                            required
                                        />
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Note</p>
                                    <textarea
                                        v-model="expenseForm.note"
                                        placeholder="Enter your note here"
                                        maxlength="500"
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[120px] text-[#000000] text-[15px] leading-6 outline-none resize-none"
                                    ></textarea>
                                    <p class="text-xs text-gray-500 self-end">Max 500 characters</p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createExpense"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create Expense</span>
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

const expenseForm = ref({
    amount: '',
    description: '',
    expense_date: '',
    category_id: '',
    user_id: '',
    note: ''
});

const formattedAmount = ref('')

const onAmountInput = (e) => {
    let rawValue = e.target.value.replace(/\D/g, '');
    let numberValue = parseInt(rawValue || '0');

    expenseForm.value.amount = numberValue;

    formattedAmount.value = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(numberValue);
}


const categories = ref([]);
const users = ref([]);

const fetchCategories = async () => {
    try {
        const { data } = await api.get('/expense-categories')
        categories.value = data.data
    } catch (error) {
        console.error('Error fetching categories: ', error)
    }
}

const fetchUsers = async () => {
    try {
        const { data } = await api.get('/admin/users')
        users.value = data.data
    } catch (error) {
        console.error('Error fetching users: ', error)
    }
}

const createExpense = async () => {
    try {
        const response = await axios.post('/api/expenses', expenseForm.value, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('Expense created:', response.data)

        expenseForm.value = {
            amount: '',
            description: '',
            expense_date: '',
            category_id: '',
            user_id: '',
            note: ''
        };

        formattedAmount.value = '';


        Swal.fire({
            title: 'Success!',
            text: 'Expense has been successfully created.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            }
        })

    } catch (error) {
        console.error('Failed to create expense:', error.response?.data || error.message)


        let errorMessage = 'Failed to create expense. Please check the data.';
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


    await fetchCategories()
    await fetchUsers()

    console.log('Initial category_id:', expenseForm.value.category_id)
})
</script>
