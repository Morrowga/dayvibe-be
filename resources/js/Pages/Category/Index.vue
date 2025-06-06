<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link ,Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, defineProps } from 'vue';
import Datatable from '@/Components/Datatable.vue';

const props = defineProps(['categories'])

const contentSearch = ref({
    enabled: true,
    trigger: 'enter',
    placeholder: 'Search this table',
})

const clientSidePaginationOptions = ref({
    enabled: true,
    mode: 'records',
    perPage: 10,
    position: 'bottom',
    perPageDropdown: [10, 20, 50,100,200],
    dropdownAllowAll: false,
    setCurrentPage: 1,
    nextLabel: 'next',
    prevLabel: 'prev',
    rowsPerPageLabel: 'Rows per page',
    ofLabel: 'of',
    pageLabel: 'page',
    allLabel: 'All',
})

const columns = ref([
    {
        label: 'Name MM',
        field: 'name_mm',
    },
    {
        label: 'Name EN',
        field: 'name_en',
    },
    {
        label: 'Manage',
        field: 'normal_action',
        sortable: false
    },
])

</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <FontAwesomeIcon class="text-gray-500 text-lg mr-2" icon="list" />
                Categories
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <VCard class="bg-white shadow-none rounded-lg">
                    <VCardText>
                        <Link :href="route('categories.create')" style="text-decoration: none;">
                            <PrimaryButton class="float-right">
                                <FontAwesomeIcon class="text-grey-500 mr-2" icon="folder-plus" />
                                Categories
                            </PrimaryButton>
                        </Link>
                    </VCardText>
                    <VCardText>
                        <div>
                            <Datatable :columns="columns" :paginationOptions="clientSidePaginationOptions" :rows="props.categories" :clientSideSearch="contentSearch" :action_url="'categories'" title="Category Deletion" />
                        </div>
                    </VCardText>
                </VCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
