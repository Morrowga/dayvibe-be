<script setup>
import FileUploadCard from '@/Components/FileUploadCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps(['brands', 'content']);

const previewUrl = ref(props?.content?.image_url ?? null);

const form = useForm({
    title: props?.content?.title ?? '',
    content: props?.content?.content ?? '',
    slug: props?.content?.slug ?? 'burmaworld-content',
    description: props?.content?.description ?? '',
    display_url: props?.content?.display_url,
    brand_id: props?.content?.brand_id ?? '',
    image: null
});

const fileSelected = (file) => {
    form.image = file
}

const previewChange = (url) => {
    previewUrl.value = url;
}

const formSubmit = () => {
    const isEdit = Boolean(props?.content);

    const routeLink = isEdit ? route('contents.update', props.content.id) + '?_method=PUT' : route('contents.store');

    form.post(routeLink, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            previewUrl.value =''
        },
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
}
</script>

<template>
    <Head title="News" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">News</h2>
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
                                            <InputLabel>Title</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="title"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.title"
                                                required
                                            ></VTextField>
                                        </div>
                                    </div>
                                    <InputError class="my-2" :message="form.errors.title" />

                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <InputLabel>Brand</InputLabel>
                                            <VSelect
                                                v-model="form.brand_id"
                                                id="brand_id"
                                                variant="outlined" density="compact" required hide-details
                                                :items="props?.brands"
                                                item-value="id"
                                                item-title="name"
                                            ></VSelect>
                                            <InputError class="my-2" :message="form.errors.brand_id" />
                                        </div>
                                    </div>

                                    <div class="d-flex justify-start mt-5">
                                        <div style="width: 100%;">
                                            <InputLabel>Display Url</InputLabel>
                                            <VTextField
                                                density="compact"
                                                variant="outlined"
                                                id="display_url"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.display_url"
                                                required
                                            ></VTextField>
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.content" />
                                </VCol>
                                <VCol cols="6" class="py-0">
                                    <FileUploadCard @fileSelected="fileSelected" @previwChange="previewChange" :previewUrl="previewUrl" />
                                </VCol>

                                <VCol cols="12" class="py-0 mt-5">
                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <VTextarea
                                                density="compact"
                                                variant="outlined"
                                                id="description"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.description"
                                                required
                                            ></VTextarea>
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.content" />
                                </VCol>
                                <VCol cols="12" class="py-0">
                                    <div class="d-flex justify-start">
                                        <div style="width: 100%;">
                                            <VTextarea
                                                density="compact"
                                                variant="outlined"
                                                id="content"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="form.content"
                                                required
                                            ></VTextarea>
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.content" />
                                </VCol>
                            </VRow>
                            <div class="my-3 d-flex justify-end">
                                <VBtn type="submit" color="primary">{{ props?.content ? 'Update' : 'Create' }}</VBtn>
                            </div>
                        </form>

                    </VCardText>
                </VCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
