<template>
    <div class="bg-gray-50 min-h-screen">
        <Sidebar :auth="auth">
            <div class="max-w-4xl mx-auto p-6">
                <!-- Header Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="p-3 bg-lp-green bg-opacity-10 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0,0,256,256">
                                <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(4,4)"><path d="M20,10c-3.314,0 -6,2.686 -6,6v32c0,3.314 2.686,6 6,6h24c3.314,0 6,-2.686 6,-6v-19.34375c0,-1.592 -0.63281,-3.11719 -1.75781,-4.24219l-12.65625,-12.65625c-1.126,-1.125 -2.65119,-1.75781 -4.24219,-1.75781zM20,14h10v10c0,3.314 2.686,6 6,6h10v18c0,1.105 -0.895,2 -2,2h-24c-1.105,0 -2,-0.895 -2,-2v-32c0,-1.105 0.895,-2 2,-2zM34,15.82813l10.17188,10.17188h-8.17187c-1.105,0 -2,-0.895 -2,-2zM24,34c-1.104,0 -2,0.895 -2,2c0,1.105 0.896,2 2,2h16c1.104,0 2,-0.895 2,-2c0,-1.105 -0.896,-2 -2,-2zM24,42c-1.104,0 -2,0.895 -2,2c0,1.105 0.896,2 2,2h16c1.104,0 2,-0.895 2,-2c0,-1.105 -0.896,-2 -2,-2z"></path></g></g>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Generate Purchase Order</h2>
                            <p class="text-gray-600">Create a new purchase order for your suppliers</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="generatePDF" class="space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-lp-green" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            Basic Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Supplier -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select
                                        v-model="form.supplier_id"
                                        @change="fetchSupplierProducts"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lp-green focus:border-lp-green transition-colors appearance-none bg-white"
                                        required
                                    >
                                        <option value="">Select Supplier</option>
                                        <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.label }}</option>
                                    </select>
                                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Purchase Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    v-model="form.date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lp-green focus:border-lp-green transition-colors"
                                    required
                                />
                            </div>

                            <!-- Tax -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Tax</label>
                                <div class="relative">
                                    <select
                                        v-model="form.tax_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lp-green focus:border-lp-green transition-colors appearance-none bg-white"
                                    >
                                        <option value="">No Tax</option>
                                        <option v-for="t in taxes" :key="t.id" :value="t.id">{{ t.name }} </option>
                                    </select>
                                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-lp-green" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                </svg>
                                Order Items
                            </h3>
                            <button
                                type="button"
                                @click="addItem"
                                :disabled="!form.supplier_id || loading"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-lp-green text-white rounded-lg hover:bg-lp-green/90 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div v-if="loading" class="text-center py-8">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-lp-green mx-auto"></div>
                            <p class="mt-2 text-gray-600">Loading products...</p>
                        </div>

                        <div class="space-y-4" v-else>
                            <!-- Items List -->
                            <div v-if="form.items.length === 0" class="text-center py-12 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                </svg>
                                <p class="text-lg font-medium">No items added yet</p>
                                <p class="text-sm">Select a supplier first, then click "Add Item"</p>
                            </div>

                            <!-- Column Headers - Added this section -->
                            <div v-if="form.items.length > 0" class="hidden md:grid md:grid-cols-12 gap-4 px-4 py-2 bg-gray-50 rounded-lg font-medium text-sm text-gray-700">
                                <div class="md:col-span-6">Product</div>
                                <div class="md:col-span-2">Quantity</div>
                                <div class="md:col-span-3">Purchase Price</div>
                                <div class="md:col-span-1">Action</div>
                            </div>

                            <div v-for="(item, index) in form.items" :key="index" class="border border-gray-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <div class="md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1 md:hidden">Product</label>
                                        <select
                                            v-model="item.product_id"
                                            @change="updateProductPrice(index)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lp-green focus:border-lp-green transition-colors"
                                            required
                                        >
                                            <option value="">Select Product</option>
                                            <option v-for="p in supplierProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1 md:hidden">Quantity</label>
                                        <input
                                            type="number"
                                            v-model="item.quantity"
                                            placeholder="Qty"
                                            min="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lp-green focus:border-lp-green transition-colors"
                                            required
                                        />
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1 md:hidden">Purchase Price</label>
                                        <input
                                            type="text"
                                            :value="formatCurrency(item.purchase_price)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                            readonly
                                        />
                                    </div>
                                    <div class="md:col-span-1">
                                        <button
                                            type="button"
                                            @click="removeItem(index)"
                                            class="w-full md:w-auto p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        >
                                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Section -->
                            <div v-if="form.items.length > 0" class="flex justify-end mt-6">
                                <div class="bg-gray-50 rounded-lg p-4 min-w-72">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Subtotal:</span>
                                            <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
                                        </div>
                                        <div v-if="selectedTax" class="flex justify-between text-sm">
                                            <span class="text-gray-600">{{ selectedTax.name }}:</span>
                                            <span class="font-medium">{{ formatCurrency(taxAmount) }}</span>
                                        </div>
                                        <hr class="border-gray-300">
                                        <div class="flex justify-between font-bold text-lg">
                                            <span>Total:</span>
                                            <span class="text-lp-green">{{ formatCurrency(grandTotal) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-lp-green" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 3a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 4a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            Additional Notes
                        </h3>

                        <textarea
                            v-model="form.notes"
                            rows="4"
                            placeholder="Add any special instructions or notes for this purchase order..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lp-green focus:border-lp-green transition-colors resize-none"
                        ></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="isGenerating || form.items.length === 0"
                            class="px-8 py-3 bg-lp-green text-white rounded-lg hover:bg-lp-green/90 transition-colors font-medium flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="isGenerating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                            </svg>
                            {{ isGenerating ? 'Generating...' : 'Generate PDF' }}
                        </button>
                    </div>
                </form>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import Sidebar from "@/Components/Sidebar.vue"
import Swal from 'sweetalert2'

const props = defineProps({
    auth: Object,
})

const suppliers = ref([])
const taxes = ref([])
const supplierProducts = ref([])
const loading = ref(false)
const isGenerating = ref(false)

const form = useForm({
    supplier_id: '',
    date: new Date().toISOString().slice(0, 10),
    items: [],
    tax_id: '',
    notes: '',
})

const getAuthHeaders = () => {
    const token = localStorage.getItem('X-API-TOKEN')
    return {
        headers: {
            'Authorization': token ? `Bearer ${token}` : ''
        }
    }
}

const selectedSupplier = computed(() => {
    return suppliers.value.find(s => s.id == form.supplier_id)
})

const selectedTax = computed(() => {
    return taxes.value.find(t => t.id == form.tax_id)
})

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => {
        const quantity = parseInt(item.quantity) || 0
        const price = parseFloat(item.purchase_price) || 0
        return sum + (quantity * price)
    }, 0)
})

const taxAmount = computed(() => {
    if (!selectedTax.value) return 0
    const percentage = parseFloat(selectedTax.value.rate) || 0
    return subtotal.value * (percentage / 100)
})

const grandTotal = computed(() => {
    return subtotal.value + taxAmount.value
})

const poNumber = computed(() => {
    return `PO/${form.supplier_id}/${new Date().getFullYear()}/${String(Date.now()).slice(-6)}`
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount || 0)
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const getProductName = (productId) => {
    const product = supplierProducts.value.find(p => p.id == productId)
    return product ? product.name : '-'
}
const addItem = () => {
    form.items.push({
        product_id: '',
        quantity: 1,
        purchase_price: 0
    })
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

const fetchSupplierProducts = async () => {
    if (!form.supplier_id) {
        supplierProducts.value = []
        form.items = []
        return
    }

    try {
        loading.value = true
        const response = await axios.get(`/api/products`, getAuthHeaders())
        supplierProducts.value = response.data.data
        form.items = []
    } catch (error) {
        console.error('Error fetching supplier products:', error)
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to load supplier products',
            confirmButtonColor: '#2F8451'
        })
    } finally {
        loading.value = false
    }
}

const updateProductPrice = (index) => {
    const selectedProductId = form.items[index].product_id
    if (!selectedProductId) {
        form.items[index].purchase_price = 0
        return
    }

    const product = supplierProducts.value.find(p => p.id == selectedProductId)
    if (product) {
        form.items[index].purchase_price = product.purchase_price || 0
    }
}

const generatePDF = async () => {
    if (form.items.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Items!',
            text: 'Please add at least one item to generate PDF',
            confirmButtonColor: '#2F8451'
        })
        return
    }

    try {
        isGenerating.value = true

        const { jsPDF } = await import('jspdf')
        const doc = new jsPDF()
        doc.setFont('helvetica')
        let yPosition = 20
        doc.setFontSize(20)
        doc.setFont('helvetica', 'bold')
        doc.text('Purchase Order', 105, yPosition, { align: 'center' })
        yPosition += 10
        doc.setFontSize(12)
        doc.setFont('helvetica', 'normal')
        doc.text(`No: ${poNumber.value}`, 105, yPosition, { align: 'center' })
        yPosition += 8
        doc.text(`Tanggal: ${formatDate(form.date)}`, 105, yPosition, { align: 'center' })
        yPosition += 20
        doc.setFontSize(14)
        doc.setFont('helvetica', 'bold')
        doc.text('Supplier:', 20, yPosition)
        yPosition += 10
        doc.setFontSize(10)
        doc.setFont('helvetica', 'normal')

        const supplierInfo = [
            ['Nama:', selectedSupplier.value?.name || '-'],
            ['Perusahaan:', selectedSupplier.value?.company_name || '-'],
            ['Email:', selectedSupplier.value?.email || '-'],
            ['Telepon:', selectedSupplier.value?.phone || '-'],
            ['Alamat:', selectedSupplier.value?.address || '-']
        ]

        supplierInfo.forEach(([label, value]) => {
            doc.setFont('helvetica', 'bold')
            doc.text(label, 20, yPosition)
            doc.setFont('helvetica', 'normal')
            doc.text(value, 60, yPosition)
            yPosition += 6
        })

        yPosition += 10


        doc.setFontSize(14)
        doc.setFont('helvetica', 'bold')
        doc.text('Item:', 20, yPosition)

        yPosition += 10


        const tableHeaders = ['No', 'Nama Produk', 'Qty', 'Harga Satuan', 'Total']
        const colWidths = [15, 80, 20, 35, 35]
        const startX = 20


        doc.setFillColor(242, 242, 242)
        doc.rect(startX, yPosition - 5, 185, 8, 'F')

        doc.setFontSize(10)
        doc.setFont('helvetica', 'bold')
        let currentX = startX
        tableHeaders.forEach((header, index) => {
            doc.text(header, currentX + 2, yPosition)

            if (index > 0) {
                doc.line(currentX, yPosition - 5, currentX, yPosition + 3)
            }
            currentX += colWidths[index]
        })

        doc.setLineWidth(0.5)
        doc.rect(startX, yPosition - 5, 185, 8)

        yPosition += 8

        doc.setFont('helvetica', 'normal')
        form.items.forEach((item, index) => {
            const quantity = parseInt(item.quantity) || 0
            const price = parseFloat(item.purchase_price) || 0
            const total = quantity * price

            const rowData = [
                (index + 1).toString(),
                getProductName(item.product_id),
                quantity.toString(),
                formatCurrency(price),
                formatCurrency(total)
            ]

            currentX = startX
            rowData.forEach((data, colIndex) => {
                if (colIndex === 0) {
                    doc.text(data, currentX + 2, yPosition)
                } else if (colIndex === 1) {

                    const lines = doc.splitTextToSize(data, colWidths[colIndex] - 4)
                    doc.text(lines, currentX + 2, yPosition)
                } else {

                    doc.text(data, currentX + colWidths[colIndex] - 2, yPosition, { align: 'right' })
                }

                if (colIndex > 0) {
                    doc.line(currentX, yPosition - 5, currentX, yPosition + 3)
                }
                currentX += colWidths[colIndex]
            })

            doc.rect(startX, yPosition - 5, 185, 8)
            yPosition += 8
        })

        yPosition += 10

        const summaryStartX = 130
        doc.setFontSize(10)

        doc.setFont('helvetica', 'normal')
        doc.text('Subtotal:', summaryStartX, yPosition)
        doc.text(formatCurrency(subtotal.value), 200, yPosition, { align: 'right' })
        yPosition += 6

        if (selectedTax.value) {
            doc.text(`${selectedTax.value.name}:`, summaryStartX, yPosition)
            doc.text(formatCurrency(taxAmount.value), 200, yPosition, { align: 'right' })
            yPosition += 6
        }

        doc.setLineWidth(0.3)
        doc.line(summaryStartX, yPosition - 2, 200, yPosition - 2)
        yPosition += 2

        doc.setFont('helvetica', 'bold')
        doc.setFontSize(12)
        doc.text('Total:', summaryStartX, yPosition)
        doc.text(formatCurrency(grandTotal.value), 200, yPosition, { align: 'right' })
        yPosition += 15

        if (form.notes) {
            doc.setFontSize(14)
            doc.setFont('helvetica', 'bold')
            doc.text('Catatan:', 20, yPosition)
            yPosition += 8

            doc.setFontSize(10)
            doc.setFont('helvetica', 'normal')
            const noteLines = doc.splitTextToSize(form.notes, 170)
            doc.text(noteLines, 20, yPosition)
            yPosition += noteLines.length * 5 + 10
        }

        yPosition += 10
        doc.setFontSize(14)
        doc.setFont('helvetica', 'bold')
        doc.text('Syarat & Ketentuan:', 20, yPosition)
        yPosition += 8

        doc.setFontSize(10)
        doc.setFont('helvetica', 'normal')
        const terms = [
            '1. Pembayaran dilakukan maksimal 30 hari setelah barang diterima',
            '2. Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kesepakatan khusus',
            '3. Supplier wajib memberikan garansi sesuai dengan standar produk',
            '4. Pengiriman barang sesuai dengan jadwal yang telah disepakati',
            '5. Segala perubahan pesanan harus dikonfirmasi secara tertulis',
            '6. Dispute akan diselesaikan melalui musyawarah mufakat'
        ]

        terms.forEach(term => {
            const termLines = doc.splitTextToSize(term, 170)
            doc.text(termLines, 20, yPosition)
            yPosition += termLines.length * 5 + 2
        })

        yPosition += 20
        doc.setFontSize(12)
        doc.setFont('helvetica', 'normal')
        doc.text('Terima kasih, kami menantikan konfirmasi dan pengiriman dari pihak Anda.', 105, yPosition, { align: 'center' })

        doc.save(`${poNumber.value}.pdf`)

        const result = Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'PDF generated successfully!',
            confirmButtonColor: '#2F8451'
        }).then((result) => {
            if(result.isConfirmed || result.isDismissed){
                form.items = []
                form.reset()
                supplierProducts.value = [];
            }
        })

    } catch (error) {
        console.error('Error generating PDF:', error)
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to generate PDF. Please try again.',
            confirmButtonColor: '#2F8451'
        })
    } finally {
        isGenerating.value = false
    }
}

const fetchInitialData = async () => {
    try {
        const [suppliersRes, taxesRes] = await Promise.all([
            axios.get('/api/suppliers', getAuthHeaders()),
            axios.get('/api/taxes', getAuthHeaders()),
        ])
        suppliers.value = suppliersRes.data.data
        taxes.value = taxesRes.data.data
    } catch (error) {
        console.error('Error fetching initial data:', error)
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to load required data',
            confirmButtonColor: '#2F8451'
        })
    }
}

onMounted(() => {
    fetchInitialData()
})
</script>

<style scoped>

.bg-lp-green {
    background-color: #2F8451;
}

.text-lp-green {
    color: #2F8451;
}

.border-lp-green {
    border-color: #2F8451;
}

.focus\:ring-lp-green:focus {
    --tw-ring-color: #2F8451;
}

.focus\:border-lp-green:focus {
    border-color: #2F8451;
}

.hover\:bg-lp-green\/90:hover {
    background-color: rgba(47, 132, 81, 0.9);
}

.hover\:border-lp-green\/30:hover {
    border-color: rgba(47, 132, 81, 0.3);
}
</style>
