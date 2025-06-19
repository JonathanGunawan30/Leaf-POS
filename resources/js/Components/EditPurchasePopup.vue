<template>
    <div v-if="show" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex justify-end overflow-hidden">
        <div class="bg-white w-full max-w-3xl h-full shadow-lg relative overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-300">
                <h2 class="text-lg font-bold text-black">EDIT PURCHASE</h2>
                <button @click="closePopup" class="text-black hover:text-gray-600">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0006 8.82208L14.1254 4.69727L15.3039 5.87577L11.1791 10.0006L15.3039 14.1253L14.1254 15.3038L10.0006 11.1791L5.87578 15.3038L4.69727 14.1253L8.82207 10.0006L4.69727 5.87577L5.87578 4.69727L10.0006 8.82208Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="updatePurchase" class="px-5 py-4">
                <!-- Purchase Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-black mb-4">Purchase</h3>

                    <!-- Row 1: Invoice Number & Status -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Invoice Number</label>
                            <input
                                v-model="purchaseForm.invoice_number"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Status</label>
                            <div class="relative">
                                <select
                                    v-model="purchaseForm.status"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Total Discount & Shipping Amount -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Total Discount</label>
                            <input
                                v-model.number="purchaseForm.total_discount"
                                type="number"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Shipping Amount</label>
                            <input
                                v-model.number="purchaseForm.shipping_amount"
                                type="number"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <!-- Row 3: Purchase Date & Due Date -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Purchase Date</label>
                            <input
                                v-model="purchaseForm.purchase_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Due Date</label>
                            <input
                                v-model="purchaseForm.due_date "
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <!-- Row 4: Estimated Arrival & Actual Arrival -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Estimated Arrival</label>
                            <input
                                v-model="purchaseForm.estimated_arrival_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Actual Arrival</label>
                            <input
                                v-model="purchaseForm.actual_arrival_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <!-- Row 5: Supplier -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-black mb-1">Supplier</label>
                        <div class="relative">
                            <select
                                v-model="purchaseForm.supplier_id"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                            >
                                <option disabled value="">Select Supplier</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{  supplier.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Product Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Product</h3>
                        <button
                            type="button"
                            @click="addProduct"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Product
                        </button>
                    </div>

                    <div v-for="(detail, index) in purchaseForm.purchase_details" :key="index" class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Product {{ index + 1 }}</span>
                            <button
                                v-if="purchaseForm.purchase_details.length > 1"
                                type="button"
                                @click="removeProduct(index)"
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
                                        v-model="detail.product_id"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Product</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{  product.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Quantity</label>
                                <input
                                    v-model.number="detail.quantity"
                                    type="number"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Note</label>
                            <textarea
                                v-model="detail.note"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                                placeholder='Enter notes...'
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Payment</h3>
                        <button
                            type="button"
                            @click="addPayment"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Payment
                        </button>
                    </div>

                    <div v-for="(payment, index) in purchaseForm.purchase_payments" :key="index" class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Payment {{ index + 1 }}</span>
                            <button
                                v-if="purchaseForm.purchase_payments.length > 1"
                                type="button"
                                @click="removePayment(index)"
                                class="text-red-500 hover:text-red-700 text-sm"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-black mb-1">Amount</label>
                            <input
                                v-model.number="payment.amount"
                                type="number"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Payment Date</label>
                                <input
                                    v-model="payment.payment_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Payment Due Date</label>
                                <input
                                    v-model="payment.due_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Payment Method</label>
                                <div class="relative">
                                    <select
                                        v-model="payment.payment_method"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="giro">Giro</option>
                                        <option value="paypal">Paypal</option>
                                        <option value="e_wallet">E-Wallet</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Payment Status</label>
                                <div class="relative">
                                    <select
                                        v-model="payment.status"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Status</option>
                                        <option value="paid">Paid</option>
                                        <option value="partially_paid">Partially Paid</option>
                                        <option value="unpaid">Unpaid</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Note</label>
                            <textarea
                                v-model="payment.note"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                                placeholder='Enter notes...'
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Taxes Section -->
                <div class="mb-6" v-if="purchaseForm.taxes && purchaseForm.taxes.length > 0">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Taxes</h3>
                        <button
                            type="button"
                            @click="addTax"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Tax
                        </button>
                    </div>

                    <div v-for="(tax, index) in purchaseForm.taxes" :key="index" class="mb-3 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Tax {{ index + 1 }}</span>
                            <button
                                v-if="purchaseForm.taxes.length > 1"
                                type="button"
                                @click="removeTax(index)"
                                class="text-red-500 hover:text-red-700 text-sm"
                            >
                                Remove
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Select Tax</label>
                            <select
                                v-model="tax.id"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            >
                                <option disabled value="">Select Tax</option>
                                <option v-for="t in taxes" :key="t.id" :value="t.id">
                                    {{ t.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
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
                            {{ loading ? 'Saving...' : 'Save' }}
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
    purchaseId: Number
});

const emit = defineEmits(['close', 'updated']);

const purchaseForm = ref({
    invoice_number: '',
    status: '',
    total_discount: 0,
    shipping_amount: 0,
    purchase_date: '',
    due_date: '',
    estimated_arrival_date: '',
    actual_arrival_date: '',
    supplier_id: '',
    purchase_details: [
        {
            product_id: '',
            quantity: '',
            note: '',
        }
    ],
    purchase_payments: [
        {
            payment_date: '',
            amount: '',
            due_date: '',
            payment_method: '',
            status: '',
            note: '',
        }
    ],
    taxes: [
        {
            tax_id: '',
        }
    ]
})

const suppliers = ref([])
const products = ref([])
const taxes = ref([])
const loading = ref(false);

const addProduct = () => {
    purchaseForm.value.purchase_details.push({
        product_id: '',
        quantity: '',
        note: '',
    });
};

const removeProduct = (index) => {
    if (purchaseForm.value.purchase_details.length > 1) {
        purchaseForm.value.purchase_details.splice(index, 1);
    }
};

const addPayment = () => {
    purchaseForm.value.purchase_payments.push({
        payment_date: '',
        amount: '',
        due_date: '',
        payment_method: '',
        status: '',
        note: '',
    });
};

const removePayment = (index) => {
    if (purchaseForm.value.purchase_payments.length > 1) {
        purchaseForm.value.purchase_payments.splice(index, 1);
    }
};

const addTax = () => {
    purchaseForm.value.taxes.push({
        tax_id: '',
    });
};

const removeTax = (index) => {
    if (purchaseForm.value.taxes.length > 1) {
        purchaseForm.value.taxes.splice(index, 1);
    }
};

const fetchData = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const [supplierResponse, productResponse, taxResponse] = await Promise.all([
            axios.get('/api/suppliers?per_page=1000000000', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }),
            axios.get('/api/products?per_page=1000000000', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }),
            axios.get('/api/taxes?per_page=1000000000', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
        ])

        suppliers.value = supplierResponse.data.data;
        products.value = productResponse.data.data;
        taxes.value = taxResponse.data.data;

    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

const fetchPurchase = async (id) => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const response = await axios.get(`/api/purchases/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        const purchase = response.data.data;
        console.log('Full purchase response:', purchase);

        purchaseForm.value = {
            invoice_number: purchase.invoice_number || '',
            status: purchase.status || '',
            total_discount: purchase.total_discount || 0,
            shipping_amount: purchase.shipping_amount || 0,
            purchase_date: purchase.purchase_date || '',
            due_date: purchase.due_date || '',
            estimated_arrival_date: purchase.estimated_arrival_date || '',
            actual_arrival_date: purchase.actual_arrival_date || '',
            supplier_id: purchase.supplier?.id || '',
            purchase_details: purchase.purchase_details && purchase.purchase_details.length > 0
                ? purchase.purchase_details.map(detail => ({
                    product_id: Number(detail.product_id) || '',
                    quantity: detail.quantity || '',
                    note: detail.note || '',
                }))
                : [{
                    product_id: '',
                    quantity: '',
                    note: '',
                }],


            purchase_payments: purchase.purchase_payments.map(payment => ({
                id: payment.id,
                payment_date: payment.payment_date || '',
                amount: payment.amount || '',
                due_date: payment.due_date || '',
                payment_method: payment.payment_method || '',
                status: payment.status || '',
                note: payment.note || ''
            })),
            taxes: purchase.taxes && purchase.taxes.length > 0
                ? purchase.taxes
                : [{
                    tax_id: '',
                }]
        };

    } catch (error) {
        console.error('Error fetching purchase:', error);
    } finally {
        loading.value = false;
    }
}

watch(() => props.show, async (newShow) => {
    if (newShow && props.purchaseId) {
        await fetchData();
        await fetchPurchase(props.purchaseId);
    }
}, { immediate: true });

watch(() => props.purchaseId, async (newId) => {
    if (newId && props.show) {
        await fetchPurchase(newId);
    }
});

const updatePurchase = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const formData = new FormData();
        formData.append('invoice_number', purchaseForm.value.invoice_number);
        formData.append('status', purchaseForm.value.status);
        formData.append('total_discount', purchaseForm.value.total_discount);
        formData.append('shipping_amount', purchaseForm.value.shipping_amount);
        formData.append('purchase_date', purchaseForm.value.purchase_date);
        formData.append('due_date', purchaseForm.value.due_date);
        formData.append('estimated_arrival_date', purchaseForm.value.estimated_arrival_date);
        formData.append('actual_arrival_date', purchaseForm.value.actual_arrival_date);
        formData.append('supplier_id', purchaseForm.value.supplier_id);


        purchaseForm.value.purchase_details.forEach((detail, index) => {
            Object.keys(detail).forEach(key => {
                if (detail[key] !== null && detail[key] !== undefined) {
                    formData.append(`purchase_details[${index}][${key}]`, detail[key]);
                } else {
                    formData.append(`purchase_details[${index}][${key}]`, '');
                }
            });
        });

        purchaseForm.value.purchase_payments.forEach((payment, index) => {
            Object.keys(payment).forEach(key => {
                if (payment[key] !== null && payment[key] !== undefined) {
                    formData.append(`purchase_payments[${index}][${key}]`, payment[key]);
                } else {
                    formData.append(`purchase_payments[${index}][${key}]`, '');
                }
            });
        });

        const taxIdsToSync = purchaseForm.value.taxes
            .map(tax => tax.id)
            .filter(id => id !== null && id !== undefined && id !== '');

        if (taxIdsToSync.length > 0) {
            taxIdsToSync.forEach((taxId, index) => {
                formData.append(`taxes[${index}][tax_id]`, taxId);
            });
        }

        console.log('--- FormData Content SPADAAAAAAAAAAAAAAA ---');
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ': ' + pair[1]);
        }
        console.log('--- End FormData Content SPADAAAAAAAAAAAA ---');
        const response = await axios.post(`/api/purchases/${props.purchaseId}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'X-HTTP-Method-Override': 'PATCH'
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Purchase data has been updated',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating purchase:', error);

        let errorMessage = 'Failed to update purchase. Please check the data or re-login.';
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
}

onMounted(async () => {
    if (props.show && props.purchaseId) {
        await fetchData();
        await fetchPurchase(props.purchaseId);
    }
})
</script>
