<script setup>
import FileUploadCard from '@/Components/FileUploadCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps(['order']);

const orderForm = useForm({
    status: props?.order?.status ?? 'confirmed',
})

const formSubmit = (status) => {

    orderForm.status = status

    const routeLink = route('orders.update', props.order.id) + '?_method=PUT';

    orderForm.post(routeLink, {
        preserveScroll: true,
        onError: (error) => {
            console.error("Form submission error:", error);
        },
    });
}

</script>

<template>
    <Head title="Order" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <VCard>
                    <VCardText style="padding: 2rem;">
                        <form @submit.prevent="formSubmit" enctype='multipart/form-data'>
                            <VRow>
                                <VCol cols="4">
                                    <div class="d-flex justify-start">
                                        <p class="pt-2 mr-3">{{ props?.order?.name }}</p>
                                        <VChip :class="{
                                                'grey': props?.order?.status === 'order',
                                                'green': props?.order?.status === 'confirmed',
                                                'red': props?.order?.status === 'cancel'
                                            }"
                                            style="text-transform: capitalize;"
                                        >
                                            {{  props?.order?.status }}
                                        </VChip>
                                    </div>
                                    <div class="mt-2 font-bold">
                                        <p>Order No - <strong style="color: red;">{{  props?.order?.code  }}</strong></p>
                                    </div>
                                    <div class="mt-3" style="font-weight: bold;">
                                        <p class="my-2">{{ props?.order?.msisdn }}</p>
                                        <p>{{ props?.order?.address + ', ' + props?.order?.state + ', ' + props?.order.city }}</p>
                                    </div>
                                    <div class="mt-4">
                                        <p style="font-size: 1.5rem;">Total Quantity - <strong style="color: green;">{{  props?.order?.total_quantity  }}</strong></p>
                                        <p class="pt-4" style="font-size: 1.5rem;">Total Price - <strong style="color: green;">{{  props?.order?.total_price  }} MMK</strong></p>
                                    </div>
                                    <div class="mt-3" style="font-weight: bold;">
                                        <p>{{ props?.order?.content }}</p>
                                    </div>
                                </VCol>
                                <VCol cols="8">
                                   <VRow>
                                        <VCol v-for="item in props?.order?.order_items" :key="item.id" cols="4">
                                            <VCard>
                                                <VCardText style="font-weight: bold;">
                                                    <img :src="item?.store_product?.first_image" style="width: 100%; height: 20vh; object-fit: cover;" alt="">
                                                    <div class="pt-2">
                                                        <p>{{  item.store_product.name }}</p>
                                                    </div>
                                                    <div class="pt-2 d-flex justify-between">
                                                        <p>Stocks {{  item.store_product.stock }} Left</p>
                                                    </div>
                                                    <div class="d-flex justify-between">
                                                        <p>{{  item.price }} x {{ item.quantity }}</p>
                                                        <p>{{  item.price * item.quantity }} MMK</p>
                                                    </div>

                                                </VCardText>
                                            </VCard>
                                        </VCol>
                                   </VRow>

                                </VCol>
                            </VRow>
                            <div class="d-flex justify-end">
                                <VBtn @click="formSubmit('cancel')" color="red">Cancel</VBtn>
                                <VBtn @click="formSubmit('confirmed')" class="ml-3" color="primary">Confirmed</VBtn>
                            </div>
                        </form>
                    </VCardText>
                </VCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.grey {
  background-color: grey;
  color: white;
}

.green {
  background-color: green;
  color: white;
}

.red {
  background-color: red;
  color: white;
}
</style>
