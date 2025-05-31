<script setup>
import FileUploadCard from '@/Components/FileUploadCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps(['category', 'sizes']);


const makingSize = () => {
    return props?.category?.sizes?.map(size => ({
    ...size,
    formattedName: `${size.name} (${size.price}MMK)` // Add the formatted name
  }));
}

const form = useForm({
    name_en: props?.category?.name_en ?? '',
    name_mm: props?.category?.name_mm ?? '',
    sizes: makingSize() ?? [],
});

const sizeform = useForm({
    name: '',
    price: '',
})

const formSubmit = () => {
    const isEdit = Boolean(props?.category);

    const routeLink = isEdit ? route('categories.update', props.category.id) + '?_method=PUT' : route('categories.store');

    form.post(routeLink, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
}

const formattedSizes = computed(() => {
  return props?.sizes?.map(size => ({
    ...size,
    formattedName: `${size.name} (${size.price}MMK)` // Add the formatted name
  }));
});


</script>

<template>
    <Head title="Category" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Category</h2>
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
                                            <InputLabel>Name EN</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="name_en"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.name_en"
                                                required
                                            ></VTextField>
                                        </div>
                                    </div>
                                    <InputError class="my-2" :message="form.errors.name_en" />

                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <InputLabel>Name MM</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="name_mm"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.name_mm"
                                                required
                                            ></VTextField>
                                        </div>
                                    </div>
                                    <InputError class="my-2" :message="form.errors.name_mm" />

                                    <div v-if="form.has_size">
                                        <InputLabel>Size Tags</InputLabel>
                                        <div class="d-flex justify-start mt-3">
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
                                                <InputError class="my-2" :message="form.errors.sizes" />
                                            </div>
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
                                </VCol>
                            </VRow>
                            <div class="my-3 d-flex justify-end">
                                <VBtn type="submit" color="primary">{{ props?.category ? 'Update' : 'Create' }}</VBtn>
                            </div>
                        </form>

                    </VCardText>
                </VCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
