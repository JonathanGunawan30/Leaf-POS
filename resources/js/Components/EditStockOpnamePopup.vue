<template>
    <div v-if="show" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex justify-end overflow-hidden">
        <div class="bg-white w-full max-w-3xl h-full shadow-lg relative overflow-y-auto">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-300">
                <h2 class="text-lg font-bold text-black">EDIT STOCK OPNAME</h2>
                <button @click="closePopup" class="text-black hover:text-gray-600">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0006 8.82208L14.1254 4.69727L15.3039 5.87577L11.1791 10.0006L15.3039 14.1253L14.1254 15.3038L10.0006 11.1791L5.87578 15.3038L4.69727 14.1253L8.82207 10.0006L4.69727 5.87577L5.87578 4.69727L10.0006 8.82208Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="updateStockOpname" class="px-5 py-4">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-black mb-4">Stock Opname Details</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Stock Opname ID</label>
                            <input
                                v-model="stockOpnameForm.id"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 bg-gray-100 cursor-not-allowed"
                                readonly
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Conducted By</label>
                            <input
                                v-model="stockOpnameForm.user_name"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 bg-gray-100 cursor-not-allowed"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Opname Date</label>
                            <input
                                v-model="stockOpnameForm.opname_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Location</label>
                            <input
                                v-model="stockOpnameForm.location"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Status</label>
                            <div class="relative">
                                <select
                                    v-model="stockOpnameForm.status"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Status</option>
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Approved By</label>
                            <input
                                v-model="stockOpnameForm.approved_by"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                                placeholder="Enter name..."
                            />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-black mb-1">Notes</label>
                        <textarea
                            v-model="stockOpnameForm.notes"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                            placeholder='Enter notes...'
                        ></textarea>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Opname Items</h3>
                        <button
                            type="button"
                            @click="addItem"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Item
                        </button>
                    </div>

                    <div v-for="(item, index) in stockOpnameForm.items" :key="`item-${index}`"
                         class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Item {{ index + 1 }}</span>
                            <button
                                v-if="stockOpnameForm.items.length > 1"
                                type="button"
                                @click="removeItem(index)"
                                class="text-red-500 hover:text-red-700 text-sm"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Product</label>
                                <div class="relative">
                                    <select
                                        v-model="item.product_id"
                                        @change="updateProductInfo(index)"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Product</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.name }} ({{ product.sku }})
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">System Stock</label>
                                <input
                                    v-model.number="item.system_stock"
                                    type="number"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 bg-gray-100 cursor-not-allowed"
                                    readonly
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Actual Stock</label>
                                <input
                                    v-model.number="item.actual_stock"
                                    type="number"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                                    min="0"
                                    @input="calculateDifference(index)"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Difference</label>
                                <input
                                    v-model.number="item.difference"
                                    type="number"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 bg-gray-100 cursor-not-allowed"
                                    readonly
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Note</label>
                            <textarea
                                v-model="item.notes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                                placeholder='Enter notes for this item...'
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-300 pt-4">
                    <div class="flex justify-center gap-4">
                        <button
                            type="button"
                            @click="closePopup"
                            class="flex-1 max-w-48 h-12 px-8 py-2 bg-white border border-gray-400 rounded-lg text-sm font-medium text-black hover:bg-gray-50 focus:outline-none"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="flex-1 max-w-48 h-12 px-8 py-2 bg-lp-green rounded-lg text-sm font-medium text-white hover:bg-green-800 focus:outline-none disabled:opacity-50"
                        >
                            {{ loading ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import {ref, watch, onMounted, computed} from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    show: Boolean,
    stockOpnameId: Number
});

const emit = defineEmits(['close', 'updated']);

const stockOpnameForm = ref({
    id: null,
    user_id: null,
    user_name: '',
    approved_by: '',
    location: '',
    opname_date: '',
    status: '',
    notes: '',
    total_items: 0,
    items: [
        {
            id: null,
            product_id: '',
            system_stock: 0,
            actual_stock: 0,
            difference: 0,
            notes: '',
        }
    ],
});

const products = ref([]);
const loading = ref(false);

const calculateDifference = (index) => {
    const item = stockOpnameForm.value.items[index];
    item.difference = (item.actual_stock || 0) - (item.system_stock || 0);
};

const updateProductInfo = (index) => {
    const selectedProduct = products.value.find(p => p.id === stockOpnameForm.value.items[index].product_id);

};

const addItem = () => {
    stockOpnameForm.value.items.push({
        id: null,
        product_id: '',
        system_stock: 0,
        actual_stock: 0,
        difference: 0,
        notes: '',
    });
};

const removeItem = (index) => {
    if (stockOpnameForm.value.items.length > 1) {
        stockOpnameForm.value.items.splice(index, 1);
    }
};

const fetchData = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const productResponse = await axios.get('/api/products?per_page=1000000000', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        products.value = productResponse.data.data;

    } catch (error) {
        console.error('Error fetching data:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to load product data. Please try again.',
            icon: 'error'
        });
    } finally {
        loading.value = false;
    }
};

const fetchStockOpname = async (id) => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const response = await axios.get(`/api/stock-opnames/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        const opname = response.data.data;
        console.log('Full stock opname response:', opname);

        stockOpnameForm.value = {
            id: opname.id,
            user_id: opname.user_id,
            user_name: opname.user?.name ?? '-',
            approved_by: opname.approved_by,
            location: opname.location || '',
            opname_date: opname.opname_date || '',
            status: opname.status || '',
            notes: opname.notes || '',
            total_items: opname.total_items || 0,
            items: opname.items && opname.items.length > 0
                ? opname.items.map(item => ({
                    id: item.id,
                    product_id: Number(item.product_id) || '',
                    system_stock: item.system_stock || 0,
                    actual_stock: item.actual_stock || 0,
                    difference: item.difference || 0,
                    notes: item.notes || '',
                }))
                : [{
                    id: null,
                    product_id: '',
                    system_stock: 0,
                    actual_stock: 0,
                    difference: 0,
                    notes: '',
                }],
        };

    } catch (error) {
        console.error('Error fetching stock opname:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to load stock opname data. Please try again.',
            icon: 'error'
        });
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, async (newShow) => {
    if (newShow && props.stockOpnameId) {
        await fetchData();
        await fetchStockOpname(props.stockOpnameId);
    }
}, {immediate: true});

watch(() => props.stockOpnameId, async (newId) => {
    if (newId && props.show) {
        await fetchStockOpname(newId);
    }
});

const updateStockOpname = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const formData = new FormData();
        formData.append('location', stockOpnameForm.value.location || '');
        formData.append('opname_date', stockOpnameForm.value.opname_date || '');
        formData.append('status', stockOpnameForm.value.status || '');
        formData.append('notes', stockOpnameForm.value.notes || '');
        if (stockOpnameForm.value.approved_by) {
            formData.append('approved_by', stockOpnameForm.value.approved_by);
        }

        formData.append('total_items', stockOpnameForm.value.items.length);

        stockOpnameForm.value.items.forEach((item, index) => {
            Object.keys(item).forEach(key => {
                if (key === 'difference') {
                    formData.append(`items[${index}][${key}]`, item[key]);
                } else if (item[key] !== null && item[key] !== undefined) {
                    formData.append(`items[${index}][${key}]`, item[key]);
                } else {
                    formData.append(`items[${index}][${key}]`, '');
                }
            });
        });

        console.log('--- FormData Content ---');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        console.log('--- End FormData Content ---');

        const response = await axios.post(`/api/stock-opnames/${props.stockOpnameId}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'X-HTTP-Method-Override': 'PATCH'
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Stock Opname data has been updated',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        });

        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating stock opname:', error);

        let errorMessage = 'Failed to update stock opname. Please check the data or re-login.';
        let useHtml = false;

        if (error.response?.data?.errors) {
            const messages = error.response.data.errors.message;

            if (typeof messages === 'string') {
                errorMessage = messages;
            } else if (typeof messages === 'object' && messages !== null) {
                const formattedErrors = Object.entries(messages)
                    .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                    .join('<br>');

                if (formattedErrors) {
                    errorMessage = formattedErrors;
                    useHtml = true;
                }
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
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
};

const closePopup = () => {
    emit('close');
};

onMounted(async () => {
    if (props.show && props.stockOpnameId) {
        await fetchData();
        await fetchStockOpname(props.stockOpnameId);
    }
});
</script>
