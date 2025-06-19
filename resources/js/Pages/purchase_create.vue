<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Purchase</span>
                </div>

                <div class="flex self-stretch justify-start items-stretch flex-row gap-2.5">

                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Purchase Information</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Invoice Number <span class="text-red-500">*</span></p>
                                    <input v-model="purchaseForm.invoice_number" placeholder="Enter invoice number"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           required/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Purchase Date <span class="text-red-500">*</span></p>
                                    <input v-model="purchaseForm.purchase_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           required/>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Total Discount <span class="text-red-500">*</span></p>
                                    <input v-model="formattedTotalDiscount" type="text"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter total discount" required/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Shipping Amount <span class="text-red-500">*</span></p>
                                    <input v-model="formattedShippingAmount" type="text"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter shipping amount" required/>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Status <span class="text-red-500">*</span></p>
                                <select v-model="purchaseForm.status" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-2.5">
                        <div class="flex w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Supplier</p>
                            <div class="flex self-stretch w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                                <div class="flex self-stretch w-full justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Select Supplier <span class="text-red-500">*</span></p>
                                    <select
                                        v-model="purchaseForm.supplier_id"
                                        required
                                        class="flex w-full justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                    >
                                        <option value="" disabled selected>Select supplier</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Important Dates</p>
                            <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                                <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Due Date</p>
                                        <input v-model="purchaseForm.due_date" type="date"
                                               class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Estimated Arrival Date <span class="text-red-500">*</span></p>
                                        <input v-model="purchaseForm.estimated_arrival_date" type="date"
                                               class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none" required/>
                                    </div>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Actual Arrival Date</p>
                                    <input v-model="purchaseForm.actual_arrival_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-[#000000] font-medium leading-6">Products</p>
                        <button @click="addProduct" type="button"
                                class="flex justify-center items-center gap-2.5 py-[5px] px-[15px] bg-[#2F8451] hover:bg-[#256F43] rounded-[10px] transition-all duration-200">
                            <span class="text-white text-sm font-medium leading-6">+ Add Product</span>
                        </button>
                    </div>

                    <div v-if="purchaseForm.purchase_details.length === 0"
                         class="flex justify-center items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px] w-full">
                        <p class="text-gray-500">No products added yet. Click "Add Product" to start.</p>
                    </div>

                    <div v-for="(product, index) in purchaseForm.purchase_details" :key="index"
                         class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-[#000000] font-medium">Product {{ index + 1 }}</h4>
                            <button @click="removeProduct(index)" type="button"
                                    class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Product <span class="text-red-500">*</span></p>
                                <select v-model="product.product_id" required
                                        @change="updatePurchasePrice(index)" class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select product</option>
                                    <option v-for="prod_option in products" :key="prod_option.id" :value="prod_option.id">
                                        {{ prod_option.name }} </option>
                                </select>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Quantity <span class="text-red-500">*</span></p>
                                <input v-model.number="product.quantity" type="number" min="1" required
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       placeholder="Enter quantity"/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Purchase Price (Each)</p>
                                <input :value="formatToRupiah(product.purchase_price)" type="text" readonly
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

                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Payment Information</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Date <span class="text-red-500">*</span></p>
                                    <input v-model="purchaseForm.purchase_payments[0].payment_date" type="date"
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
                                    <input v-model="purchaseForm.purchase_payments[0].due_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Method <span class="text-red-500">*</span></p>
                                    <select v-model="purchaseForm.purchase_payments[0].payment_method" required
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
                                    <select v-model="purchaseForm.purchase_payments[0].status" required
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
                                <textarea v-model="purchaseForm.purchase_payments[0].note" placeholder="Enter payment note"
                                          class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                            </div>
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

                    <div v-if="purchaseForm.taxes.length === 0" class="flex justify-center w-full items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px]">
                        <p class="text-gray-500">No taxes added yet. Click "Add Tax" to start.</p>
                    </div>

                    <div v-for="(tax, index) in purchaseForm.taxes" :key="index"
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
                    <button @click="createPurchase"
                            class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105">
                        <span class="text-white text-sm font-medium leading-6">Create Purchase</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref, computed} from 'vue' // Added computed
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

const purchaseForm = ref({
    invoice_number: '',
    purchase_date: '',
    total_discount: '',
    shipping_amount: '',
    status: '',
    due_date: '',
    estimated_arrival_date: '',
    actual_arrival_date: '',
    supplier_id: '',
    purchase_details: [],
    purchase_payments: [
        {
            payment_date: '',
            amount: '',
            due_date: '',
            payment_method: '',
            status: '',
            note: ''
        }
    ],
    taxes: []
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
    get: () => formatToRupiah(purchaseForm.value.total_discount),
    set: (val) => {
        purchaseForm.value.total_discount = parseRupiah(val);
    }
});

const formattedShippingAmount = computed({
    get: () => formatToRupiah(purchaseForm.value.shipping_amount),
    set: (val) => {
        purchaseForm.value.shipping_amount = parseRupiah(val);
    }
});

const formattedPaymentAmount = computed({
    get: () => {
        if (purchaseForm.value.purchase_payments && purchaseForm.value.purchase_payments[0]) {
            return formatToRupiah(purchaseForm.value.purchase_payments[0].amount);
        }
        return formatToRupiah('');
    },
    set: (val) => {
        if (!purchaseForm.value.purchase_payments) {
            purchaseForm.value.purchase_payments = [];
        }
        if (!purchaseForm.value.purchase_payments[0]) {
            purchaseForm.value.purchase_payments[0] = {
                payment_date: '', amount: 0, due_date: '', payment_method: '', status: '', note: ''
            };
        }
        purchaseForm.value.purchase_payments[0].amount = parseRupiah(val);
    }
});


const suppliers = ref([])
const products = ref([])
const taxes = ref([])
const loading = ref(false)

const addProduct = () => {
    purchaseForm.value.purchase_details.push({
        product_id: '',
        quantity: 1,
        note: '',
        purchase_price: 0
    })
}

const removeProduct = (index) => {
    purchaseForm.value.purchase_details.splice(index, 1);
}

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
    } else if (index === 0 && purchaseForm.value.purchase_payments.length === 1) {

    }
};


const addTax = () => {
    purchaseForm.value.taxes.push({
        tax_id: '',
    });
};

const removeTax = (index) => {
    purchaseForm.value.taxes.splice(index, 1);
};

const updatePurchasePrice = (index) => {
    const selectedProductId = purchaseForm.value.purchase_details[index].product_id;
    const selectedProductData = products.value.find(p => p.id === selectedProductId);

    if (selectedProductData) {
        purchaseForm.value.purchase_details[index].purchase_price = Number(selectedProductData.purchase_price) || 0;
    } else {
        purchaseForm.value.purchase_details[index].purchase_price = 0;
    }
}

const fetchData = async () => {
    try {
        loading.value = true
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) {
            return;
        }

        const headers = { 'Authorization': `Bearer ${apiToken}` };

        const [supplierResponse, productResponse, taxResponse] = await Promise.all([
            axios.get('/api/suppliers?per_page=100000000', { headers }),
            axios.get('/api/products?per_page=100000000', { headers }),
            axios.get('/api/taxes?per_page=100000000', { headers })
        ]);

        suppliers.value = supplierResponse.data.data;
        products.value = productResponse.data.data.map(p => ({...p, purchase_price: Number(p.purchase_price) || 0 })); // Ensure numeric
        taxes.value = taxResponse.data.data;
    } catch (error) {
        console.log('Error fetching data: ', error);
    } finally {
        loading.value = false;
    }
}

const createPurchase = async () => {
    try {
        loading.value = true
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) return;

        const formData = new FormData();

        formData.append('invoice_number', purchaseForm.value.invoice_number);
        formData.append('purchase_date', purchaseForm.value.purchase_date);
        formData.append('total_discount', purchaseForm.value.total_discount || 0);
        formData.append('shipping_amount', purchaseForm.value.shipping_amount || 0);
        formData.append('status', purchaseForm.value.status);
        formData.append('due_date', purchaseForm.value.due_date);
        formData.append('estimated_arrival_date', purchaseForm.value.estimated_arrival_date);
        formData.append('actual_arrival_date', purchaseForm.value.actual_arrival_date);
        formData.append('supplier_id', purchaseForm.value.supplier_id);

        purchaseForm.value.purchase_details.forEach((detail, index) => {
            Object.keys(detail).forEach(key => {
                let value = detail[key];
                if (key === 'purchase_price') {
                    value = Number(value) || 0;
                }
                formData.append(`purchase_details[${index}][${key}]`, value !== null && value !== undefined ? value : '');
            });
        });

        purchaseForm.value.purchase_payments.forEach((payment, index) => {
            Object.keys(payment).forEach(key => {
                let value = payment[key];
                if (key === 'amount') {
                    value = Number(value) || 0;
                }
                formData.append(`purchase_payments[${index}][${key}]`, value !== null && value !== undefined ? value : '');
            });
        });

        const taxesToSync = purchaseForm.value.taxes
            .filter(tax => tax.tax_id !== null && tax.tax_id !== undefined && tax.tax_id !== '');

        if (taxesToSync.length > 0) {
            taxesToSync.forEach((tax, index) => {
                formData.append(`taxes[${index}][tax_id]`, tax.tax_id);
            });
        }


        console.log('--- FormData Content SPADAAAAAAAAAAAAAAA ---');
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ': ' + pair[1]);
        }
        console.log('--- End FormData Content SPADAAAAAAAAAAAA ---');


        const response = await api.post('/purchases', formData, {
            headers: {
                'Authorization': `Bearer ${apiToken}`,
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Purchase data has been created successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        purchaseForm.value = {
            invoice_number: '',
            purchase_date: '',
            total_discount: '',
            shipping_amount: '',
            status: '',
            due_date: '',
            estimated_arrival_date: '',
            actual_arrival_date: '',
            supplier_id: '',
            purchase_details: [],
            purchase_payments: [
                {
                    payment_date: '',
                    amount: '',
                    due_date: '',
                    payment_method: '',
                    status: '',
                    note: ''
                }
            ],
            taxes: []
        }

    } catch (error) {
        console.error('Failed to create purchase:', error.response?.data || error.message)

        let errorMessage = 'Failed to add purchase. Please check the data or re-login.';
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
