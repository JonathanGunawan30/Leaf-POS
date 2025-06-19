<template>
    <div v-show="show" class="fixed inset-0 overflow-hidden z-50 transition-opacity duration-300">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" @click="closePopup"></div>

    <!-- Popup Panel -->
        <div class="absolute inset-y-0 right-0 max-w-[750px] w-full bg-white shadow-xl transform transition-transform duration-300 ease-in-out"
             :class="{ 'translate-x-0': show, 'translate-x-full': !show }">

      <!-- Header -->
      <div class="flex justify-between items-center p-5 border-b border-gray-300">
        <span class="text-black font-bold">EDIT PRODUCT</span>
        <button @click="closePopup" class="text-black hover:text-gray-700">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.0006 8.82208L14.1255 4.69727L15.304 5.87577L11.1791 10.0006L15.304 14.1253L14.1255 15.3038L10.0006 11.1791L5.87584 15.3038L4.69733 14.1253L8.82213 10.0006L4.69733 5.87577L5.87584 4.69727L10.0006 8.82208Z" fill="black"/>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-5 overflow-y-auto h-[calc(100%-120px)]">
        <form @submit.prevent="updateProduct">
          <!-- Upload Product Photos -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Upload Product Photo</p>
            <div class="border border-dashed border-gray-400 rounded-lg p-4 flex justify-center items-center">
              <div v-if="imagePreview" class="relative">
                <img :src="imagePreview" alt="Product Preview" class="h-[150px] object-contain" />
                <button
                  @click.prevent="clearImage"
                  class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 transform translate-x-1/2 -translate-y-1/2"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6l12 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
              <label v-else class="cursor-pointer text-center">
                <input
                  type="file"
                  class="hidden"
                  accept="image/jpeg,image/jpg,image/png"
                  @change="handleImageUpload"
                />
                <div class="text-center">
                  <svg width="66" height="59" viewBox="0 0 66 59" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-2">
                    <path d="M30.771 27.1902C32.0351 25.984 34.0339 25.9938 35.269 27.2322L44.894 36.8572C46.1481 38.1116 46.1482 40.139 44.894 41.3933C44.2684 42.0189 43.4468 42.3337 42.6255 42.3337C41.8042 42.3337 40.9826 42.0189 40.3569 41.3933L36.2085 37.2449V55.1667C36.2085 56.941 34.7715 58.3747 33.0005 58.3748C31.2295 58.3748 29.7925 56.941 29.7925 55.1667V37.0593L25.605 41.1013C24.3313 42.3364 22.3008 42.2948 21.0688 41.0212C19.8368 39.7443 19.8723 37.7161 21.146 36.4841L30.771 27.1902ZM32.9995 0.624756C41.2802 0.624756 48.5767 5.95365 51.2075 13.6023C59.0325 14.6644 65.0835 21.3892 65.0835 29.4998C65.0835 33.4171 63.6586 37.1868 61.0728 40.116C60.4375 40.8314 59.5552 41.2009 58.6665 41.2009C57.9125 41.2009 57.1553 40.9372 56.5425 40.3982C55.2176 39.2207 55.0891 37.1964 56.2632 35.865C57.8128 34.1132 58.6665 31.8483 58.6665 29.4998C58.6665 24.1932 54.3481 19.8748 49.0415 19.8748H48.7202C47.1933 19.8745 45.8783 18.7967 45.5767 17.2986C44.3767 11.3568 39.0889 7.04175 32.9995 7.04175C26.9134 7.0419 21.623 11.3569 20.4263 17.2986C20.1246 18.7968 18.8059 19.8748 17.2788 19.8748H16.9585C11.6519 19.8748 7.3335 24.1932 7.3335 29.4998C7.3335 31.8483 8.18691 34.1132 9.73975 35.865C10.9106 37.1963 10.7854 39.2207 9.45752 40.3982C8.12927 41.5724 6.10122 41.441 4.93018 40.116C2.34109 37.1868 0.916504 33.4171 0.916504 29.4998C0.916505 21.3891 6.96738 14.6643 14.7925 13.6023C17.4265 5.9538 24.7222 0.624896 32.9995 0.624756Z" fill="#2F8451"/>
                  </svg>
                  <div class="font-bold">
                    <span class="text-black">Drag & Drop your</span> <br>
                    <span class="text-green-700"> image </span>
                    <span class="text-black">or</span>
                    <span class="text-green-700"> file</span>
                  </div>
                  <div class="text-sm">
                    <span class="text-black">or</span>
                    <span class="text-green-700"> browse file </span>
                    <span class="text-black">on your computer</span>
                  </div>
                </div>
              </label>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mt-1">
              <p>Supported Formats: PNG, JPG, JPEG</p>
              <p>Maximum Size: 1 MB</p>
            </div>
          </div>

          <!-- Product Name -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Product Name <span class="text-red-500">*</span></p>
            <input
              v-model="productForm.name"
              type="text"
              class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
              required
            />
          </div>

          <!-- Category -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Category <span class="text-red-500">*</span></p>
            <select
              v-model="productForm.category_id"
              class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg appearance-none"
              required
            >
              <option value="" disabled>Select category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- Stock and Stock Alert -->
          <div class="flex gap-4 mb-4">
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Stock <span class="text-red-500">*</span></p>
              <input
                v-model="productForm.stock"
                type="number"
                min="0"
                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                required
              />
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Stock Alert</p>
              <input
                v-model="productForm.stock_alert"
                type="number"
                min="0"
                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
              />
            </div>
          </div>

          <!-- Barcode -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Barcode</p>
            <input
              v-model="productForm.barcode"
              type="text"
              class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
              @input="onBarcodeInput"
            />
          </div>

          <!-- Selling Price and Purchase Price -->
          <div class="flex gap-4 mb-4">
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Selling Price <span class="text-red-500">*</span></p>
              <input
                v-model="productForm.selling_price"
                type="text"
                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                @input="(e) => handleCurrencyInput(e, 'selling_price')"
                required
              />
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Purchase Price <span class="text-red-500">*</span></p>
              <input
                v-model="productForm.purchase_price"
                type="text"
                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                @input="(e) => handleCurrencyInput(e, 'purchase_price')"
                required
              />
            </div>
          </div>

          <!-- SKU -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">SKU</p>
            <input
              v-model="productForm.sku"
              type="text"
              class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>

          <!-- Discount -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Discount</p>
            <div class="relative">
              <input
                v-model="productForm.discount"
                type="number"
                min="0"
                max="100"
                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg pr-8"
                @input="preventNegativeValues('discount')"
              />
              <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
            </div>
          </div>

          <!-- Brand -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Brand</p>
            <input
              v-model="productForm.brand"
              type="text"
              class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>

          <!-- Description -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Description</p>
            <textarea
              v-model="productForm.description"
              class="w-full h-[136px] px-4 py-2 border border-gray-300 rounded-lg resize-none"
            ></textarea>
          </div>

          <!-- Unit, Weight, Volume -->
          <div class="flex gap-4 mb-4">
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Unit ID <span class="text-red-500">*</span></p>
              <select
                v-model="productForm.unit_id"
                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg appearance-none"
                required
              >
                <option value="" disabled>Select unit</option>
                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                  {{ unit.name }}
                </option>
              </select>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Weight (KG)</p>
              <div class="relative">
                <input
                  v-model="productForm.weight"
                  type="number"
                  min="0"
                  class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg pr-10"
                  @input="preventNegativeValues('weight')"
                />
                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">KG</span>
              </div>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium mb-1">Volume (M³)</p>
              <div class="relative">
                <input
                  v-model="productForm.volume"
                  type="number"
                  min="0"
                  class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg pr-10"
                  @input="preventNegativeValues('volume')"
                />
                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">M³</span>
              </div>
            </div>
          </div>

          <!-- Tax -->
          <div class="mb-4">
            <p class="text-sm font-medium mb-1">Tax <span class="text-red-500">*</span></p>
            <select
              v-model="productForm.tax_id"
              class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg appearance-none"
              required
            >
              <option value="" disabled>Select tax rate</option>
              <option v-for="tax in taxRates" :key="tax.id" :value="tax.id">
                {{ tax.name }} ({{ tax.rate }}%)
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
            @click="updateProduct"
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
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
  show: Boolean,
  productId: Number
});

const emit = defineEmits(['close', 'updated']);

const productForm = ref({
  name: '',
  description: '',
  category_id: '',
  unit_id: '',
  barcode: '',
  weight: '',
  volume: '',
  selling_price: '',
  purchase_price: '',
  stock: '',
  stock_alert: '',
  sku: '',
  discount: '',
  brand: '',
  tax_id: ''
});

const categories = ref([]);
const units = ref([]);
const taxRates = ref([]);
const imageFile = ref(null);
const imagePreview = ref(null);
const imageRemoved = ref(false);
const loading = ref(false);

const fetchData = async () => {
  try {
    const token = localStorage.getItem('X-API-TOKEN');
    if (!token) return;

    const [categoriesRes, unitsRes, taxesRes] = await Promise.all([
      axios.get('/api/categories', { headers: { Authorization: `Bearer ${token}` } }),
      axios.get('/api/units', { headers: { Authorization: `Bearer ${token}` } }),
      axios.get('/api/taxes', { headers: { Authorization: `Bearer ${token}` } })
    ]);

    categories.value = categoriesRes.data.data;
    units.value = unitsRes.data.data;
    taxRates.value = taxesRes.data.data;
  } catch (error) {
    console.error('Error fetching data:', error);
  }
};

watch(() => props.productId, async (newId) => {
  if (newId && props.show) {
    await fetchProductData(newId);
  }
}, { immediate: true });

watch(() => props.show, async (newShow) => {
  if (newShow && props.productId) {
    await fetchProductData(props.productId);
  }
});

const fetchProductData = async (id) => {
  if (!id) return;

  try {
    loading.value = true;
    const token = localStorage.getItem('X-API-TOKEN');
    if (!token) return;

    const response = await axios.get(`/api/products/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    const product = response.data.data;

    const originalSellingPrice = parseFloat(product.selling_price);
    const originalPurchasePrice = parseFloat(product.purchase_price);
    const formattedSellingPrice = formatCurrency(originalSellingPrice);
    const formattedPurchasePrice = formatCurrency(originalPurchasePrice);

    let taxId = '';
    if (product.taxes && product.taxes.length > 0) {
      taxId = product.taxes[0].id;
    }

    productForm.value = {
      name: product.name || '',
      description: product.description || '',
      category_id: product.category_id || '',
      unit_id: product.unit_id || '',
      barcode: product.barcode || '',
      weight: product.weight || '',
      volume: product.volume || '',
      selling_price: formattedSellingPrice,
      purchase_price: formattedPurchasePrice,
      original_selling_price: originalSellingPrice,
      original_purchase_price: originalPurchasePrice,
      stock: product.stock || '',
      stock_alert: product.stock_alert || '',
      sku: product.sku || '',
      discount: product.discount || '',
      brand: product.brand || '',
      tax_id: taxId
    };

    if (product.images) {
      imagePreview.value = product.images;
    }
  } catch (error) {
    console.error('Error fetching product:', error);
    Swal.fire({
      title: 'Error',
      text: 'Failed to load product data',
      icon: 'error'
    });
  } finally {
    loading.value = false;
  }
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
      Swal.fire({
        title: 'Invalid File Type',
        text: 'Please select a JPG, JPEG, or PNG image file.',
        icon: 'error'
      });
      event.target.value = '';
      return;
    }

    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};
const clearImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  imageRemoved.value = true;
};

const formatCurrency = (value) => {
    if (!value) return '';
    return value.toString();
};

const handleCurrencyInput = (event, field) => {
  productForm.value[field] = event.target.value;

  if (field === 'selling_price') {
    productForm.value.original_selling_price = parseFloat(event.target.value) || 0;
  } else if (field === 'purchase_price') {
    productForm.value.original_purchase_price = parseFloat(event.target.value) || 0;
  }
};

const onBarcodeInput = (event) => {
  productForm.value.barcode = event.target.value.replace(/\D/g, '').slice(0, 13);
};

const preventNegativeValues = (field) => {
  if (productForm.value[field] < 0) {
    productForm.value[field] = 0;
  }
};

const updateProduct = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem('X-API-TOKEN');
    if (!token) return;

    const formData = new FormData();
    formData.append('name', productForm.value.name);
    formData.append('description', productForm.value.description);
    formData.append('category_id', productForm.value.category_id);
    formData.append('unit_id', productForm.value.unit_id);
    formData.append('barcode', productForm.value.barcode);
    formData.append('weight', productForm.value.weight);
    formData.append('volume', productForm.value.volume);

    if (productForm.value.original_selling_price !== undefined) {
      formData.append('selling_price', parseFloat(productForm.value.original_selling_price));
    } else {
      formData.append('selling_price', parseFloat(productForm.value.selling_price) || 0);
    }

    if (productForm.value.original_purchase_price !== undefined) {
      formData.append('purchase_price', parseFloat(productForm.value.original_purchase_price));
    } else {
      formData.append('purchase_price', parseFloat(productForm.value.purchase_price) || 0);
    }

    formData.append('stock', parseInt(productForm.value.stock) || 0);
    formData.append('stock_alert', parseInt(productForm.value.stock_alert) || 0);
    formData.append('sku', productForm.value.sku);
    formData.append('discount', productForm.value.discount || 0);
    formData.append('brand', productForm.value.brand);
    formData.append('taxes[]', parseInt(productForm.value.tax_id));

    if (imageFile.value) {
      formData.append('images', imageFile.value);
    } else if (imageRemoved.value) {
      formData.append('remove_image', '1');
    }

    const response = await axios.post(`/api/products/${props.productId}`, formData, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
        'X-HTTP-Method-Override': 'PATCH'
      }
    });

    Swal.fire({
      title: 'Success',
      text: 'Product has been updated successfully',
      icon: 'success'
    });

    emit('updated');
    closePopup();
  } catch (error) {
    console.error('Error updating product:', error);

    let errorMessage = 'Failed to update product. Please check the data or re-login.';
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

onMounted(() => {
  fetchData();
});
</script>
