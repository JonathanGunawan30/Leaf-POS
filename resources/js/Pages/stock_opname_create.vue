<template>
    <div class="bg-gray-100 min-h-screen">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-4">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-5 bg-white rounded-xl shadow-sm">
                    <span class="text-gray-900 text-lg font-semibold">Create Stock Opname</span>
                </div>
                <div class="flex self-stretch justify-start items-start flex-col gap-4 w-full">
                    <div
                        class="flex flex-col w-full gap-4 p-5 bg-white rounded-xl shadow-sm">
                        <p class="self-stretch text-gray-900 text-base font-semibold">Stock Opname Information</p>
                        <div
                            class="flex self-stretch flex-col gap-4 p-5 bg-white border border-gray-200 rounded-lg">
                            <div class="flex self-stretch flex-col gap-1.5">
                                <label for="location" class="text-gray-900 text-xs font-medium">Location <span class="text-red-500">*</span></label>
                                <p class="text-gray-500 text-xs">e.g., Gudang Utama.</p>
                                <input id="location" v-model="stockOpnameForm.location" placeholder="Enter location"
                                       class="w-full p-2.5 bg-white border border-gray-300 rounded-lg h-11 text-gray-900 text-sm outline-none focus:border-blue-500"
                                       required/>
                            </div>
                            <div class="flex self-stretch flex-col gap-1.5">
                                <label for="status" class="text-gray-900 text-xs font-medium">Status <span class="text-red-500">*</span></label>
                                <select id="status" v-model="stockOpnameForm.status" required
                                        class="w-full p-2.5 bg-white border border-gray-300 rounded-lg h-11 text-gray-900 text-sm outline-none focus:border-blue-500">
                                    <option value="draft">Draft</option>
                                    <option value="submitted">Submitted</option>
                                </select>
                            </div>
                            <div class="flex self-stretch flex-col gap-1.5">
                                <label for="notes" class="text-gray-900 text-xs font-medium">Notes</label>
                                <textarea id="notes" v-model="stockOpnameForm.notes" placeholder="Add any relevant notes"
                                          class="w-full p-2.5 bg-white border border-gray-300 rounded-lg h-24 text-gray-900 text-sm outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch flex-col gap-4 p-5 bg-white rounded-xl shadow-sm">
                    <p class="self-stretch text-gray-900 text-base font-semibold">Scanner Mode</p>
                    <div class="flex gap-6">

                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <input type="radio" v-model="scannerMode" value="camera" class="text-blue-600 focus:ring-blue-500">
                            <i class="fas fa-camera text-gray-500 text-lg"></i>
                            <span class="text-sm text-gray-700 font-medium">Camera Scanner</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 transition-colors relative">
                            <input type="radio" v-model="scannerMode" value="physical" class="text-blue-600 focus:ring-blue-500">
                            <i class="fas fa-barcode text-blue-600 text-lg"></i>
                            <span class="text-sm text-blue-800 font-medium">Physical Scanner</span>
                            <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">Recommended</span>
                        </label>
                    </div>
                </div>

                <div v-if="scannerMode === 'camera'" class="flex self-stretch flex-col gap-4 p-5 bg-white rounded-xl shadow-sm">
                    <p class="self-stretch text-gray-900 text-base font-semibold">Camera Scanner</p>
                    <div class="flex self-stretch flex-col gap-4 p-5 bg-white border border-gray-200 rounded-lg">
                        <button @click="toggleScanner"
                                :disabled="isInitializingScanner"
                                :class="[
                                    isScannerActive ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700',
                                    'flex justify-center items-center gap-2.5 py-2.5 px-5 rounded-lg transition-all duration-200 text-white font-medium disabled:opacity-50 disabled:cursor-not-allowed text-sm'
                                ]">
                            <i v-if="isInitializingScanner" class="fas fa-spinner fa-spin"></i>
                            <i v-else-if="isScannerActive" class="fas fa-stop-circle"></i>
                            <i v-else class="fas fa-video"></i>
                            <span v-if="isInitializingScanner">Initializing...</span>
                            <span v-else>{{ isScannerActive ? 'Stop Scanning' : 'Start Camera Scanner' }}</span>
                        </button>

                        <div v-if="showPermissionInfo" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-center gap-3">
                            <i class="fas fa-info-circle text-yellow-600 text-lg"></i>
                            <p class="text-yellow-800 text-sm">
                                Camera permission required for barcode scanning. Please allow camera access when prompted.
                            </p>
                        </div>

                        <div v-if="isScannerActive" ref="scannerViewport" id="interactive" class="viewport w-full h-80 bg-gray-200 flex justify-center items-center overflow-hidden relative rounded-lg">
                            <div class="drawingBuffer absolute top-0 left-0 w-full h-full pointer-events-none"></div>
                            <div class="laser absolute top-1/2 left-0 w-full h-0.5 bg-red-500 transform -translate-y-1/2 opacity-75 animate-pulse"></div>
                            <div v-if="!scannerReady" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-60 text-white">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-white mx-auto mb-3"></div>
                                    <p class="text-base">Initializing camera...</p>
                                </div>
                            </div>
                        </div>

                        <p v-if="scannedBarcode" class="text-sm text-green-700 mt-2 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Last Scanned: <strong class="text-gray-900">{{ scannedBarcode }}</strong>
                        </p>
                        <p v-if="scannerError" class="text-sm text-red-600 mt-2 flex items-center gap-2">
                            <i class="fas fa-times-circle text-red-500"></i>
                            Error: {{ scannerError }}
                        </p>
                        <p v-if="isScannerActive && !isScanningEnabled" class="text-sm text-gray-600 mt-2 flex items-center gap-2">
                            <i class="fas fa-pause-circle text-gray-500"></i>
                            Scanning paused (1s delay)...
                        </p>
                    </div>
                </div>

                <div v-if="scannerMode === 'physical'" class="flex self-stretch flex-col gap-4 p-5 bg-white rounded-xl shadow-sm">
                    <p class="self-stretch text-gray-900 text-base font-semibold">Physical Scanner</p>
                    <div class="flex self-stretch flex-col gap-4 p-5 bg-white border border-gray-200 rounded-lg">

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-3">
                            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                            <p class="text-blue-800 text-sm">
                                <strong>Physical Scanner Mode Active</strong> <br>
                                Click in the input field below, then scan your barcode. The scanner will automatically input the barcode and process it.
                            </p>
                        </div>

                        <div class="flex self-stretch flex-col gap-1.5">
                            <label for="physical-barcode-input" class="text-gray-900 text-xs font-medium">Scan Barcode Here</label>
                            <input
                                id="physical-barcode-input"
                                ref="physicalScannerInput"
                                v-model="physicalScannerValue"
                                @keydown.enter="handlePhysicalScannerInput"
                                @input="handlePhysicalScannerChange"
                                placeholder="Click here and scan your barcode..."
                                class="w-full p-2.5 bg-yellow-50 border-2 border-yellow-300 rounded-lg h-11 text-gray-900 text-sm outline-none focus:border-blue-500 focus:bg-white"
                            />
                            <p class="text-gray-500 text-xs">Focus on this field and use your physical scanner</p>
                        </div>

                        <div v-if="physicalScannerStatus.isProcessing" class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-gray-600"></i>
                            <span class="text-sm text-gray-700">Processing barcode...</span>
                        </div>

                        <div v-if="physicalScannerStatus.lastScanned" class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <p class="text-green-800 text-sm">
                                Last Scanned: <strong class="text-gray-900">{{ physicalScannerStatus.lastScanned }}</strong>
                            </p>
                            <p class="text-green-600 text-xs mt-1">{{ physicalScannerStatus.lastMessage }}</p>
                        </div>

                        <div v-if="physicalScannerStatus.error" class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-center gap-2">
                            <i class="fas fa-times-circle text-red-600"></i>
                            <p class="text-red-800 text-sm">Error: {{ physicalScannerStatus.error }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="flex self-stretch flex-col gap-4 p-5 bg-white rounded-xl shadow-sm">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-gray-900 text-base font-semibold">Stock Opname Items</p>
                        <button @click="addStockOpnameItem" type="button"
                                class="flex justify-center items-center gap-2 py-2 px-4 bg-green-600 hover:bg-green-700 rounded-lg transition-all duration-200 text-white text-sm font-medium">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Item</span>
                        </button>
                    </div>

                    <div v-if="stockOpnameForm.items.length === 0"
                         class="flex justify-center items-center p-10 border-2 border-dashed border-gray-300 rounded-lg w-full bg-gray-50">
                        <p class="text-gray-500 text-sm">No items added yet. Click "Add Item" or scan a barcode to start.</p>
                    </div>

                    <div v-for="(item, index) in stockOpnameForm.items" :key="index"
                         class="flex self-stretch flex-col gap-4 p-5 bg-white border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-gray-900 font-medium">Item {{ index + 1 }}</h4>
                            <button @click="removeStockOpnameItem(index)" type="button"
                                    class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center gap-1">
                                <i class="fas fa-trash-alt"></i>
                                Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-gray-900 text-xs font-medium">Product <span class="text-red-500">*</span></label>
                                <select v-model="item.product_id" required
                                        class="w-full p-2.5 bg-white border border-gray-300 rounded-lg h-11 text-gray-900 text-sm outline-none focus:border-blue-500">
                                    <option value="" disabled selected>Select product</option>
                                    <option v-for="product_option in products" :key="product_option.id" :value="product_option.id">
                                        {{ product_option.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-gray-900 text-xs font-medium">Actual Stock <span class="text-red-500">*</span></label>
                                <input v-model.number="item.actual_stock" type="number" min="0" required
                                       @input="preventNegativeValues(item, 'actual_stock')"
                                       class="w-full p-2.5 bg-white border border-gray-300 rounded-lg h-11 text-gray-900 text-sm outline-none focus:border-blue-500"
                                       placeholder="Enter actual stock quantity"/>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-gray-900 text-xs font-medium">Item Notes</label>
                            <textarea v-model="item.notes" placeholder="Notes for this item"
                                      class="w-full p-2.5 bg-white border border-gray-300 rounded-lg h-20 text-gray-900 text-sm outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createStockOpname"
                        class="flex justify-center items-center gap-2 py-2.5 px-6 bg-green-700 hover:bg-green-800 rounded-lg transition-all duration-200 text-white text-base font-medium shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-save"></i>
                        <span>Submit Stock Opname</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import { onMounted, ref, onBeforeUnmount, nextTick, watch } from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";
import Quagga from 'quagga';
import '@fortawesome/fontawesome-free/css/all.min.css';

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

const stockOpnameForm = ref({
    location: '',
    status: 'draft',
    notes: '',
    items: []
})

const products = ref([])

const scannerMode = ref('physical')

const isScannerActive = ref(false)
const scannedBarcode = ref('')
const scannerError = ref('')
const scannerViewport = ref(null)
const isInitializingScanner = ref(false)
const scannerReady = ref(false)
const showPermissionInfo = ref(false)
const isScanningEnabled = ref(true)

const physicalScannerInput = ref(null)
const physicalScannerValue = ref('')
const physicalScannerStatus = ref({
    isProcessing: false,
    lastScanned: '',
    lastMessage: '',
    error: ''
})

let audioContext = null
let beepAudio = null
const audioPool = []
const maxPoolSize = 5
let currentAudioIndex = 0
const isAudioInitialized = ref(false)

watch(scannerMode, (newMode) => {
    if (newMode === 'camera' && isScannerActive.value) {
        stopScanner()
    } else if (newMode === 'physical') {
        stopScanner()
        physicalScannerValue.value = ''
        physicalScannerStatus.value = {
            isProcessing: false,
            lastScanned: '',
            lastMessage: '',
            error: ''
        }
        nextTick(() => {
            setTimeout(() => {
                if (physicalScannerInput.value) {
                    physicalScannerInput.value.focus()
                }
            }, 100)
        })
    }
})

const handlePhysicalScannerChange = (event) => {
    if (physicalScannerStatus.value.error) {
        physicalScannerStatus.value.error = ''
    }
}

const handlePhysicalScannerInput = async (event) => {
    event.preventDefault()

    const barcode = physicalScannerValue.value.trim()

    if (!barcode) {
        physicalScannerStatus.value.error = 'Please scan a barcode.'
        physicalScannerValue.value = ''
        nextTick(() => physicalScannerInput.value?.focus())
        return
    }

    if (barcode.length < 3) {
        physicalScannerStatus.value.error = 'Barcode too short. Please scan a valid barcode.'
        physicalScannerValue.value = ''
        nextTick(() => physicalScannerInput.value?.focus())
        return
    }

    physicalScannerStatus.value.isProcessing = true
    physicalScannerStatus.value.error = ''

    try {
        await processScannedBarcode(barcode, 'physical')

        physicalScannerStatus.value.lastScanned = barcode
        physicalScannerStatus.value.isProcessing = false
        physicalScannerStatus.value.error = ''

    } catch (error) {
        physicalScannerStatus.value.isProcessing = false

        console.error('Physical scanner processing error:', error)
    } finally {
        physicalScannerValue.value = ''
        nextTick(() => {
            setTimeout(() => {
                if (physicalScannerInput.value) {
                    physicalScannerInput.value.focus()
                }
            }, 100);
        });
    }
}

const processScannedBarcode = async (barcode, source = 'camera') => {
    console.log(`Barcode ${source} detected:`, barcode)

    const productFound = products.value.find(p => p.barcode === barcode)

    if (productFound) {
        if (!isAudioInitialized.value) {
            await initializeAudio()
        }

        await playBeepSound()

        const existingItemIndex = stockOpnameForm.value.items.findIndex(item => item.product_id === productFound.id)

        if (existingItemIndex !== -1) {
            stockOpnameForm.value.items[existingItemIndex].actual_stock += 1
            console.log(`Incremented stock for ${productFound.name}. New stock: ${stockOpnameForm.value.items[existingItemIndex].actual_stock}`)

            if (source === 'physical') {
                physicalScannerStatus.value.lastMessage = `Stock updated: ${productFound.name} (${stockOpnameForm.value.items[existingItemIndex].actual_stock})`
            }
        } else {
            stockOpnameForm.value.items.push({
                product_id: productFound.id,
                actual_stock: 1,
                notes: `Scanned via ${source === 'physical' ? 'physical scanner' : 'camera'}: ${barcode}`
            })
            console.log(`Added ${productFound.name} with stock 1.`)

            if (source === 'physical') {
                physicalScannerStatus.value.lastMessage = `Added: ${productFound.name} (1)`
            }
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            icon: 'success',
            title: `${productFound.name} ${existingItemIndex !== -1 ? 'updated' : 'added'}!`
        })

    } else {
        console.warn(`Product with barcode ${barcode} not found.`)
        const errorMessage = `Product with barcode ${barcode} not found.`

        if (source === 'physical') {
            physicalScannerStatus.value.error = errorMessage
        } else {
            scannerError.value = errorMessage
            setTimeout(() => {
                scannerError.value = ''
            }, 3000)
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            icon: 'warning',
            title: `Product with barcode ${barcode} not found!`
        })

        throw new Error(errorMessage)
    }
}

const createBeepSound = () => {
    try {
        const sampleRate = 22050
        const duration = 0.2
        const frequency = 800
        const samples = sampleRate * duration

        const buffer = new ArrayBuffer(44 + samples * 2)
        const view = new DataView(buffer)

        const writeString = (offset, string) => {
            for (let i = 0; i < string.length; i++) {
                view.setUint8(offset + i, string.charCodeAt(i))
            }
        }

        writeString(0, 'RIFF')
        view.setUint32(4, 36 + samples * 2, true)
        writeString(8, 'WAVE')
        view.setUint32(12, 0x20746D66, true) // 'fmt '
        view.setUint32(16, 16, true)
        view.setUint16(20, 1, true)
        view.setUint16(22, 1, true)
        view.setUint32(24, sampleRate, true)
        view.setUint32(28, sampleRate * 2, true)
        view.setUint16(32, 2, true)
        view.setUint16(34, 16, true)
        writeString(36, 'data')
        view.setUint32(40, samples * 2, true)

        let offset = 44
        for (let i = 0; i < samples; i++) {
            const sample = Math.sin(2 * Math.PI * frequency * i / sampleRate) * 0.8
            view.setInt16(offset, sample * 0x7FFF, true)
            offset += 2
        }

        const blob = new Blob([buffer], { type: 'audio/wav' })
        return URL.createObjectURL(blob)
    } catch (error) {
        return null
    }
}

const initAudioPool = () => {
    try {

        const beepUrl = createBeepSound()
        const fallbackBeepUrl = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmAaAzSIzPLU'

        for (let i = 0; i < maxPoolSize; i++) {
            const audio = new Audio()
            audio.src = beepUrl || fallbackBeepUrl
            audio.volume = 0.8
            audio.preload = 'auto'
            audio.load()
            audioPool.push(audio)
        }

        return true
    } catch (error) {
        return false
    }
}

const initAudioContext = () => {
    try {
        if (!audioContext || audioContext.state === 'closed') {
            audioContext = new (window.AudioContext || window.webkitAudioContext)()
        }
        return audioContext
    } catch (error) {
        console.error('Failed to init audio context:', error)
        return null
    }
}

const playWebAudioBeep = async () => {
    try {
        const ctx = initAudioContext()
        if (!ctx) return false

        if (ctx.state === 'suspended') {
            await ctx.resume()
        }

        const oscillator = ctx.createOscillator()
        const gainNode = ctx.createGain()

        oscillator.type = 'sine'
        oscillator.frequency.value = 800

        const now = ctx.currentTime
        gainNode.gain.setValueAtTime(0, now)
        gainNode.gain.linearRampToValueAtTime(0.8, now + 0.01)
        gainNode.gain.exponentialRampToValueAtTime(0.01, now + 0.18)
        gainNode.gain.linearRampToValueAtTime(0, now + 0.2)

        oscillator.connect(gainNode)
        gainNode.connect(ctx.destination)

        oscillator.start(now)
        oscillator.stop(now + 0.2)

        return true
    } catch (error) {
        console.error('Web Audio beep failed:', error)
        return false
    }
}

const playBeepSound = async () => {
    console.log('🔊 Playing beep sound...')

    if (audioPool.length > 0) {
        try {
            const audio = audioPool[currentAudioIndex]
            currentAudioIndex = (currentAudioIndex + 1) % maxPoolSize

            audio.currentTime = 0
            audio.volume = 0.8

            const playPromise = audio.play()
            if (playPromise !== undefined) {
                await playPromise
                console.log('✅ Beep played successfully (Audio Pool)')
                return
            }
        } catch (error) {
            console.warn('Audio pool failed, trying Web Audio...', error)
        }
    }

    const webAudioSuccess = await playWebAudioBeep()
    if (webAudioSuccess) {
        console.log('✅ Beep played successfully (Web Audio)')
        return
    }

    try {
        const fallbackAudio = new Audio()
        fallbackAudio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmAaAzSIzPLU'
        fallbackAudio.volume = 0.7
        await fallbackAudio.play()
        console.log('✅ Beep played successfully (Fallback)')
    } catch (error) {
        console.error('❌ All beep methods failed:', error)
    }
}

const initializeAudio = async () => {
    if (isAudioInitialized.value) return

    try {
        initAudioPool()

        const ctx = initAudioContext()
        if (ctx && ctx.state === 'suspended') {
            await ctx.resume()
        }

        isAudioInitialized.value = true

        setTimeout(() => {
            playBeepSound()
        }, 500)

    } catch (error) {
        console.error('❌ Failed to initialize audio:', error)
    }
}


const fetchProducts = async () => {
    try {
        const { data } = await api.get('/products?per_page=100000000')
        products.value = data.data
    } catch (error) {
        console.error('Error fetching products:', error)
    }
}

const addStockOpnameItem = () => {
    stockOpnameForm.value.items.push({
        product_id: '',
        actual_stock: 0,
        notes: ''
    })
}

const removeStockOpnameItem = (index) => {
    stockOpnameForm.value.items.splice(index, 1)
}

const preventNegativeValues = (item, field) => {
    if (item[field] < 0) {
        item[field] = 0;
    }
}

const checkSecureContext = () => {
    const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1'
    if (!isSecure) {
        scannerError.value = 'Camera access requires HTTPS or localhost. Please use a secure connection.'
        return false
    }
    return true
}

const requestCameraPermission = async () => {
    try {
        showPermissionInfo.value = true

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Camera access not supported in this browser')
        }

        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })

        stream.getTracks().forEach(track => track.stop())

        showPermissionInfo.value = false
        return true
    } catch (error) {
        showPermissionInfo.value = false
        console.error('Camera permission error:', error)

        let errorMessage = 'Camera access denied or not available.'
        if (error.name === 'NotAllowedError') {
            errorMessage = 'Camera permission denied. Please allow camera access and try again.'
        } else if (error.name === 'NotFoundError') {
            errorMessage = 'No camera found on this device.'
        } else if (error.name === 'NotSupportedError') {
            errorMessage = 'Camera not supported in this browser.'
        }

        scannerError.value = errorMessage
        return false
    }
}

const initScanner = async () => {
    if (!isScannerActive.value || !scannerViewport.value) {
        console.warn("Scanner not active or viewport not ready.")
        return
    }

    if (!checkSecureContext()) {
        return
    }

    const hasPermission = await requestCameraPermission()
    if (!hasPermission) {
        isScannerActive.value = false
        return
    }

    scannerReady.value = false
    scannerError.value = ''
    isScanningEnabled.value = true

    const config = {
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: scannerViewport.value,
            constraints: {
                width: { min: 640, ideal: 1280 },
                height: { min: 480, ideal: 720 },
                aspectRatio: { min: 1, max: 2 },
                facingMode: "environment"
            },
        },
        decoder: {
            readers: [
                "ean_reader",
                "ean_8_reader",
                "code_39_reader",
                "code_39_vin_reader",
                "codabar_reader",
                "upc_reader",
                "upc_e_reader",
                "i2of5_reader",
                "code_128_reader"
            ]
        },
        locate: true,
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        frequency: 10,
        debug: false
    }

    Quagga.init(config, function (err) {
        if (err) {
            console.error('Quagga initialization error:', err)
            scannerError.value = 'Failed to initialize scanner: ' + err.message
            isScannerActive.value = false
            isInitializingScanner.value = false
            return
        }

        console.log("Quagga initialization finished. Ready to start")

        try {
            Quagga.start()
            scannerReady.value = true
            isInitializingScanner.value = false
            console.log("Scanner started successfully")
        } catch (startError) {
            console.error('Failed to start scanner:', startError)
            scannerError.value = 'Failed to start camera: ' + startError.message
            isScannerActive.value = false
            isInitializingScanner.value = false
        }
    })

    Quagga.onDetected(handleBarcodeDetected)
    Quagga.onProcessed(handleProcessedResult)
}

const handleBarcodeDetected = async (result) => {
    if (!isScanningEnabled.value) {
        return
    }

    const barcode = result.codeResult.code
    if (barcode && barcode.length > 3) {
        scannedBarcode.value = barcode
        console.log('Barcode detected:', barcode)

        isScanningEnabled.value = false
        setTimeout(() => {
            isScanningEnabled.value = true
            console.log('Scanning re-enabled.')
        }, 1000)

        try {
            await processScannedBarcode(barcode, 'camera')
        } catch (error) {
            console.error('Error processing camera scanned barcode:', error)
        }
    }
}

const handleProcessedResult = (result) => {
    if (scannerViewport.value && result) {
        const drawingCtx = Quagga.canvas.ctx.overlay
        const drawingCanvas = Quagga.canvas.dom.overlay

        if (drawingCtx && drawingCanvas) {
            drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height)

            if (result.boxes) {
                drawingCtx.strokeStyle = "green"
                drawingCtx.lineWidth = 2
                result.boxes.filter(box => box !== result.box).forEach(box => {
                    Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 })
                })
            }

            if (result.box) {
                drawingCtx.strokeStyle = "blue"
                drawingCtx.lineWidth = 2
                Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "blue", lineWidth: 2 })
            }

            if (result.codeResult && result.codeResult.code) {
                drawingCtx.strokeStyle = "red"
                drawingCtx.lineWidth = 3
                Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: 'red', lineWidth: 3 })
            }
        }
    }
}

const stopScanner = () => {
    try {
        if (Quagga && typeof Quagga.stop === 'function') {
            Quagga.stop()
            Quagga.offDetected(handleBarcodeDetected)
            Quagga.offProcessed(handleProcessedResult)
            console.log("Scanner stopped successfully")
        }
    } catch (error) {
        console.error('Error stopping scanner:', error)
    }

    isScannerActive.value = false
    scannerReady.value = false
    isInitializingScanner.value = false
    showPermissionInfo.value = false
    isScanningEnabled.value = true
}

const toggleScanner = async () => {
    if (isScannerActive.value) {
        stopScanner()
    } else {
        if (!isAudioInitialized.value) {
            await initializeAudio()
        }

        scannedBarcode.value = ''
        scannerError.value = ''
        isInitializingScanner.value = true
        isScannerActive.value = true
        isScanningEnabled.value = true

        await nextTick()

        setTimeout(() => {
            initScanner()
        }, 1000)
    }
}

const createStockOpname = async () => {
    try {
        const payload = {
            ...stockOpnameForm.value,
            items: stockOpnameForm.value.items.map(item => ({
                ...item,
                actual_stock: parseInt(item.actual_stock) || 0
            }))
        }

        const response = await api.post('/stock-opnames', payload)

        console.log('Stock Opname created:', response.data)

        stockOpnameForm.value = {
            location: '',
            status: 'draft',
            notes: '',
            items: []
        }

        Swal.fire({
            title: 'Success!',
            text: 'Stock Opname has been successfully recorded.',
            icon: 'success',
            confirmButtonText: 'OK'
        })

    } catch (error) {
        console.error('Failed to create Stock Opname:', error.response?.data || error.message)

        let errorMessage = 'Failed to create Stock Opname. Please check the data or re-login.'
        let useHtml = false

        if (error.response?.data?.errors?.message) {
            const messages = error.response.data.errors.message
            const formattedErrors = Object.entries(messages)
                .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                .join('<br>')

            if (formattedErrors) {
                errorMessage = formattedErrors
                useHtml = true
            }
        }

        Swal.fire({
            title: 'Failed!',
            html: useHtml ? errorMessage : undefined,
            text: useHtml ? undefined : errorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
        })
    }
}

onMounted(async () => {
    await fetchProducts()

    try {
        initAudioContext()
        console.log('Audio context pre-initialized')
    } catch (error) {
        console.error('Pre-initialization error:', error)
    }

    const initAudioOnInteraction = () => {
        if (!isAudioInitialized.value) {
            initializeAudio()
        }
    }

    document.addEventListener('click', initAudioOnInteraction, { once: true })
    document.addEventListener('touchstart', initAudioOnInteraction, { once: true })
    document.addEventListener('keydown', initAudioOnInteraction, { once: true })
})

onBeforeUnmount(() => {
    stopScanner()

    if (audioContext && audioContext.state !== 'closed') {
        audioContext.close()
        audioContext = null
    }

    audioPool.forEach(audio => {
        if (audio.src && audio.src.startsWith('blob:')) {
            URL.revokeObjectURL(audio.src)
        }
        audio.pause()
        audio.src = ''
    })
    audioPool.length = 0

    isAudioInitialized.value = false
})
</script>

<style scoped>

#interactive.viewport {
    position: relative;
    width: 100%;
    height: 300px;
    overflow: hidden;
    border-radius: 8px;
    background: #000;
}

#interactive.viewport video {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    object-fit: cover;
}

#interactive.viewport canvas {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
}

.drawingBuffer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.laser {
    position: absolute;
    top: 50%;
    left: 10%;
    width: 80%;
    height: 2px;
    background: linear-gradient(90deg, transparent, red, transparent);
    transform: translateY(-50%);
    opacity: 0.7;
    animation: laser-sweep 2s ease-in-out infinite;
}

@keyframes laser-sweep {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.8; }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>

