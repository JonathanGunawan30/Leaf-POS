<template>
    <div v-if="show" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex justify-end overflow-hidden">
        <div class="bg-white w-full max-w-3xl h-full shadow-lg relative overflow-y-auto">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-300">
                <h2 class="text-lg font-bold text-black">EDIT PURCHASE RETURN</h2>
                <button @click="closePopup" class="text-black hover:text-gray-600">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0006 8.82208L14.1254 4.69727L15.3039 5.87577L11.1791 10.0006L15.3039 14.1253L14.1254 15.3038L10.0006 11.1791L5.87578 15.3038L4.69727 14.1253L8.82207 10.0006L4.69727 5.87577L5.87578 4.69727L10.0006 8.82208Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="updatePurchaseReturn" class="px-5 py-4">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-black mb-4">Purchase Return Details</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Invoice Number</label>
                            <input
                                v-model="purchaseReturnForm.invoice_number"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                                disabled
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Return Date</label>
                            <input
                                v-model="purchaseReturnForm.return_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Status</label>
                            <div class="relative">
                                <select
                                    v-model="purchaseReturnForm.status"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Payment Status</label>
                            <div class="relative">
                                <select
                                    v-model="purchaseReturnForm.payment_status"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Payment Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="partially_paid">Partially Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Compensation Method</label>
                            <div class="relative">
                                <select
                                    v-model="purchaseReturnForm.compensation_method"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Compensation Method</option>
                                    <option value="replacement">Replacement</option>
                                    <option value="refund">Refund</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Reason</label>
                            <textarea
                                v-model="purchaseReturnForm.reason"
                                rows="1"
                                class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                                placeholder='Enter reason for return...'
                            ></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Estimated Arrival Date</label>
                                <input
                                    v-model="purchaseReturnForm.estimated_arrival_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                        </div>
                        <div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Actual Arrival Date</label>
                                <input
                                    v-model="purchaseReturnForm.actual_arrival_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Returned Products</h3>
                        <button
                            type="button"
                            @click="addProductReturnDetail"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Product
                        </button>
                    </div>

                    <div v-for="(detail, index) in purchaseReturnForm.purchase_details" :key="index" class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Product {{ index + 1 }}</span>
                            <button
                                v-if="purchaseReturnForm.purchase_details.length > 1"
                                type="button"
                                @click="removeProductReturnDetail(index)"
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
                                            {{ product.name }}
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
                                placeholder='Enter notes for returned product...'
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Refund/Payment Details</h3>
                        <button
                            type="button"
                            @click="addPurchaseReturnPayment"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Payment
                        </button>
                    </div>

                    <div v-for="(payment, index) in purchaseReturnForm.purchase_return_payments" :key="index" class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Payment {{ index + 1 }}</span>
                            <button
                                v-if="purchaseReturnForm.purchase_return_payments.length > 1"
                                type="button"
                                @click="removePurchaseReturnPayment(index)"
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
                                step="0.01"
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
                                <label class="block text-sm font-medium text-black mb-1">Due Date (Payment)</label>
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
                                placeholder='Enter notes for payment...'
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-6" v-if="purchaseReturnForm.taxes && purchaseReturnForm.taxes.length > 0">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Taxes</h3>
                        <button
                            type="button"
                            @click="addPurchaseReturnTax"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Tax
                        </button>
                    </div>

                    <div v-for="(tax, index) in purchaseReturnForm.taxes" :key="index" class="mb-3 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Tax {{ index + 1 }}</span>
                            <button
                                v-if="purchaseReturnForm.taxes.length > 1"
                                type="button"
                                @click="removePurchaseReturnTax(index)"
                                class="text-red-500 hover:text-red-700 text-sm"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-black mb-1">Select Tax</label>
                            <select
                                v-model="tax.tax_id"
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
import {ref, watch, onMounted} from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    show: Boolean,
    purchaseReturnId: Number
});

const emit = defineEmits(['close', 'updated']);


const purchaseReturnForm = ref({
    invoice_number: '',
    return_date: '',
    status: '',
    payment_status: '',
    compensation_method: '',
    purchase_id: null,
    reason: '',
    due_date: null,
    estimated_arrival_date: null,
    actual_arrival_date: null,

    total_amount: 0,
    total_tax: 0,
    total_discount: 0,
    shipping_amount: 0,
    grand_total: 0,

    purchase_details: [
        {
            product_id: '',
            quantity: '',
            note: '',
        }
    ],
    purchase_return_payments: [
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
});

const products = ref([]);
const taxes = ref([]);


const loading = ref(false);


const addProductReturnDetail = () => {
    purchaseReturnForm.value.purchase_details.push({
        product_id: '',
        quantity: '',
        note: '',
    });
};

const removeProductReturnDetail = (index) => {
    if (purchaseReturnForm.value.purchase_details.length > 1) {
        purchaseReturnForm.value.purchase_details.splice(index, 1);
    }
};


const addPurchaseReturnPayment = () => {
    purchaseReturnForm.value.purchase_return_payments.push({
        payment_date: '',
        amount: '',
        due_date: '',
        payment_method: '',
        status: '',
        note: '',
    });
};

const removePurchaseReturnPayment = (index) => {
    if (purchaseReturnForm.value.purchase_return_payments.length > 1) {
        purchaseReturnForm.value.purchase_return_payments.splice(index, 1);
    }
};

const addPurchaseReturnTax = () => {
    purchaseReturnForm.value.taxes.push({
        tax_id: '',
    });
};

const removePurchaseReturnTax = (index) => {
    if (purchaseReturnForm.value.taxes.length > 1) {
        purchaseReturnForm.value.taxes.splice(index, 1);
    }
};

const fetchDataForDropdowns = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const [productResponse, taxResponse] = await Promise.all([
            axios.get('/api/products?per_page=1000000000', {
                headers: { Authorization: `Bearer ${token}` }
            }),
            axios.get('/api/taxes?per_page=1000000000', {
                headers: { Authorization: `Bearer ${token}` }
            })
        ]);

        products.value = productResponse.data.data;
        taxes.value = taxResponse.data.data;

    } catch (error) {
        console.error('Error fetching dropdown data:', error);
    } finally {
        loading.value = false;
    }
};


const fetchPurchaseReturn = async (id) => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const response = await axios.get(`/api/purchase-returns/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const purchaseReturn = response.data.data;
        console.log('Full purchase return response:', purchaseReturn);

        purchaseReturnForm.value = {
            invoice_number: purchaseReturn.invoice_number || '',
            return_date: purchaseReturn.return_date || '',
            status: purchaseReturn.status || '',
            payment_status: purchaseReturn.payment_status || '',
            compensation_method: purchaseReturn.compensation_method || '',
            purchase_id: purchaseReturn.purchase_id || null,
            reason: purchaseReturn.reason || '',
            due_date: purchaseReturn.due_date || null,
            estimated_arrival_date: purchaseReturn.estimated_arrival_date || null,
            actual_arrival_date: purchaseReturn.actual_arrival_date || null,

            total_amount: parseFloat(purchaseReturn.total_amount) || 0,
            total_tax: parseFloat(purchaseReturn.total_tax) || 0,
            total_discount: parseFloat(purchaseReturn.total_discount) || 0,
            shipping_amount: parseFloat(purchaseReturn.shipping_amount) || 0,
            grand_total: parseFloat(purchaseReturn.grand_total) || 0,

            purchase_details: purchaseReturn.purchase_details && purchaseReturn.purchase_details.length > 0
                ? purchaseReturn.purchase_details.map(detail => ({
                    id: detail.id,
                    product_id: Number(detail.product_id) || '',
                    quantity: detail.quantity || '',
                    note: detail.note || '',
                }))
                : [{ product_id: '', quantity: '', note: '' }],

            purchase_return_payments: purchaseReturn.purchase_return_payments && purchaseReturn.purchase_return_payments.length > 0
                ? purchaseReturn.purchase_return_payments.map(payment => ({
                    id: payment.id,
                    payment_date: payment.payment_date || '',
                    amount: payment.amount || '',
                    due_date: payment.due_date || '',
                    payment_method: payment.payment_method || '',
                    status: payment.status || '',
                    note: payment.note || ''
                }))
                : [{ payment_date: '', amount: '', due_date: '', payment_method: '', status: '', note: '' }],


            taxes: purchaseReturn.taxes && purchaseReturn.taxes.length > 0
                ? purchaseReturn.taxes.map(tax => ({
                    id: tax.id,
                    tax_id: tax.tax_id || tax.id,
                }))
                : [{ tax_id: ''}]
        };

    } catch (error) {
        console.error('Error fetching purchase return:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load purchase return data.',
            icon: 'error'
        });
    } finally {
        loading.value = false;
    }
};

const updatePurchaseReturn = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const payload = {
            invoice_number: purchaseReturnForm.value.invoice_number,
            return_date: purchaseReturnForm.value.return_date,
            status: purchaseReturnForm.value.status,
            payment_status: purchaseReturnForm.value.payment_status,
            compensation_method: purchaseReturnForm.value.compensation_method,
            purchase_id: purchaseReturnForm.value.purchase_id,
            reason: purchaseReturnForm.value.reason,
            due_date: purchaseReturnForm.value.due_date,
            estimated_arrival_date: purchaseReturnForm.value.estimated_arrival_date,
            actual_arrival_date: purchaseReturnForm.value.actual_arrival_date,

            purchase_details: purchaseReturnForm.value.purchase_details.map(detail => ({
                ...(detail.id && { id: detail.id }),
                product_id: detail.product_id,
                quantity: detail.quantity,
                note: detail.note,
            })),
            purchase_return_payments: purchaseReturnForm.value.purchase_return_payments.map(payment => ({
                ...(payment.id && { id: payment.id }),
                payment_date: payment.payment_date,
                amount: payment.amount,
                due_date: payment.due_date,
                payment_method: payment.payment_method,
                status: payment.status,
                note: payment.note,
            })),
            taxes: purchaseReturnForm.value.taxes.map(tax => ({
                ...(tax.id && { id: tax.id }),
                tax_id: tax.tax_id,
            })),
        };


        const response = await axios.patch(`/api/purchase-returns/${props.purchaseReturnId}`, payload, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Purchase Return data has been updated',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        });

        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating purchase return:', error);

        let errorMessage = 'Failed to update purchase return. Please check the data or re-login.';
        let useHtml = false;

        if (error.response?.data?.errors) {
            const messages = error.response.data.errors;
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

watch(() => props.show, async (newShow) => {
    if (newShow && props.purchaseReturnId) {
        await fetchDataForDropdowns();
        await fetchPurchaseReturn(props.purchaseReturnId);
    }
}, { immediate: true });

watch(() => props.purchaseReturnId, async (newId) => {
    if (newId && props.show) {
        await fetchPurchaseReturn(newId);
    }
});
</script>
