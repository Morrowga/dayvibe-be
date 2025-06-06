<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  previewUrls: {
    type: Array,
    default: () => [], // Default to an empty array
  },
  media: {
    type: Array
  }
});

const previewUrls = ref([...props.previewUrls]); // Initialize with props value

const fileInput = ref(null);
const form = ref({
  images: []
});

const emit = defineEmits(["filesSelected", "previewsChange", "updateDeleteImages"]);

const triggerFileInput = () => {
  fileInput.value.click();
};

// Watch for changes in props and update previewUrls accordingly
watch(
  () => props.previewUrls,
  (urls) => {
    previewUrls.value = [...urls];
  }
);

const onFileChange = (event) => {
  const files = event.target.files;
  if (files.length) {
    const newPreviews = Array.from(files).map((file) => ({
      file,
      url: URL.createObjectURL(file),
    }));
    previewUrls.value = [...previewUrls.value, ...newPreviews];

    // Add the files to form.images
    form.value.images = [...form.value.images, ...Array.from(files)];

    emit("filesSelected", Array.from(files));
    emit("previewsChange", previewUrls.value);
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
  const files = event.dataTransfer.files;
  if (files.length) {
    const newPreviews = Array.from(files).map((file) => ({
      file,
      url: URL.createObjectURL(file),
    }));
    previewUrls.value = [...previewUrls.value, ...newPreviews];
    form.value.images = [...form.value.images, ...Array.from(files)];

    emit("filesSelected", Array.from(files));
    emit("previewsChange", previewUrls.value);
  }
};

// Remove a preview and corresponding file from the arrays
const removePreview = (index, url) => {
  const removedPreview = previewUrls.value.splice(index, 1)[0]; // Get the removed preview
  emit("previewsChange", previewUrls.value); // Emit updated previewUrls

  // Handle the file removal from form.images
  const removedFile = removedPreview.file;
  form.value.images = form.value.images.filter((file) => file !== removedFile); // Remove file from form.images
  emit("filesSelected", form.value.images); // Emit updated files array

  let removImg = props?.media.find(item => item.original_url === url);

  emit('updateDeleteImages', removImg.id)
};
</script>

<template>
  <div class="file-upload-card">
    <div
      class="upload-area"
      @dragover.prevent="onDragOver"
      @dragleave.prevent="onDragLeave"
      @drop.prevent="onDrop"
    >
      <!-- Upload Icon in Top-Right Corner -->
      <div class="upload-icon" @click="triggerFileInput">
        <v-icon>mdi-upload</v-icon>
      </div>

      <input
        type="file"
        ref="fileInput"
        accept="image/*"
        class="file-input"
        @change="onFileChange"
        multiple
      />

      <div class="icon-wrapper" v-if="!previewUrls.length">
        <p>Drag and drop images or click the upload icon</p>
      </div>

      <div class="image-preview-container" v-else>
        <div
          v-for="(preview, index) in previewUrls"
          :key="index"
          class="image-preview"
        >
          <img :src="preview.url" alt="Uploaded Image" />
          <div class="remove-icon" @click="removePreview(index, preview.url)">
            <v-icon>mdi-delete</v-icon>
          </div>
        </div>
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
  padding: 10px;
  width: 100%; /* Ensure a fixed size for the card */
  height: 300px;
  text-align: center;
  position: relative;
  cursor: pointer;
  transition: border-color 0.3s ease;
  overflow: auto; /* Ensures the previews fit within the card area */
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

.upload-icon {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: rgba(0, 0, 0, 0.3);
  padding: 10px;
  border-radius: 50%;
  cursor: pointer;
  color: white;
}

.icon-wrapper v-icon {
  font-size: 40px;
}

.image-preview-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.image-preview {
  width: 200px;
  height: 200px;
  overflow: hidden;
  border-radius: 10px;
  flex-shrink: 0;
  position: relative;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-icon {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
  padding: 5px;
  border-radius: 50%;
  cursor: pointer;
}

.remove-icon v-icon {
  font-size: 16px;
}
</style>
