<script setup>
import FileUploadCard from '@/Components/FileUploadCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps(['size']);

const sizeform = useForm({
    name: props?.size?.name ?? '',
    price: props?.size?.price ?? 0,
})

const formSubmit = () => {
    const isEdit = Boolean(props?.size);

    const routeLink = isEdit ? route('sizes.update', props.size.id) + '?_method=PUT' : route('sizes.store');

    sizeform.post(routeLink, {
        preserveScroll: true,
        onSuccess: () => {
            sizeform.reset();
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
}

</script>

<template>
    <Head title="Size" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Size</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <VCard>
                    <VCardText style="padding: 2rem;">
                        <form @submit.prevent="formSubmit" enctype='multipart/form-data'>
                            <VRow>
                                <VCol cols="12">
                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <InputLabel>Name</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="sizeform.name"
                                                required
                                            ></VTextField>
                                            <InputError class="my-2" :message="sizeform.errors.name" />
                                        </div>
                                    </div>
                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <InputLabel>Price</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="price"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="sizeform.price"
                                                required
                                            ></VTextField>
                                            <InputError class="my-2" :message="sizeform.errors.price" />
                                        </div>
                                    </div>
                                    <div class="d-flex justify-end">
                                        <VBtn type="submit" color="primary">{{ props?.size ? 'Update' : 'Create' }}</VBtn>
                                    </div>
                                </VCol>
                            </VRow>
                        </form>
                    </VCardText>
                </VCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
