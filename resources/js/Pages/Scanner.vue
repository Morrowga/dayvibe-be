<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Reactive variables
const selectedFile = ref(null);
const imagePreview = ref(null);
const isUploading = ref(false);
const isSave = ref(false);
const successMessage = ref(false);
const uploadError = ref('');
const uploadResult = ref([]);
const dragActive = ref(false);

// New variables for code search
const searchMode = ref('upload'); // 'upload' or 'find'
const codeNumber = ref('');
const isSearching = ref(false);

// File input reference
const fileInput = ref(null);

// Check for code parameter on component mount
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const codeParam = urlParams.get('code');

    if (codeParam && codeParam.trim() !== '') {
        searchMode.value = 'find';
        codeNumber.value = codeParam.trim();
        // Clear any previous results
        uploadResult.value = [];
        uploadError.value = '';
    }
});

// Switch between upload and find modes
const switchMode = (mode) => {
    searchMode.value = mode;
    clearForm();
};

// Handle file selection
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    processFile(file);
};

// Handle drag and drop
const handleDrop = (event) => {
    event.preventDefault();
    dragActive.value = false;

    const files = event.dataTransfer.files;
    if (files.length > 0) {
        processFile(files[0]);
    }
};

const handleDragOver = (event) => {
    event.preventDefault();
    dragActive.value = true;
};

const handleDragLeave = (event) => {
    event.preventDefault();
    dragActive.value = false;
};

// Process selected file
const processFile = (file) => {
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        uploadError.value = 'Please select a valid image file';
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        uploadError.value = 'File size must be less than 5MB';
        return;
    }

    selectedFile.value = file;
    uploadError.value = '';
    uploadResult.value = []; // Clear previous result

    // Create image preview
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

// Search by code number
const searchByCode = async () => {
    if (!codeNumber.value.trim()) {
        uploadError.value = 'Please enter a code number';
        return;
    }

    isSearching.value = true;
    uploadError.value = '';

    try {
        const formData = new FormData();
        formData.append('mode', 'find');
        formData.append('code', codeNumber.value.trim());

        const response = await axios.post('/api/scanner', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        uploadResult.value = response.data;
    } catch (error) {
        if (error.response) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                uploadError.value = errors.code ? errors.code[0] : (errors.general || 'Failed to find order');
            } else {
                uploadError.value = error.response.data.message || 'Order not found';
            }
        } else {
            uploadError.value = 'Network error occurred';
        }
        console.error('Search error:', error);
    } finally {
        isSearching.value = false;
    }
};

// Upload and process QR code using AXIOS
const uploadQRCode = async () => {
    if (!selectedFile.value) {
        uploadError.value = 'Please select an image file first';
        return;
    }

    isUploading.value = true;
    uploadError.value = '';

    try {
        const formData = new FormData();
        formData.append('mode', 'upload');
        formData.append('qr_image', selectedFile.value);

        const response = await axios.post('/api/scanner', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        uploadResult.value = response.data;
        console.log(response);
    } catch (error) {
        if (error.response) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                uploadError.value = errors.qr_image ? errors.qr_image[0] : (errors.general || 'Failed to process QR code');
            } else {
                uploadError.value = error.response.data.message || 'Failed to process QR code';
            }
        } else {
            uploadError.value = 'Network error occurred';
        }
        console.error('Upload error:', error);
    } finally {
        isUploading.value = false;
    }
};

const saveData = async () => {
    isSave.value = true;

    try {
        const formData = new FormData();
        formData.append('data', JSON.stringify(uploadResult.value.data));
        formData.append('code', uploadResult.value.code);

        const response = await axios.post('/api/qr-order', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        successMessage.value = 'Order is success';

        setTimeout(() => {
            clearForm()
        }, 3000);

        console.log(response);
    } catch (error) {
        console.error('Upload error:', error);
    } finally {
        isSave.value = false;
    }
};

// Clear form
const clearForm = () => {
    selectedFile.value = null;
    imagePreview.value = null;
    codeNumber.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
    uploadResult.value = [];
    successMessage.value = '';
    uploadError.value = '';
};

// Copy result to clipboard
const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        console.log('Copied to clipboard');
    });
};

// Trigger file input click
const triggerFileInput = () => {
    fileInput.value?.click();
};
</script>

<template>
    <Head title="QR Code Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">QR Code Management</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Mode Selection -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Action</h3>
                        <div class="flex space-x-4">
                            <button
                                @click="switchMode('upload')"
                                :class="[
                                    'px-6 py-3 rounded-lg font-medium transition duration-200',
                                    searchMode === 'upload'
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                            >
                                📷 Upload QR Code
                            </button>
                            <button
                                @click="switchMode('find')"
                                :class="[
                                    'px-6 py-3 rounded-lg font-medium transition duration-200',
                                    searchMode === 'find'
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                            >
                                🔍 Find by Code Number
                            </button>
                        </div>
                    </div>
                </div>

                <!-- QR Upload Section -->
                <div v-if="searchMode === 'upload'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upload QR Code Image</h3>

                        <!-- File Input (Hidden) -->
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            @change="handleFileSelect"
                            class="hidden"
                        />

                        <!-- Drop Zone -->
                        <div
                            @drop="handleDrop"
                            @dragover="handleDragOver"
                            @dragleave="handleDragLeave"
                            @click="triggerFileInput"
                            :class="[
                                'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors duration-200',
                                dragActive
                                    ? 'border-blue-400 bg-blue-50'
                                    : 'border-gray-300 hover:border-gray-400'
                            ]"
                        >
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-700 mb-2">
                                    {{ dragActive ? 'Drop your QR code image here' : 'Click to upload or drag and drop' }}
                                </p>
                                <p class="text-sm text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div v-if="imagePreview" class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Preview:</h4>
                            <div class="flex items-start space-x-4">
                                <img :src="imagePreview" alt="QR Code Preview" class="w-32 h-32 object-contain border rounded">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-2">
                                        <strong>File:</strong> {{ selectedFile?.name }}
                                    </p>
                                    <p class="text-sm text-gray-600 mb-4">
                                        <strong>Size:</strong> {{ Math.round(selectedFile?.size / 1024) }} KB
                                    </p>
                                    <div class="w-full md:w-1/2">
                                        <button
                                            @click="uploadQRCode"
                                            :disabled="isUploading"
                                            class="bg-blue-500 hover:bg-blue-700 w-full mt-2 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded transition duration-200"
                                        >
                                            {{ isUploading ? 'Processing...' : 'Process QR Code' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Overlay for Upload -->
                        <div v-if="isUploading" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-blue-700">Processing QR code...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code Search Section -->
                <div v-if="searchMode === 'find'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Find Order by Code Number</h3>

                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label for="codeNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                    Enter Code Number
                                </label>
                                <input
                                    id="codeNumber"
                                    v-model="codeNumber"
                                    type="text"
                                    placeholder="Enter order code number..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    @keyup.enter="searchByCode"
                                />
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="searchByCode"
                                    :disabled="isSearching || !codeNumber.trim()"
                                    class="bg-green-500 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded transition duration-200"
                                >
                                    {{ isSearching ? 'Searching...' : 'Search' }}
                                </button>
                            </div>
                        </div>

                        <!-- Loading Overlay for Search -->
                        <div v-if="isSearching" class="mt-4 p-4 bg-green-50 border border-green-200 rounded">
                            <div class="flex items-center space-x-2">
                                <svg class="animate-spin h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-green-700">Searching for order...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Display -->
                <div v-if="uploadError" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <strong>Error:</strong> {{ uploadError }}
                        </div>
                    </div>
                </div>

                <!-- Results Card - Dev Info -->
                <!-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="uploadResult && uploadResult.data">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code Dev</h3>
                        <div class="bg-gray-50 p-4 rounded border">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-1">Decoded Data:</p>
                                    <p class="font-mono text-sm bg-white p-2 rounded border break-all">{{ uploadResult }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Results Card - QR Data -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="uploadResult && uploadResult.data">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">QR Code Data</h3>
                        <div class="bg-gray-50 p-4 rounded border">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-1">Total Quantity:</p>
                                    <p class="font-mono text-sm bg-white p-2 rounded border break-all">{{ uploadResult?.data?.tq ?? 0 }}</p>
                                </div>
                                <div class="flex-1 mx-3">
                                    <p class="text-sm text-gray-600 mb-1">Total Amount:</p>
                                    <p class="font-mono text-sm bg-white p-2 rounded border break-all">{{ uploadResult?.data?.ta ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="uploadResult">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
                        <div class="bg-gray-50 p-4 rounded border">
                            <div class="flex justify-between items-start">
                                <VRow>
                                    <VCol cols="6" sm="12" lg="2" v-for="(item, index) in uploadResult.items" :key="index">
                                        <div style="height: 120px;">
                                            <img :src="item.img" style="width: 100px; height: auto;" alt="">
                                        </div>

                                        <p>Quantity - {{ item.quantity  }}</p>
                                    </VCol>
                                </VRow>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Process Section - Only show for upload mode and when order hasn't been processed -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="uploadResult && uploadResult.data && searchMode === 'upload'">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Process</h3>
                        <div class="bg-gray-50 p-4 rounded border">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-1">Serial Number:</p>
                                    <p class="font-mono text-sm bg-white p-2 rounded border break-all">{{ uploadResult.code }}</p>
                                    <p class="font-mono bg-white p-2 rounded border break-all mt-3" v-if="successMessage != ''" style="color: green !important;">{{ successMessage }}</p>

                                    <div class="w-full md:w-2/2">
                                        <button
                                            @click="saveData()"
                                            :disabled="isSave"
                                            class="bg-blue-500 hover:bg-blue-700 w-full mt-2 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded transition duration-200"
                                        >
                                            {{ isSave ? 'Processing...' : 'Process Order' }}
                                        </button>
                                        <button
                                            @click="clearForm"
                                            :disabled="isUploading || isSave"
                                            class="bg-gray-500 hover:bg-gray-700 w-full mt-2 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded transition duration-200"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Found Order Info - Only show for find mode -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="uploadResult && uploadResult.data && searchMode === 'find'">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Found Order Information</h3>
                        <div class="bg-green-50 p-4 rounded border border-green-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-1">Order Code:</p>
                                    <p class="font-mono text-sm bg-white p-2 rounded border break-all">{{ uploadResult.code }}</p>
                                    <p class="font-mono bg-green-100 p-2 rounded border break-all mt-3 text-green-800">
                                        ✅ Order already exists in the system
                                    </p>

                                    <div class="w-full md:w-2/2">
                                        <button
                                            @click="clearForm"
                                            class="bg-gray-500 hover:bg-gray-700 w-full mt-2 text-white font-bold py-2 px-4 rounded transition duration-200"
                                        >
                                            Clear Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
