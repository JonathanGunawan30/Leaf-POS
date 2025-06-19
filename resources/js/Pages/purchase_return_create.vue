<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Purchase Return</span>
                </div>

                <form @submit.prevent="createPurchaseReturn" class="flex self-stretch justify-start items-stretch flex-row gap-2.5">

                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Return Information</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Invoice Number (Original Purchase) <span class="text-red-500">*</span></p>
                                    <input v-model="purchaseReturnForm.invoice_number"
                                           @blur="fetchOriginalPurchaseData"
                                           placeholder="Enter invoice number of original purchase"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           required/>
                                    <p v-if="invoiceNotFound" class="text-red-500 text-xs">Invoice not found or invalid.</p>
                                    <p v-if="!purchaseReturnForm.purchase_id && purchaseReturnForm.invoice_number.trim().length > 0" class="text-gray-500 text-xs">Enter invoice number to link to original purchase.</p>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Return Date <span class="text-red-500">*</span></p>
                                    <input v-model="purchaseReturnForm.return_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           required/>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Status <span class="text-red-500">*</span></p>
                                    <select v-model="purchaseReturnForm.status" required
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Status <span class="text-red-500">*</span></p>
                                    <select v-model="purchaseReturnForm.payment_status" required
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select payment status</option>
                                        <option value="unpaid">Unpaid</option>
                                        <option value="paid">Paid</option>
                                        <option value="partially_paid">Partially Paid</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Compensation Method <span class="text-red-500">*</span></p>
                                    <select v-model="purchaseReturnForm.compensation_method" required
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select method</option>
                                        <option value="refund">Refund</option>
                                        <option value="replacement">Replacement</option>
                                    </select>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Reason <span class="text-red-500">*</span></p>
                                    <input v-model="purchaseReturnForm.reason" placeholder="Enter reason for return"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           required/>
                                </div>
                            </div>
                            <input type="hidden" v-model="purchaseReturnForm.purchase_id" />
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-2.5">
                        <div class="flex w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Optional Details</p>
                            <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">

                                <!-- Total Discount -->
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Total Discount (on Return)</p>
                                    <input v-model="formattedTotalDiscount" type="text"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter total discount for return" />
                                </div>

                                <!-- Shipping Amount -->
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Shipping Amount (from Original Purchase)</p>
                                    <input v-model="formattedShippingAmount" type="text" readonly
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-gray-100 border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none cursor-not-allowed"
                                           placeholder="Auto-filled from original purchase" />
                                </div>

                                <!-- Estimated & Actual Arrival Date -->
                                <!-- Estimated & Actual Arrival Date (1 row) -->
                                <div class="flex self-stretch flex-row gap-4">
                                    <div class="flex flex-1 flex-col gap-[5px]">
                                        <p class="text-[#000000] text-xs leading-6">Estimated Arrival Date</p>
                                        <input v-model="purchaseReturnForm.estimated_arrival_date" type="date"
                                               class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none" />
                                    </div>
                                    <div class="flex flex-1 flex-col gap-[5px]">
                                        <p class="text-[#000000] text-xs leading-6">Actual Arrival Date</p>
                                        <input v-model="purchaseReturnForm.actual_arrival_date" type="date"
                                               class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none" />
                                    </div>
                                </div>
                                <p class="text-[12px] text-gray-500 italic mt-1">
                                    * Only applicable if the item is physically returned or the compensation method is replacement.
                                </p>


                            </div>
                        </div>
                    </div>


                </form>

                <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-[#000000] font-medium leading-6">Returned Products Details</p>
                        <button @click="addProductReturnDetail" type="button"
                                class="flex justify-center items-center gap-2.5 py-[5px] px-[15px] bg-[#2F8451] hover:bg-[#256F43] rounded-[10px] transition-all duration-200">
                            <span class="text-white text-sm font-medium leading-6">+ Add Product</span>
                        </button>
                    </div>

                    <div v-if="purchaseReturnForm.purchase_return_details.length === 0"
                         class="flex justify-center items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px] w-full">
                        <p class="text-gray-500">No returned products added yet. Click "Add Product" to start.</p>
                    </div>

                    <div v-for="(product, index) in purchaseReturnForm.purchase_return_details" :key="index"
                         class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-[#000000] font-medium">Product {{ index + 1 }}</h4>
                            <button @click="removeProductReturnDetail(index)" type="button"
                                    class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Product <span class="text-red-500">*</span></p>
                                <select v-model="product.product_id" required
                                        :disabled="!productsInOriginalPurchase.length"
                                        @change="updateUnitPrice(index)" class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                        :class="{'bg-gray-100 cursor-not-allowed': !productsInOriginalPurchase.length}">
                                    <option value="" disabled selected>Select product</option>
                                    <option v-for="prod_option in productsInOriginalPurchase" :key="prod_option.id" :value="prod_option.id">
                                        {{ prod_option.name }}
                                    </option>
                                </select>
                                <p v-if="!productsInOriginalPurchase.length && purchaseReturnForm.purchase_id" class="text-gray-500 text-xs">No products found for this purchase.</p>
                                <p v-if="!purchaseReturnForm.purchase_id" class="text-gray-500 text-xs">Enter a valid invoice number to select products.</p>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Quantity <span class="text-red-500">*</span></p>
                                <input v-model.number="product.quantity" type="number" min="1" required
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       placeholder="Enter quantity"/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Unit Price</p>
                                <input :value="formatToRupiah(product.unit_price)" type="text" readonly
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-gray-100 border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none cursor-not-allowed"
                                       placeholder="Price"/>
                            </div>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                            <p class="self-stretch text-[#000000] text-xs leading-6">Note</p>
                            <textarea v-model="product.note" placeholder="Enter product note"
                                      class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-[#000000] font-medium leading-6">Return Payment/Refund Information</p>
                        <button @click="addPurchaseReturnPayment" type="button"
                                class="flex justify-center items-center gap-2.5 py-[5px] px-[15px] bg-[#2F8451] hover:bg-[#256F43] rounded-[10px] transition-all duration-200">
                            <span class="text-white text-sm font-medium leading-6">+ Add Payment</span>
                        </button>
                    </div>

                    <div v-if="purchaseReturnForm.purchase_return_payments.length === 0"
                         class="flex justify-center items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px] w-full">
                        <p class="text-gray-500">No payments added yet. Click "Add Payment" to start.</p>
                    </div>

                    <div v-for="(payment, index) in purchaseReturnForm.purchase_return_payments" :key="index"
                         class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-[#000000] font-medium">Payment {{ index + 1 }}</h4>
                            <button @click="removePurchaseReturnPayment(index)" type="button"
                                    class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Payment Date <span class="text-red-500">*</span></p>
                                <input v-model="payment.payment_date" type="date"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none" required/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Amount <span class="text-red-500">*</span></p>
                                <input v-model="formattedPaymentAmount" type="text"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       placeholder="Enter payment amount" required/>
                            </div>
                        </div>
                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Payment Due Date</p>
                                <input v-model="payment.due_date" type="date"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Payment Method <span class="text-red-500">*</span></p>
                                <select v-model="payment.payment_method" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select payment method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="giro">Giro</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="e_wallet">E-Wallet</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Payment Status <span class="text-red-500">*</span></p>
                                <select v-model="payment.status" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select payment status</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                            </div>
                        </div>
                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                            <p class="self-stretch text-[#000000] text-xs leading-6">Payment Note</p>
                            <textarea v-model="payment.note" placeholder="Enter payment note"
                                      class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-[#000000] font-medium leading-6">Tax Information</p>
                        <button @click="addTax" type="button"
                                class="flex justify-center items-center gap-2.5 py-[5px] px-[15px] bg-[#2F8451] hover:bg-[#256F43] rounded-[10px] transition-all duration-200">
                            <span class="text-white text-sm font-medium leading-6">+ Add Tax</span>
                        </button>
                    </div>

                    <div v-if="purchaseReturnForm.purchase_return_taxes.length === 0" class="flex justify-center w-full items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px]">
                        <p class="text-gray-500">No taxes added yet. Click "Add Tax" to start.</p>
                    </div>

                    <div v-for="(tax, index) in purchaseReturnForm.purchase_return_taxes" :key="index"
                         class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-[#000000] font-medium">Tax {{ index + 1 }}</h4>
                            <button @click="removeTax(index)" type="button"
                                    class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                            <p class="self-stretch text-[#000000] text-xs leading-6">Tax Type <span class="text-red-500">*</span></p>
                            <select v-model="tax.tax_id" required
                                    class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                <option value="" disabled selected>Select tax</option>
                                <option v-for="taxOption in taxes" :key="taxOption.id" :value="taxOption.id">
                                    {{ taxOption.name }} ({{ taxOption.rate }}%)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-center pt-4">
                    <button @click="createPurchaseReturn"
                            class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105">
                        <span class="text-white text-sm font-medium leading-6">Create Purchase Return</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref, computed, watch} from 'vue' // Added watch
import axios from 'axios'
import Swal from "sweetalert2";

const token = localStorage.getItem('X-API-TOKEN')

const api = axios.create({
    baseURL: '/api',
    timeout: 8000,
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

const purchaseReturnForm = ref({
    invoice_number: '',
    return_date: '',
    reason: '',
    status: '',
    payment_status: '',
    compensation_method: '',
    purchase_id: null, // This will be set automatically based on invoice_number

    total_discount: 0,
    shipping_amount: 0, // This will be auto-filled from the original purchase

    purchase_return_details: [],
    purchase_return_payments: [], // <-- Changed to empty array initially
    purchase_return_taxes: []
})


const formatToRupiah = (rawValue) => {
    if (rawValue === '' || rawValue === null || rawValue === undefined) {
        return '';
    }
    const number = Number(rawValue);
    if (isNaN(number)) {
        return '';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};

const parseRupiah = (formattedString) => {
    if (typeof formattedString !== 'string' || !formattedString) return 0;
    const number = Number(String(formattedString).replace(/[^0-9]/g, ''));
    return isNaN(number) ? 0 : number;
};

const formattedTotalDiscount = computed({
    get: () => formatToRupiah(purchaseReturnForm.value.total_discount),
    set: (val) => {
        purchaseReturnForm.value.total_discount = parseRupiah(val);
    }
});

const formattedShippingAmount = computed({
    get: () => formatToRupiah(purchaseReturnForm.value.shipping_amount),
    set: (val) => {
        // This is read-only, so the setter might not be strictly necessary for user input,
        // but it's good practice for consistency.
        purchaseReturnForm.value.shipping_amount = parseRupiah(val);
    }
});

const formattedPaymentAmount = computed({
    get: () => {
        // Access the first payment if it exists
        if (purchaseReturnForm.value.purchase_return_payments && purchaseReturnForm.value.purchase_return_payments[0]) {
            return formatToRupiah(purchaseReturnForm.value.purchase_return_payments[0].amount);
        }
        return formatToRupiah('');
    },
    set: (val) => {
        // Ensure the first payment object exists when setting amount
        if (!purchaseReturnForm.value.purchase_return_payments || purchaseReturnForm.value.purchase_return_payments.length === 0) {
            addPurchaseReturnPayment(); // Add a payment if none exists
        }
        purchaseReturnForm.value.purchase_return_payments[0].amount = parseRupiah(val);
    }
});

const products = ref([])
const productsInOriginalPurchase = ref([]); // New ref to hold products from original purchase
const taxes = ref([])
const loading = ref(false)
const invoiceNotFound = ref(false); // New reactive state for invoice search


// --- Functions for Purchase Return Details ---
const addProductReturnDetail = () => {
    purchaseReturnForm.value.purchase_return_details.push({
        product_id: '',
        quantity: 1,
        unit_price: 0,
        note: '',
    })
}

const removeProductReturnDetail = (index) => {
    purchaseReturnForm.value.purchase_return_details.splice(index, 1);
}

const updateUnitPrice = (index) => {
    // Now look up product in productsInOriginalPurchase
    const selectedProductId = purchaseReturnForm.value.purchase_return_details[index].product_id;
    const selectedProductData = productsInOriginalPurchase.value.find(p => p.product_id === selectedProductId); // assuming product_id is from details

    if (selectedProductData) {
        // Ensure we're using the unit_price from the purchase detail, not product master
        purchaseReturnForm.value.purchase_return_details[index].unit_price = Number(selectedProductData.unit_price) || 0;
    } else {
        purchaseReturnForm.value.purchase_return_details[index].unit_price = 0;
    }
}


// --- Functions for Purchase Return Payments ---
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
    } else if (index === 0 && purchaseReturnForm.value.purchase_return_payments.length === 1) {
        // Clear the only payment instead of removing if it's the last one
        purchaseReturnForm.value.purchase_return_payments[0] = {
            payment_date: '', amount: '', due_date: '', payment_method: '', status: '', note: ''
        };
    }
};


// --- Functions for Purchase Return Taxes ---
const addTax = () => {
    purchaseReturnForm.value.purchase_return_taxes.push({
        tax_id: '',
    });
};

const removeTax = (index) => {
    purchaseReturnForm.value.purchase_return_taxes.splice(index, 1);
};


// --- Fetching Data for Dropdowns and Original Purchase ---
const fetchData = async () => {
    try {
        loading.value = true
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) {
            return;
        }

        const headers = { 'Authorization': `Bearer ${apiToken}` };

        // Only fetch master data (all products and taxes) if needed for general reference,
        // but for product selection, we will rely on productsInOriginalPurchase.
        const [productResponse, taxResponse] = await Promise.all([
            axios.get('/api/products?per_page=100000000', { headers }),
            axios.get('/api/taxes?per_page=100000000', { headers })
        ]);

        products.value = productResponse.data.data.map(p => ({...p, purchase_price: Number(p.purchase_price) || 0 }));
        taxes.value = taxResponse.data.data;
    } catch (error) {
        console.log('Error fetching data: ', error);
    } finally {
        loading.value = false;
    }
}

// Function to fetch original purchase data by invoice number
const fetchOriginalPurchaseData = async () => {
    invoiceNotFound.value = false;
    purchaseReturnForm.value.purchase_id = null; // Reset purchase_id
    purchaseReturnForm.value.shipping_amount = 0; // Reset shipping_amount
    productsInOriginalPurchase.value = []; // Clear products from previous purchase
    purchaseReturnForm.value.purchase_return_details = []; // Clear current return details

    const invoiceNumber = purchaseReturnForm.value.invoice_number.trim();
    if (!invoiceNumber) {
        return; // Don't search if invoice number is empty
    }

    try {
        // Fetch original purchase details with product information
        const response = await api.get(`/purchases?search=${invoiceNumber}&per_page=1&exact_invoice=true&with_details=true`); // Ensure backend returns purchase_details
        if (response.data.data.length > 0) {
            const originalPurchase = response.data.data[0];
            purchaseReturnForm.value.purchase_id = originalPurchase.id;
            purchaseReturnForm.value.shipping_amount = Number(originalPurchase.shipping_amount) || 0;

            // Populate productsInOriginalPurchase with products from this specific purchase
            productsInOriginalPurchase.value = originalPurchase.purchase_details.map(detail => ({
                id: detail.product_id, // This is the actual product ID
                name: detail.product_name, // Product name from detail
                unit_price: Number(detail.unit_price) || 0 // Unit price from purchase detail
            }));

            // Optionally, pre-fill purchase_return_details with some items from the original purchase
            // e.g., if you want to return all items by default, or provide suggestions
            // purchaseReturnForm.value.purchase_return_details = productsInOriginalPurchase.value.map(p => ({
            //     product_id: p.id,
            //     quantity: 1, // Default to 1, user can change
            //     unit_price: p.unit_price,
            //     note: ''
            // }));


        } else {
            invoiceNotFound.value = true;
            Swal.fire({
                title: 'Not Found',
                text: `Original Purchase with Invoice Number "${invoiceNumber}" not found. Please check the invoice number.`,
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error fetching original purchase:', error);
        invoiceNotFound.value = true;
        Swal.fire({
            title: 'Error',
            text: 'Failed to fetch original purchase data. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Watch for changes in invoice_number and trigger fetch
watch(() => purchaseReturnForm.value.invoice_number, (newInvoiceNumber) => {
    // Only trigger if invoice number is not empty and has a reasonable length (e.g., > 3 chars)
    // or if it becomes empty (to clear previous data)
    if (newInvoiceNumber.trim().length > 3 || newInvoiceNumber.trim().length === 0) {
        fetchOriginalPurchaseData();
    } else {
        // Don't reset if typing
        invoiceNotFound.value = false;
        purchaseReturnForm.value.purchase_id = null;
        purchaseReturnForm.value.shipping_amount = 0;
        productsInOriginalPurchase.value = [];
        purchaseReturnForm.value.purchase_return_details = [];
    }
}, { deep: true });


// === Create Purchase Return ===
const createPurchaseReturn = async () => {
    try {
        loading.value = true
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) return;

        // Frontend validation: Check if purchase_id is set
        if (!purchaseReturnForm.value.purchase_id) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please enter a valid Invoice Number to link to an original Purchase.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            loading.value = false;
            return;
        }

        const payload = {
            invoice_number: purchaseReturnForm.value.invoice_number,
            return_date: purchaseReturnForm.value.return_date,
            reason: purchaseReturnForm.value.reason,
            status: purchaseReturnForm.value.status,
            payment_status: purchaseReturnForm.value.payment_status,
            compensation_method: purchaseReturnForm.value.compensation_method,
            purchase_id: purchaseReturnForm.value.purchase_id, // Ensure purchase_id is sent
            total_discount: purchaseReturnForm.value.total_discount,
            shipping_amount: purchaseReturnForm.value.shipping_amount,

            purchase_return_details: purchaseReturnForm.value.purchase_return_details.map(detail => ({
                product_id: detail.product_id,
                quantity: detail.quantity,
                unit_price: detail.unit_price, // Ensure unit_price is sent
                note: detail.note,
            })),
            purchase_return_payments: purchaseReturnForm.value.purchase_return_payments.map(payment => ({
                payment_date: payment.payment_date,
                amount: payment.amount,
                due_date: payment.due_date,
                payment_method: payment.payment_method,
                status: payment.status,
                note: payment.note,
            })),
            purchase_return_taxes: purchaseReturnForm.value.purchase_return_taxes.map(tax => ({
                tax_id: tax.tax_id,
            })),
        };


        console.log('--- Payload Content ---');
        console.log(JSON.stringify(payload, null, 2));
        console.log('--- End Payload Content ---');


        const response = await api.post('/purchase-returns', payload);

        Swal.fire({
            title: 'Success',
            text: 'Purchase Return data has been created successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        // Reset form after successful creation
        purchaseReturnForm.value = {
            invoice_number: '',
            return_date: '',
            reason: '',
            status: '',
            payment_status: '',
            compensation_method: '',
            purchase_id: null,
            total_discount: 0,
            shipping_amount: 0,
            purchase_return_details: [],
            purchase_return_payments: [], // Reset to empty array
            purchase_return_taxes: []
        }
        invoiceNotFound.value = false;
        productsInOriginalPurchase.value = []; // Clear products list
        // No need to reset general 'products' as it's not used for selection anymore

    } catch (error) {
        console.error('Failed to create purchase return:', error.response?.data || error.message)

        let errorMessage = 'Failed to add purchase return. Please check the data or re-login.';
        let useHtml = false;

        if (error.response?.data?.errors) {
            const messages = error.response.data.errors;
            const formattedErrors = Object.entries(messages)
                .map(([field, msgs]) => {
                    let messageText = '';

                    if (Array.isArray(msgs)) {
                        messageText = msgs.join(', ');
                    } else if (typeof msgs === 'string') {
                        messageText = msgs;
                    } else if (typeof msgs === 'object' && msgs !== null) {
                        messageText = JSON.stringify(msgs);
                    } else {
                        messageText = String(msgs);
                    }

                    return `<strong>${field}</strong>: ${messageText}`;
                })
                .filter(Boolean)
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
            confirmButtonText: 'OK'
        })
    } finally {
        loading.value = false;
    }
}

onMounted(async () => {
    await fetchData();
})
</script>
