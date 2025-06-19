<template>
    <div v-if="show" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex justify-end overflow-hidden">
        <div class="bg-white w-full max-w-3xl h-full shadow-lg relative overflow-y-auto">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-300">
                <h2 class="text-lg font-bold text-black">EDIT SALE</h2>
                <button @click="closePopup" class="text-black hover:text-gray-600">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0006 8.82208L14.1254 4.69727L15.3039 5.87577L11.1791 10.0006L15.3039 14.1253L14.1254 15.3038L10.0006 11.1791L5.87578 15.3038L4.69727 14.1253L8.82207 10.0006L4.69727 5.87577L5.87578 4.69727L10.0006 8.82208Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="updateSale" class="px-5 py-4">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-black mb-4">Sale Details</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Main Invoice Number</label>
                            <input
                                v-model="saleForm.invoice_number"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">DP Invoice Number</label>
                            <input
                                v-model="saleForm.invoice_downpayment_number"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Sale Date</label>
                            <input
                                v-model="saleForm.sale_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Due Date</label>
                            <input
                                v-model="saleForm.due_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Invoice Issue Date</label>
                            <input
                                v-model="saleForm.invoice_issue_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">DP Invoice Issue Date</label>
                            <input
                                v-model="saleForm.invoice_downpayment_issue_date"
                                type="date"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Delivery Number</label>
                            <input
                                v-model="saleForm.delivery_number"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Customer</label>
                            <div class="relative">
                                <select
                                    v-model="saleForm.customer_id"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Customer</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Total Discount</label>
                            <input
                                :value="formatNumber(saleForm.total_discount)"
                                @input="saleForm.total_discount = parseFormattedNumber($event.target.value)"
                                type="text"
                                inputmode="numeric"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Total Tax</label>
                            <input
                                :value="formatNumber(saleForm.total_tax)"
                                @input="saleForm.total_tax = parseFormattedNumber($event.target.value)"
                                type="text"
                                inputmode="numeric"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Total Amount</label>
                            <input
                                :value="formatNumber(saleForm.total_amount)"
                                @input="saleForm.total_amount = parseFormattedNumber($event.target.value)"
                                type="text"
                                inputmode="numeric"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Grand Total</label>
                            <input
                                :value="formatNumber(saleForm.grand_total)"
                                @input="saleForm.grand_total = parseFormattedNumber($event.target.value)"
                                type="text"
                                inputmode="numeric"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />

                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Status</label>
                            <div class="relative">
                                <select
                                    v-model="saleForm.status"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Status</option>

                                    <option v-if="originalStatus === 'indent'" value="indent">Indent</option>

                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Payment Status</label>
                            <div class="relative">
                                <select
                                    v-model="saleForm.payment_status"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                >
                                    <option disabled value="">Select Payment Status</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                    <option value="partially_paid">Partially Paid</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-black mb-1">Note</label>
                        <textarea
                            v-model="saleForm.note"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                            placeholder='Enter notes...'
                        ></textarea>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Products</h3>
                        <button
                            type="button"
                            @click="addProduct"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Product
                        </button>
                    </div>

                    <div v-for="(detail, index) in saleForm.sale_details" :key="`product-${index}`"
                         class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Product {{ index + 1 }}</span>
                            <button
                                v-if="saleForm.sale_details.length > 1"
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
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-black mb-1">Unit Price</label>
                            <input
                                :value="formatNumber(detail.unit_price)"
                                @input="detail.unit_price = parseFormattedNumber($event.target.value)"
                                type="text"
                                inputmode="numeric"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
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

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Payments</h3>
                        <button
                            type="button"
                            @click="addPayment"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Payment
                        </button>
                    </div>

                    <div v-for="(payment, index) in saleForm.sale_payments" :key="`payment-${index}`"
                         class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Payment {{ index + 1 }}</span>
                            <button
                                v-if="saleForm.sale_payments.length > 1"
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
                                :value="formatNumber(payment.amount)"
                                @input="payment.amount = parseFormattedNumber($event.target.value)"
                                type="text"
                                inputmode="numeric"
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
                                        <option value="unpaid">Unpaid</option>
                                        <option value="paid">Paid</option>
                                        <option value="partially_paid">Partially Paid</option>
                                        <option value="failed">Failed</option>
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

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-black">Shipments</h3>
                        <button
                            type="button"
                            @click="addShipment"
                            class="px-3 py-1 bg-lp-green text-white text-sm rounded-lg hover:bg-green-800"
                        >
                            Add Shipment
                        </button>
                    </div>

                    <div v-for="(shipment, index) in saleForm.shipments" :key="`shipment-${index}`"
                         class="mb-4 p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-gray-600">Shipment {{ index + 1 }}</span>
                            <button
                                v-if="saleForm.shipments.length > 1"
                                type="button"
                                @click="removeShipment(index)"
                                class="text-red-500 hover:text-red-700 text-sm"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Courier</label>
                                <div class="relative">
                                    <select
                                        v-model="shipment.courier_id"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Courier</option>
                                        <option v-for="courier in couriers" :key="courier.id" :value="courier.id">
                                            {{ courier.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Vehicle Type</label>
                                <div class="relative">
                                    <select
                                        v-model="shipment.vehicle_type"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Vehicle Type</option>
                                        <option value="motorcycle">Motorcycle</option>
                                        <option value="car_sedan">Car Sedan</option>
                                        <option value="car_van">Car Van</option>
                                        <option value="car_pickup">Car Pickup</option>
                                        <option value="truck_small">Small Truck</option>
                                        <option value="truck_medium">Medium Truck</option>
                                        <option value="truck_large">Large Truck</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-black mb-1">Vehicle Number</label>
                            <input
                                v-model="shipment.vehicle_number"
                                type="text"
                                class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                            />
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Shipping Date</label>
                                <input
                                    v-model="shipment.shipping_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Estimated Arrival</label>
                                <input
                                    v-model="shipment.estimated_arrival_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Actual Arrival</label>
                                <input
                                    v-model="shipment.actual_arrival_date"
                                    type="date"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-xs text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Shipping Cost</label>
                                <input
                                    :value="formatNumber(shipment.shipping_cost)"
                                    @input="shipment.shipping_cost = parseFormattedNumber($event.target.value)"
                                    type="text"
                                    inputmode="numeric"
                                    class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-black mb-1">Shipment Status</label>
                                <div class="relative">
                                    <select
                                        v-model="shipment.status"
                                        class="w-full h-11 px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 appearance-none"
                                    >
                                        <option disabled value="">Select Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="processing">Processing</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="on_hold">On Hold</option>
                                        <option value="delivered_partially">Delivered Partially</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Note</label>
                            <textarea
                                v-model="shipment.note"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-400 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-green-600 resize-none"
                                placeholder='Enter notes...'
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
    saleId: Number
});

const emit = defineEmits(['close', 'updated']);

const saleForm = ref({
    invoice_number: '',
    invoice_downpayment_number: '',
    invoice_issue_date: '',
    invoice_downpayment_issue_date: '',
    delivery_number: '',
    sale_date: '',
    total_amount: 0,
    total_tax: 0,
    total_discount: 0,
    grand_total: 0,
    status: '', // Sale status
    payment_status: '', // Sale payment status
    due_date: '', // Sale due date
    note: '', // Sale general note
    customer_id: '',
    sale_details: [
        {
            product_id: '',
            quantity: '',
            unit_price: '',
            note: '',
        }
    ],
    sale_payments: [
        {
            payment_date: '',
            amount: '',
            due_date: '', // Payment due date
            payment_method: '',
            status: '', // Payment status
            note: '',
        }
    ],
    shipments: [
        {
            courier_id: '',
            vehicle_type: '',
            vehicle_number: '',
            shipping_date: '',
            estimated_arrival_date: '',
            actual_arrival_date: '',
            status: '', // Shipment status
            shipping_cost: '',
            note: '',
        }
    ]
});
const originalStatus = ref('');
const customers = ref([]);
const products = ref([]);
const couriers = ref([]);
const loading = ref(false);
const formatNumber = (value) => {
    if (!value) return '';
    return new Intl.NumberFormat('id-ID').format(Number(value));
};

const isStatusReadonly = computed(() => {
    return saleForm.value.status !== '';
});

const parseFormattedNumber = (value) => {
    return value.toString().replace(/\D/g, '');
};


const addProduct = () => {
    saleForm.value.sale_details.push({
        product_id: '',
        quantity: '',
        unit_price: '',
        note: '',
    });
};

const removeProduct = (index) => {
    if (saleForm.value.sale_details.length > 1) {
        saleForm.value.sale_details.splice(index, 1);
    }
};

const addPayment = () => {
    saleForm.value.sale_payments.push({
        payment_date: '',
        amount: '',
        due_date: '',
        payment_method: '',
        status: '',
        note: '',
    });
};

const removePayment = (index) => {
    if (saleForm.value.sale_payments.length > 1) {
        saleForm.value.sale_payments.splice(index, 1);
    }
};

const addShipment = () => {
    saleForm.value.shipments.push({
        courier_id: '',
        vehicle_type: '',
        vehicle_number: '',
        shipping_date: '',
        estimated_arrival_date: '',
        actual_arrival_date: '',
        status: '',
        shipping_cost: '',
        note: '',
    });
};

const removeShipment = (index) => {
    if (saleForm.value.shipments.length > 1) {
        saleForm.value.shipments.splice(index, 1);
    }
};

const fetchData = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const [customerResponse, productResponse, courierResponse] = await Promise.all([
            axios.get('/api/customers?per_page=1000000000', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }),
            axios.get('/api/products?per_page=1000000000', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }),
            axios.get('/api/couriers?per_page=1000000000&status=available', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
        ]);

        customers.value = customerResponse.data.data;
        products.value = productResponse.data.data;
        couriers.value = courierResponse.data.data;

    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const fetchSale = async (id) => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const response = await axios.get(`/api/sales/${id}`, { // Assuming /api/sales endpoint
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        const sale = response.data.data;
        originalStatus.value = sale.status || '';
        console.log('Full sale response:', sale);

        // Map the fetched data to the saleForm
        saleForm.value = {
            invoice_number: sale.invoice_number || '',
            invoice_downpayment_number: sale.invoice_downpayment_number || '',
            invoice_issue_date: sale.invoice_issue_date || '',
            invoice_downpayment_issue_date: sale.invoice_downpayment_issue_date || '',
            delivery_number: sale.delivery_number || '',
            sale_date: sale.sale_date || '',
            total_amount: sale.total_amount || 0,
            total_tax: sale.total_tax || 0,
            total_discount: sale.total_discount || 0,
            grand_total: sale.grand_total || 0,
            status: sale.status || '',
            payment_status: sale.payment_status || '',
            due_date: sale.due_date || '',
            note: sale.note || '',
            customer_id: sale.customer?.id || '',
            sale_details: sale.sale_details && sale.sale_details.length > 0
                ? sale.sale_details.map(detail => ({
                    id: detail.id, // Include ID for existing details if your backend needs it for update
                    product_id: Number(detail.product_id) || '',
                    quantity: detail.quantity || '',
                    unit_price: detail.unit_price || '',
                    note: detail.note || '',
                }))
                : [{ // Default if no details
                    product_id: '',
                    quantity: '',
                    unit_price: '',
                    note: '',
                }],
            sale_payments: sale.sale_payments && sale.sale_payments.length > 0
                ? sale.sale_payments.map(payment => ({
                    id: payment.id, // Include ID for existing payments
                    payment_date: payment.payment_date || '',
                    amount: payment.amount || '',
                    due_date: payment.due_date || '',
                    payment_method: payment.payment_method || '',
                    status: payment.status || '',
                    note: payment.note || ''
                }))
                : [{ // Default if no payments
                    payment_date: '',
                    amount: '',
                    due_date: '',
                    payment_method: '',
                    status: '',
                    note: '',
                }],
            shipments: sale.shipments && sale.shipments.length > 0
                ? sale.shipments.map(shipment => ({
                    id: shipment.id, // Include ID for existing shipments
                    courier_id: Number(shipment.courier_id) || '',
                    vehicle_type: shipment.vehicle_type || '',
                    vehicle_number: shipment.vehicle_number || '',
                    shipping_date: shipment.shipping_date || '',
                    estimated_arrival_date: shipment.estimated_arrival_date || '',
                    actual_arrival_date: shipment.actual_arrival_date || '',
                    status: shipment.status || '',
                    shipping_cost: shipment.shipping_cost || '',
                    note: shipment.note || '',
                }))
                : [{ // Default if no shipments
                    courier_id: '',
                    vehicle_type: '',
                    vehicle_number: '',
                    shipping_date: '',
                    estimated_arrival_date: '',
                    actual_arrival_date: '',
                    status: '',
                    shipping_cost: '',
                    note: '',
                }]
        };

    } catch (error) {
        console.error('Error fetching sale:', error);
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, async (newShow) => {
    if (newShow && props.saleId) {
        await fetchData();
        await fetchSale(props.saleId);
    }
}, {immediate: true});

watch(() => props.saleId, async (newId) => {
    if (newId && props.show) {
        await fetchSale(newId);
    }
});

const updateSale = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if (!token) return;

        const formData = new FormData();
        formData.append('invoice_number', saleForm.value.invoice_number || '');
        formData.append('invoice_downpayment_number', saleForm.value.invoice_downpayment_number || '');
        formData.append('invoice_issue_date', saleForm.value.invoice_issue_date || '');
        formData.append('invoice_downpayment_issue_date', saleForm.value.invoice_downpayment_issue_date || '');
        formData.append('delivery_number', saleForm.value.delivery_number || '');
        formData.append('sale_date', saleForm.value.sale_date || '');
        formData.append('total_amount', saleForm.value.total_amount || 0);
        formData.append('total_tax', saleForm.value.total_tax || 0);
        formData.append('total_discount', saleForm.value.total_discount || 0);
        formData.append('grand_total', saleForm.value.grand_total || 0);
        formData.append('status', saleForm.value.status || '');
        formData.append('payment_status', saleForm.value.payment_status || '');
        formData.append('due_date', saleForm.value.due_date || '');
        formData.append('note', saleForm.value.note || '');
        formData.append('customer_id', saleForm.value.customer_id || '');


        // Append sale_details
        saleForm.value.sale_details.forEach((detail, index) => {
            Object.keys(detail).forEach(key => {
                if (detail[key] !== null && detail[key] !== undefined) {
                    formData.append(`sale_details[${index}][${key}]`, detail[key]);
                } else {
                    formData.append(`sale_details[${index}][${key}]`, '');
                }
            });
        });

        // Append sale_payments
        saleForm.value.sale_payments.forEach((payment, index) => {
            Object.keys(payment).forEach(key => {
                if (payment[key] !== null && payment[key] !== undefined) {
                    formData.append(`sale_payments[${index}][${key}]`, payment[key]);
                } else {
                    formData.append(`sale_payments[${index}][${key}]`, '');
                }
            });
        });

        // Append shipments
        saleForm.value.shipments.forEach((shipment, index) => {
            Object.keys(shipment).forEach(key => {
                if (shipment[key] !== null && shipment[key] !== undefined) {
                    formData.append(`shipments[${index}][${key}]`, shipment[key]);
                } else {
                    formData.append(`shipments[${index}][${key}]`, '');
                }
            });
        });


        console.log('--- FormData Content ---');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        console.log('--- End FormData Content ---');

        const response = await axios.post(`/api/sales/${props.saleId}`, formData, { // Assuming /api/sales endpoint
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'X-HTTP-Method-Override': 'PATCH' // For Laravel PUT/PATCH with FormData
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Sale data has been updated',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        });

        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating sale:', error);

        let errorMessage = 'Failed to update sale. Please check the data or re-login.';
        let useHtml = false;

        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;

            if (typeof errors === 'string') {
                errorMessage = errors;
            } else if (typeof errors === 'object' && errors !== null) {
                const formattedErrors = Object.entries(errors)
                    .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                    .join('<br>');

                if (formattedErrors) {
                    errorMessage = formattedErrors;
                    useHtml = true;
                }
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
    if (props.show && props.saleId) {
        await fetchData();
        await fetchSale(props.saleId);
    }
});
</script>
