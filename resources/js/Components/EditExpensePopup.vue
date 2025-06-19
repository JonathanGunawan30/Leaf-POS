<template>
    <div v-show="show" class="fixed inset-0 overflow-hidden z-50 transition-opacity duration-300">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" @click="closePopup"></div>

        <!-- Popup Panel -->
        <div class="absolute inset-y-0 right-0 max-w-[750px] w-full bg-white shadow-xl transform transition-transform duration-300 ease-in-out"
             :class="{ 'translate-x-0': show, 'translate-x-full': !show }">


            <!-- Header -->
            <div class="flex justify-between items-center p-5 border-b border-gray-300">
                <span class="text-black font-bold">EDIT EXPENSE</span>
                <button @click="closePopup" class="text-black hover:text-gray-700">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0006 8.82208L14.1255 4.69727L15.304 5.87577L11.1791 10.0006L15.304 14.1253L14.1255 15.3038L10.0006 11.1791L5.87584 15.3038L4.69733 14.1253L8.82213 10.0006L4.69733 5.87577L5.87584 4.69727L10.0006 8.82208Z" fill="black"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-5 overflow-y-auto h-[calc(100%-120px)]">
                <form @submit.prevent="updateExpense">

                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Expense Amount <span class="text-red-500">*</span></p>
                        <input
                            v-model="expenseForm.amount"
                            type="number"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Expense Description</p>
                        <input
                            v-model="expenseForm.description"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Expense Date  <span class="text-red-500">*</span></p>
                        <input
                            v-model="expenseForm.expense_date"
                            type="date"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Expense Notes</p>
                        <input
                            v-model="expenseForm.note"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <!-- Category Select -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Expense Category <span class="text-red-500">*</span></p>
                        <select
                            v-model="expenseForm.category_id"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            required
                        >
                            <option value="" disabled selected>Select a category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- User Select -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">User <span class="text-red-500">*</span></p>
                        <select
                            v-model="expenseForm.user_id"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            required
                        >
                            <option value="" disabled selected>Select a user</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>




                </form>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-0 left-0 right-0 p-5 border-t border-gray-300 bg-white flex justify-center">
                <div class="flex gap-4 w-full max-w-md">
                    <button
                        @click="closePopup"
                        class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-black hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="updateExpense"
                        class="flex-1 py-2 px-4 bg-green-700 text-white rounded-lg hover:bg-green-800"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    show: Boolean,
    expenseId: Number
});

const emit = defineEmits(['close', 'updated']);

const expenseForm = ref({
    amount: '',
    description: '',
    expense_date: '',
    category_id: '',
    user_id: '',
    note: ''
})

const categories = ref([]);
const users = ref([]);


const loading = ref(false);

watch(() => props.expenseId, async (newId) => {
    if (newId && props.show) {
        await fetchExpense(newId);
    }
}, { immediate: true });

watch(() => props.show, async (newShow) => {
    if (newShow && props.expenseId) {
        await fetchData();
        await fetchExpense(props.expenseId);
    }
});


const fetchData = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const [categoryResponse, userResponse] = await Promise.all([
            axios.get('/api/expense-categories', {
                headers: { Authorization: `Bearer ${token}` }
            }),
            axios.get('/api/admin/users', {
                headers: { Authorization: `Bearer ${token}` }
            })
        ]);

        categories.value = categoryResponse.data.data;
        users.value = userResponse.data.data;
    } catch (error) {
        console.log('Error fetching data: ', error);
    } finally {
        loading.value = false;
    }
};


const fetchExpense = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if(!token) return;

        const response = await axios.get('/api/expenses/' + props.expenseId, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        const expense = response.data.data;

        expenseForm.value = {
            amount: Number(expense.amount) % 1 === 0
                ? Number(expense.amount).toString()
                : Number(expense.amount).toFixed(2).replace('.', ','),
            description: expense.description,
            expense_date: expense.expense_date,
            category_id: expense.category?.id,
            user_id: expense.user?.id,
            note: expense.note
        };

    } catch (error) {
        console.log('Error fetching expense data: ', error);

    } finally{
        loading.value = false;
    }

}

const updateExpense = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if(!token) return;



        const formData = new FormData();
        formData.append('amount', expenseForm.value.amount);
        formData.append('description', expenseForm.value.description);
        formData.append('expense_date', expenseForm.value.expense_date);
        formData.append('category_id', expenseForm.value.category_id);
        formData.append('user_id', expenseForm.value.user_id);
        formData.append('note', expenseForm.value.note);

        const response = await axios.post(`/api/expenses/${props.expenseId}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'X-HTTP-Method-Override': 'PATCH'
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Expense data has been updated',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating expense:', error);

        let errorMessage = 'Failed to update expense. Please check the data or re-login.';
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
            icon: 'error'
        });
    } finally {
        loading.value = false;
    }
}

const closePopup = () => {
    emit('close');
};

onMounted(() => {
    fetchExpense();
    fetchData();
});

</script>
