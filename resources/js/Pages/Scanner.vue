<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Reactive variables - keeping only what's needed for find mode
const isUploading = ref(false);
const isSave = ref(false);
const successMessage = ref(false);
const uploadError = ref('');
const uploadResult = ref([]);

// Variables for code search
const searchMode = ref('upload'); // 'upload' or 'find'
const codeNumber = ref('');
const isSearching = ref(false);

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
    codeNumber.value = '';
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

                <!-- QR Upload Section - Empty Content -->
                <div v-if="searchMode === 'upload'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upload QR Code Image</h3>

                        <!-- Empty content area -->
                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-12 text-center">
                            <div class="text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <p class="text-lg">Upload functionality temporarily disabled</p>
                                <p class="text-sm mt-2">Please use "Find by Code Number" instead</p>
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
