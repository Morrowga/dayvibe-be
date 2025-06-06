<script setup>
import {ref, defineProps, onMounted } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Link ,Head } from '@inertiajs/vue3';

const props = defineProps({
    columns:
    {
        type: Object,
        required: true
    },
    rows:
    {
        type: Object,
        required: true
    },
    paginationOptions:
    {
        type: Object,
        required: true
    },
    clientSideSearch:
    {
        type: Object,
        required: false
    },
    action_url:
    {
        type: String,
        required: false
    },
    title:
    {
        type: String,
        required: false
    }
})

const stringLimit = (str, limit) => {
    return str.length > limit ? str.substring(0, limit) + '...' : str;
}

</script>
<template>
    <vue-good-table
    class="mt-5 data-table"
    styleClass="vgt-table"
    :columns="props.columns"
    :pagination-options="props.paginationOptions"
    :search-options="props.clientSideSearch"
    :rows="props.rows" >
    <template #table-row="dataProps">
        <div v-if="dataProps.column.field == 'image_url'">
            <img class="rounded-lg h-[12vh] w-[10vh]" :src="dataProps.row.image_url">
        </div>
        <div v-if="dataProps.column.field == 'image_urls' && dataProps.row.image_urls.length > 0">
            <img class="rounded-lg h-[12vh] w-[15vh]" :src="dataProps.row.image_urls[0].url">
        </div>
        <div v-if="dataProps.column.field == 'status'">
            <VChip :class="{
                    'grey': dataProps.row.status === 'order',
                    'green': dataProps.row.status === 'confirmed',
                    'red': dataProps.row.status === 'cancel'
                }"
                style="text-transform: capitalize;"
            >
                {{  dataProps.row.status }}
            </VChip>
        </div>
        <div v-if="dataProps.column.field == 'normal_action'">
            <Link style="text-decoration: none;" :href="route(props.action_url + '.edit', dataProps.row.id)">
                <PrimaryButton class="mx-2">
                    <FontAwesomeIcon class="text-white-500" icon="scissors" />
                </PrimaryButton>
            </Link>
            <DeleteButton :deleteId="dataProps.row.id" :deleteUrl="props.action_url" :title="props.title">
                <FontAwesomeIcon class="text-white" icon="trash-can" />
            </DeleteButton>
        </div>
        <div v-if="dataProps.column.field == 'title'">
            <span>{{ stringLimit(dataProps.row.title, 20) }}</span>
        </div>
        <div v-if="dataProps.column.field == 'has_size'">
            <span>{{ dataProps.row.has_size ? 'Yes' : 'No' }}</span>
        </div>
        <div v-if="dataProps.column.field == 'description'">
            <span>{{ stringLimit(dataProps.row.description, 20) }}</span>
        </div>
        <div v-if="dataProps.column.field == 'created_at'">
            <span>{{ formatOrderDate(dataProps.row.created_at) }}</span>
        </div>
    </template>
    </vue-good-table>
</template>

<style>
.data-table table.vgt-table {
    background-color: #f5f5f7;
    border-color: rgb(#f5f5f7);
}
.data-table table.vgt-table td {
    color: #000;
}
.data-table table.vgt-table thead {
    color: #f5f5f7 !important;
}

.table thead th.vgt-checkbox-col {
    background: #f5f5f7 !important;
}

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
