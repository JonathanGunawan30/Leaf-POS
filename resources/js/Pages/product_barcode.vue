<template>
    <div class="bg-gray-100 min-h-screen">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Print Barcode Product</span>
                </div>

                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Search Product</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Product Name or SKU</p>
                                <p class="text-gray-500 text-xs">Search by product name or SKU code.</p>
                                <div class="flex self-stretch gap-2.5">
                                    <input
                                        v-model="searchQuery"
                                        placeholder="Type product name or SKU..."
                                        class="flex flex-1 justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                        style="height: 45px"
                                    />
                                    <button
                                        @click="searchProducts"
                                        :disabled="isLoading"
                                        class="flex justify-center items-center gap-2.5 py-[9px] px-[20px] bg-[#2F8451] hover:bg-[#256F43] disabled:bg-gray-400 rounded-[10px] h-[45px] transition-all duration-200">
                                        <span class="text-white text-sm font-medium">{{ isLoading ? 'Searching...' : 'Search' }}</span>
                                    </button>
                                </div>
                                <p v-if="searchError" class="text-red-500 text-sm mt-1">{{ searchError }}</p> </div>

                            <div v-if="searchResults.length > 0" class="flex self-stretch justify-start items-start flex-col gap-2">
                                <p class="text-[#000000] text-xs font-medium">Search Results:</p>
                                <div class="flex flex-row flex-wrap gap-3 py-2 w-full">
                                    <div
                                        v-for="product in searchResults"
                                        :key="product.id"
                                        @click="selectProduct(product)"
                                        class="flex-none w-64 p-3 border rounded-[10px] hover:bg-gray-50 cursor-pointer shadow-sm bg-white"
                                    >
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-[#000000] truncate">{{ product.name }}</p>
                                            <p class="text-xs text-gray-500">SKU: {{ product.sku }}</p>
                                            <p class="text-xs text-gray-500">Barcode: {{ product.barcode }}</p>
                                            <p class="text-xs text-gray-500">Selling Price: Rp {{ formatCurrency(product.selling_price) }}</p>
                                            <p class="text-xs text-gray-500">Stock: {{ product.stock }} {{ product.unit?.code || '' }}</p>
                                        </div>
                                        <div class="text-xs text-blue-600 mt-2 text-right">Select</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="selectedProduct" class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Selected Product & Barcode Preview</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">

                            <div class="flex self-stretch justify-start items-start flex-col gap-2 p-4 bg-gray-50 rounded-[10px]">
                                <h3 class="text-lg font-medium text-[#000000]">{{ selectedProduct.name }}</h3>
                                <div class="grid grid-cols-2 gap-4 w-full text-sm">
                                    <div>
                                        <p class="text-gray-600">SKU: {{ selectedProduct.sku }}</p>
                                        <p class="text-gray-600">Barcode: {{ selectedProduct.barcode }}</p>
                                        <p class="text-gray-600">Brand: {{ selectedProduct.brand || '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Selling Price: Rp {{ formatCurrency(selectedProduct.selling_price) }}</p>
                                        <p class="text-gray-600">Stock: {{ selectedProduct.stock }} {{ selectedProduct.unit?.code || '' }}</p>
                                        <p class="text-gray-600">Category: {{ selectedProduct.category?.name || '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-center items-center flex-col gap-2 p-4 border rounded-[10px]">
                                <p class="text-sm font-medium text-[#000000]">Barcode Preview</p>
                                <div class="flex flex-col items-center gap-2 p-4 bg-white border rounded">
                                    <div v-if="selectedProduct.barcode_images" class="barcode-container">
                                        <img :src="selectedProduct.barcode_images" alt="Barcode" class="max-w-full h-auto">
                                    </div>
                                    <canvas v-else ref="barcodeCanvas" class="border"></canvas>
                                    <p class="text-xs text-gray-600">{{ selectedProduct.barcode }}</p>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Print Quantity</p>
                                <p class="text-gray-500 text-xs">How many barcode labels to print?</p>
                                <input
                                    v-model.number="printQuantity"
                                    type="number"
                                    min="1"
                                    max="100"
                                    placeholder="Enter quantity"
                                    class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                    style="height: 45px"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="selectedProduct && printQuantity > 0" class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Print Preview (A4 Paper)</p>
                        <div class="flex self-stretch justify-center items-start p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                            <div class="print-preview bg-white border-2 border-gray-300 shadow-lg" style="width: 210mm; min-height: 297mm; transform-origin: top;">
                                <div class="p-4 grid grid-cols-3 gap-4">
                                    <div
                                        v-for="i in Math.min(printQuantity, 24)"
                                        :key="i"
                                        class="flex flex-col items-center justify-center p-2 border border-dashed border-gray-300"
                                        style="width: 60mm; height: 30mm;">
                                        <div class="barcode-mini mb-1" v-if="!selectedProduct.barcode_images"></div>
                                        <img v-else :src="selectedProduct.barcode_images" class="w-12 h-6 object-contain mb-1" alt="barcode">
                                        <p class="text-xs text-center truncate" style="font-size: 8px;">{{ selectedProduct.name }}</p>
                                        <p class="text-xs text-center font-mono" style="font-size: 7px;">{{ selectedProduct.barcode }}</p>
                                        <p class="text-xs text-center" style="font-size: 7px;">Rp {{ formatCurrency(selectedProduct.selling_price) }}</p>
                                    </div>
                                </div>
                                <div v-if="printQuantity > 24" class="text-center p-4 text-sm text-gray-500">
                                    ... and {{ printQuantity - 24 }} more labels
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="selectedProduct" class="flex self-stretch justify-start items-center gap-2.5 pt-4">
                    <button
                        @click="printBarcodes"
                        :disabled="!selectedProduct || printQuantity <= 0"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] disabled:bg-gray-400 disabled:cursor-not-allowed rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Generate PDF</span>
                    </button>
                    <button
                        @click="printDirectly"
                        :disabled="!selectedProduct || printQuantity <= 0"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] disabled:bg-gray-400 disabled:cursor-not-allowed rounded-[10px] w-[140px] h-[48px] transition-all duration-200 hover:scale-105"
                        style="background-color: #2478ff;"
                    >
                        <span class="text-white text-sm font-medium leading-6">Print Now</span>
                    </button>
                    <button
                        @click="clearSelection"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] rounded-[10px] w-[100px] h-[48px] transition-all duration-200 hover:scale-105"
                        style="background-color: #6d6d6d;"
                    >
                        <span class="text-white text-sm font-medium leading-6">Clear</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import { onMounted, ref, nextTick, watch } from 'vue'
import axios from 'axios'
import Swal from "sweetalert2"
import { router } from '@inertiajs/vue3'
import jsPDF from 'jspdf'

const token = localStorage.getItem('X-API-TOKEN')

const searchQuery = ref('')
const searchResults = ref([])
const selectedProduct = ref(null)
const printQuantity = ref(1)
const barcodeCanvas = ref(null)
const isLoading = ref(false)
const searchError = ref('')
let debounceTimeout = null

const searchProducts = async () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = []
        searchError.value = ''
        return
    }

    if (debounceTimeout) {
        clearTimeout(debounceTimeout)
    }

    debounceTimeout = setTimeout(async () => {
        try {
            isLoading.value = true
            searchError.value = ''

            const response = await axios.get(`/api/products`, {
                params: {
                    search: searchQuery.value
                },
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            })

            searchResults.value = response.data.data || response.data
            if (searchResults.value.length === 0) {
                searchError.value = 'No products found matching your search.'
            }
        } catch (error) {
            console.error('Failed to search products:', error)
            searchResults.value = []
            searchError.value = 'Failed to search products. Please try again later.' // Display a subtle error
        } finally {
            isLoading.value = false
        }
    }, 500)
}

const selectProduct = (product) => {
    selectedProduct.value = product
    searchResults.value = []
    searchQuery.value = product.name
    searchError.value = ''

    nextTick(() => {
        generateBarcodePreview()
    })
}

const generateBarcodePreview = () => {
    if (!barcodeCanvas.value || !selectedProduct.value || selectedProduct.value.barcode_images) return

    const canvas = barcodeCanvas.value
    const ctx = canvas.getContext('2d')

    canvas.width = 200
    canvas.height = 60

    ctx.clearRect(0, 0, canvas.width, canvas.height)
    ctx.fillStyle = 'white'
    ctx.fillRect(0, 0, canvas.width, canvas.height)

    const barcode = selectedProduct.value.barcode
    ctx.fillStyle = 'black'

    let x = 10
    for (let i = 0; i < barcode.length; i++) {
        const charCode = barcode.charCodeAt(i)
        const pattern = charCode % 4 + 1

        for (let j = 0; j < pattern; j++) {
            const width = (charCode % 3) + 1
            const height = 35
            ctx.fillRect(x, 10, width, height)
            x += width + 1
        }
        x += 2
    }
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID').format(amount)
}

const printBarcodes = async () => {
    if (!selectedProduct.value || printQuantity.value <= 0) return

    try {
        Swal.fire({
            title: 'Generating PDF...',
            text: 'Please wait while we prepare your barcode labels.',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading()
            }
        })

        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        })

        const pageWidth = 210
        const pageHeight = 297
        const margin = 10

        const labelWidth = 60
        const labelHeight = 30
        const labelsPerRow = 3
        const labelsPerCol = 9
        const labelsPerPage = labelsPerRow * labelsPerCol

        const horizontalSpacing = (pageWidth - 2 * margin - labelsPerRow * labelWidth) / (labelsPerRow - 1)
        const verticalSpacing = (pageHeight - 2 * margin - labelsPerCol * labelHeight) / (labelsPerCol - 1)

        let currentPage = 1
        let labelCount = 0

        for (let i = 0; i < printQuantity.value; i++) {
            if (labelCount > 0 && labelCount % labelsPerPage === 0) {
                pdf.addPage()
                currentPage++
            }

            const labelIndex = labelCount % labelsPerPage
            const row = Math.floor(labelIndex / labelsPerRow)
            const col = labelIndex % labelsPerRow

            const x = margin + col * (labelWidth + horizontalSpacing)
            const y = margin + row * (labelHeight + verticalSpacing)

            pdf.setDrawColor(200, 200, 200)
            pdf.setLineWidth(0.1)
            pdf.rect(x, y, labelWidth, labelHeight)

            if (selectedProduct.value.barcode_images) {
                try {
                    const barcodeImg = selectedProduct.value.barcode_images
                    pdf.addImage(barcodeImg, 'PNG', x + 5, y + 3, 50, 12)
                } catch (error) {
                    console.warn('Failed to add barcode image:', error)
                    drawSimpleBarcode(pdf, x + 5, y + 3, 50, 12, selectedProduct.value.barcode)
                }
            } else {
                drawSimpleBarcode(pdf, x + 5, y + 3, 50, 12, selectedProduct.value.barcode)
            }
            pdf.setFontSize(8)
            pdf.setFont('helvetica', 'bold')
            const productName = selectedProduct.value.name.length > 25
                ? selectedProduct.value.name.substring(0, 25) + '...'
                : selectedProduct.value.name
            pdf.text(productName, x + 2, y + 20, { maxWidth: labelWidth - 4 })

            pdf.setFontSize(7)
            pdf.setFont('helvetica', 'normal')
            pdf.text(selectedProduct.value.barcode, x + 2, y + 23)

            pdf.setFontSize(7)
            pdf.setFont('helvetica', 'bold')
            const price = `Rp ${formatCurrency(selectedProduct.value.selling_price)}`
            pdf.text(price, x + 2, y + 27)

            labelCount++
        }

        Swal.close()

        const filename = `barcode_${selectedProduct.value.sku}_${new Date().getTime()}.pdf`
        const result = await Swal.fire({
            title: 'PDF Generated!',
            text: `${printQuantity.value} barcode labels ready to print.`,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Download PDF',
            cancelButtonText: 'Print Now',
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#2F8451',
            buttonsStyling: true,
        });

        if (result.isConfirmed) {
            pdf.save(filename)
        } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
            const pdfBlob = pdf.output('blob')
            const pdfUrl = URL.createObjectURL(pdfBlob)
            const printWindow = window.open(pdfUrl, '_blank')

            printWindow.onload = () => {
                setTimeout(() => {
                    printWindow.print()
                }, 500)
            }
        }

    } catch (error) {
        console.error('Failed to generate PDF:', error)

        Swal.fire({
            title: 'PDF Generation Failed!',
            text: 'Failed to generate barcode PDF. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md'
            },
            buttonsStyling: false
        })
    }
}

const drawSimpleBarcode = (pdf, x, y, width, height, barcodeText) => {
    pdf.setFillColor(0, 0, 0)

    let currentX = x

    const segmentWidth = width / barcodeText.length;
    const barWidth = Math.max(0.5, segmentWidth / 4);

    for (let i = 0; i < barcodeText.length; i++) {
        const charCode = barcodeText.charCodeAt(i)
        const pattern = charCode % 4 + 1

        for (let j = 0; j < pattern; j++) {
            if (j % 2 === 0) {
                pdf.rect(currentX, y, barWidth, height, 'F')
            }
            currentX += barWidth
        }
        currentX += barWidth * 0.5
    }
}

const printDirectly = async () => {
    if (!selectedProduct.value || printQuantity.value <= 0) return

    try {
        Swal.fire({
            title: 'Preparing Print...',
            text: 'Opening print dialog...',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 1000,
            willOpen: () => {
                Swal.showLoading()
            }
        })

        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        })

        const pageWidth = 210
        const pageHeight = 297
        const margin = 10
        const labelWidth = 60
        const labelHeight = 30
        const labelsPerRow = 3
        const labelsPerCol = 9
        const labelsPerPage = labelsPerRow * labelsPerCol

        const horizontalSpacing = (pageWidth - 2 * margin - labelsPerRow * labelWidth) / (labelsPerRow - 1)
        const verticalSpacing = (pageHeight - 2 * margin - labelsPerCol * labelHeight) / (labelsPerCol - 1)

        let labelCount = 0

        for (let i = 0; i < printQuantity.value; i++) {
            if (labelCount > 0 && labelCount % labelsPerPage === 0) {
                pdf.addPage()
            }

            const labelIndex = labelCount % labelsPerPage
            const row = Math.floor(labelIndex / labelsPerRow)
            const col = labelIndex % labelsPerRow

            const x = margin + col * (labelWidth + horizontalSpacing)
            const y = margin + row * (labelHeight + verticalSpacing)

            pdf.setDrawColor(200, 200, 200)
            pdf.setLineWidth(0.1)
            pdf.rect(x, y, labelWidth, labelHeight)

            if (selectedProduct.value.barcode_images) {
                try {
                    pdf.addImage(selectedProduct.value.barcode_images, 'PNG', x + 5, y + 3, 50, 12)
                } catch (error) {
                    drawSimpleBarcode(pdf, x + 5, y + 3, 50, 12, selectedProduct.value.barcode)
                }
            } else {
                drawSimpleBarcode(pdf, x + 5, y + 3, 50, 12, selectedProduct.value.barcode)
            }

            pdf.setFontSize(8)
            pdf.setFont('helvetica', 'bold')
            const productName = selectedProduct.value.name.length > 25
                ? selectedProduct.value.name.substring(0, 25) + '...'
                : selectedProduct.value.name
            pdf.text(productName, x + 2, y + 20, { maxWidth: labelWidth - 4 })

            pdf.setFontSize(7)
            pdf.setFont('helvetica', 'normal')
            pdf.text(selectedProduct.value.barcode, x + 2, y + 23)

            pdf.setFontSize(7)
            pdf.setFont('helvetica', 'bold')
            const price = `Rp ${formatCurrency(selectedProduct.value.selling_price)}`
            pdf.text(price, x + 2, y + 27)

            labelCount++
        }

        const pdfBlob = pdf.output('blob')
        const pdfUrl = URL.createObjectURL(pdfBlob)
        const printWindow = window.open(pdfUrl, '_blank')

        printWindow.onload = () => {
            setTimeout(() => {
                printWindow.print()
                Swal.close()
            }, 500)
        }

    } catch (error) {
        console.error('Failed to print directly:', error)
        Swal.fire({
            title: 'Print Failed!',
            text: 'Failed to open print dialog. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md'
            },
            buttonsStyling: false
        })
    }
}

const clearSelection = () => {
    selectedProduct.value = null
    searchQuery.value = ''
    searchResults.value = []
    printQuantity.value = 1
    searchError.value = ''
}

watch(selectedProduct, () => {
    if (selectedProduct.value) {
        nextTick(() => {
            generateBarcodePreview()
        })
    }
}, { deep: true })

watch(searchQuery, () => {
    searchProducts()
})

onMounted(async () => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
    }
})
</script>

<style scoped>
.print-preview {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.barcode-mini {
    width: 40px;
    height: 15px;
    background: repeating-linear-gradient(
        to right,
        black 0px,
        black 1px,
        white 1px,
        white 2px
    );
}

@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
    }
}

</style>
