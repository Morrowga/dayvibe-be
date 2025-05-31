<script setup>
import MultipleFileUploadCard from '@/Components/MultipleFileUploadCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps(['store_product', 'categories', 'sizes']);

console.log(props?.store_product?.image_urls);

const previewUrls = ref(props?.store_product?.image_urls?.length > 0 ? props?.store_product?.image_urls : []);

const form = useForm({
    name: props?.store_product?.name ?? '',
    price: props?.store_product?.price ?? '700',
    stock: props?.store_product?.stock ?? 99999,
    active: props?.store_product?.active ?? true,
    category_id: props?.store_product?.category_id ?? '',
    has_size: props?.store_product?.has_size ?? false,
    delete_images: [],
    images: null
});

console.log(props?.store_product)

const filesSelected = (files) => {
    form.images = files
    console.log(form.images)
}

const previewChange = (urls) => {
    previewUrls.value = urls;
}

const formSubmit = () => {
    const isEdit = Boolean(props?.store_product);

    const routeLink = isEdit ? route('store-products.update', props.store_product.id) + '?_method=PUT' : route('store-products.store');

    form.post(routeLink, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if(!isEdit) {
                previewUrls.value = [];
            }
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
}

const updateDeleteImages = (id) => {
    form.delete_images.push(id)

    console.log(form.delete_images);
}


</script>

<template>
    <Head title="Store Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Store Product</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <VCard>
                    <VCardText style="padding: 2rem;">
                        <form @submit.prevent="formSubmit" enctype='multipart/form-data'>
                            <VRow>
                                <VCol cols="6" class="py-0">
                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <InputLabel>Name</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.name"
                                                required
                                            ></VTextField>
                                        </div>
                                    </div>
                                    <InputError class="my-2" :message="form.errors.name" />

                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <div style="width: 100%;">
                                                <InputLabel>Price</InputLabel>
                                                <VTextField
                                                    density="compact"
                                                    variant="outlined"
                                                    id="price"
                                                    type="number"
                                                    class="mt-1 block w-full"
                                                    v-model="form.price"
                                                    required
                                                ></VTextField>
                                            </div>
                                            <InputError class="my-2" :message="form.errors.price" />
                                        </div>
                                    </div>

                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <InputLabel>Stock</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="stock"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="form.stock"
                                                required
                                            ></VTextField>
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.stock" />
                                </VCol>
                                <VCol cols="6">
                                    <InputError class="my-2" :message="form.errors.title" />

                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <VSelect
                                                v-model="form.category_id"
                                                id="sizes"
                                                class="mt-3"
                                                variant="outlined" density="compact" required hide-details
                                                :items="props?.categories"
                                                item-value="id"
                                                item-title="name_en"
                                            ></VSelect>
                                            <InputError class="my-2" :message="form.errors.category_id" />
                                        </div>
                                    </div>
                                    <div>
                                        <v-checkbox
                                            v-model="form.has_size"
                                            label="Size"
                                            :false-value="false"
                                            :true-value="true"
                                        ></v-checkbox>
                                    </div>
                                    <!-- <div class="d-flex justify-start mt-11">
                                        <div style="width: 100%;">
                                            <v-select
                                                v-model="form.sizes"
                                                :items="formattedSizes"
                                                hint="Select Size"
                                                variant="outlined" density="compact" required hide-details
                                                item-value="id"
                                                item-title="formattedName"
                                                multiple
                                                persistent-hint
                                            ></v-select>
                                            <InputError class="my-2" :message="form.errors.category_id" />
                                        </div>
                                    </div> -->
                                    <div class="d-flex justify-start">
                                        <div>
                                            <v-checkbox
                                                v-model="form.active"
                                                label="Active"
                                                :false-value="false"
                                                :true-value="true"
                                            ></v-checkbox>
                                        </div>

                                    </div>
                                </VCol>
                                <VCol cols="12" class="py-0">
                                    <MultipleFileUploadCard @updateDeleteImages="updateDeleteImages" @filesSelected="filesSelected" :media="props?.store_product?.media" @previwChange="previewChange" :previewUrls="previewUrls" />
                                </VCol>
                            </VRow>
                            <div class="my-5 d-flex justify-end">
                                <VBtn type="submit" color="primary">{{ props?.store_product ? 'Update' : 'Create' }}</VBtn>
                            </div>
                        </form>

                    </VCardText>
                </VCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
