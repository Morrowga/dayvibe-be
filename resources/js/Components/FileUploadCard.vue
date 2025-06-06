<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    previewUrl: {
        type: String
    }
})

const previewUrl = ref(props?.previewUrl);
const fileInput = ref(null);

const triggerFileInput = () => {
  fileInput.value.click();
};

watch(() => props?.previewUrl, (url) => {
  previewUrl.value = url;
});

const emit = defineEmits(['fileSelected', 'previewChange'])

const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    previewUrl.value = URL.createObjectURL(file);
    emit("fileSelected", file);
    emit('previewChange', previewUrl.value)
  }
};

const onDragOver = (event) => {
  event.currentTarget.classList.add("drag-over");
};

const onDragLeave = (event) => {
  event.currentTarget.classList.remove("drag-over");
};

const onDrop = (event) => {
  event.currentTarget.classList.remove("drag-over");
  const file = event.dataTransfer.files[0];
  if (file) {
    previewUrl.value = URL.createObjectURL(file);
    emit("file-selected", file); // Emit the file to parent if needed
  }
};
</script>


<template>
    <div class="file-upload-card">
      <div
        class="upload-area"
        :class="{ 'has-image': previewUrl }"
        @dragover.prevent="onDragOver"
        @dragleave.prevent="onDragLeave"
        @drop.prevent="onDrop"
        @click="triggerFileInput"
      >
        <input
          type="file"
          ref="fileInput"
          accept="image/*"
          class="file-input"
          @change="onFileChange"
        />
        <div v-if="!previewUrl" class="icon-wrapper">
          <v-icon>mdi-upload</v-icon>
          <p>Drag and drop an image or click to upload</p>
        </div>
        <div v-else class="image-preview">
          <img :src="previewUrl" alt="Uploaded Image" />
        </div>
      </div>
    </div>
  </template>

<style scoped>
.file-upload-card {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.upload-area {
  border: 2px dashed #ccc;
  border-radius: 10px;
  padding: 0;
  width: 100%; /* Ensure a fixed size for the card */
  height: 300px;
  text-align: center;
  position: relative;
  cursor: pointer;
  transition: border-color 0.3s ease;
  overflow: hidden; /* Ensures the image fits within the card area */
}

.upload-area.drag-over {
  border-color: #007bff;
}

.file-input {
  display: none;
}

.icon-wrapper {
  color: #aaa;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.icon-wrapper v-icon {
  font-size: 40px;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Ensures the image is scaled and cropped to fit the card */
  border-radius: 10px;
}
</style>
