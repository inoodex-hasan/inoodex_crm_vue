<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  video: Object,
})

const form = useForm({
  video_name: props.video.video_name || '',
  status: props.video.status || 'pending',
})

const submit = () => {
  form.put(`/admin/marketing/videos/${props.video.id}`)
}
</script>

<template>
  <AdminLayout title="Edit Video">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <h2 class="text-xl font-bold uppercase tracking-wide text-gray-800">Edit Video</h2>
      <Link href="/admin/marketing/videos" class="btn btn-outline-primary">Back to Videos</Link>
    </div>

    <div class="panel mt-6 max-w-2xl">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">Video Name *</label>
          <input type="text" v-model="form.video_name" class="form-input mt-1" required />
          <p v-if="form.errors.video_name" class="text-danger text-sm mt-1">{{ form.errors.video_name }}</p>
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Status *</label>
          <select v-model="form.status" class="form-select mt-1" required>
            <option value="pending">Pending</option>
            <option value="not_edited">Not Edited</option>
            <option value="editing">Editing</option>
            <option value="ready">Ready</option>
            <option value="uploaded">Uploaded</option>
          </select>
          <p v-if="form.errors.status" class="text-danger text-sm mt-1">{{ form.errors.status }}</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            Update Video
          </button>
          <Link href="/admin/marketing/videos" class="btn btn-outline-secondary">Cancel</Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
