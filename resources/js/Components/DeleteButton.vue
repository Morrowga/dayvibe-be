<script setup>
import { router } from '@inertiajs/core';
import { ref, defineProps, computed, onMounted } from 'vue';
import { Link,useForm,usePage } from '@inertiajs/vue3';

const props = defineProps({
    deleteId : {
        required: true
    },
    deleteUrl: {
        type: String,
        required: true
    },
    title:
    {
        type: String,
        required: true
    }
})

const dialog = ref(false)

const deleteItem = () => {
    router.delete(props.deleteUrl + '/' + props.deleteId, {
        onSuccess: () => {
            dialog.value = false
        },
    });
}

</script>
<template>
    <button class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" @click="dialog = true">
        <slot />
    </button>


    <VDialog
        v-model="dialog"
        width="auto"
      >
        <VCard
          class="px-5 py-5"
          max-width="400"
          :text="'This '+ props.title +' will forever delete from System. Are you sure with this ?'"
          :title="props.title"
        >
          <template v-slot:actions>
                <div class="ms-auto">
                    <VBtn
                    class="mt-2"
                    text="Cancel"
                    @click="dialog = false"
                    >
                    </VBtn>
                    <VBtn
                        @click="deleteItem"
                        class="bg-red mt-2"
                        text="Yes, I'm confirm."
                        >
                    </VBtn>
                </div>
          </template>
        </VCard>
    </VDialog>
  </template>
