<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  poster: Object,
})

const form = useForm({
  poster_name: props.poster.poster_name || '',
  status: props.poster.status || 'pending',
})

const submit = () => {
  form.put(`/admin/marketing/posters/${props.poster.id}`)
}
</script>

<template>
  <AdminLayout title="Edit Poster">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <h2 class="text-xl font-bold uppercase tracking-wide text-gray-800">Edit Poster</h2>
      <Link href="/admin/marketing/posters" class="btn btn-outline-primary">Back to Posters</Link>
    </div>

    <div class="panel mt-6 max-w-2xl">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">Poster Name *</label>
          <input type="text" v-model="form.poster_name" class="form-input mt-1" required />
          <p v-if="form.errors.poster_name" class="text-danger text-sm mt-1">{{ form.errors.poster_name }}</p>
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Status *</label>
          <select v-model="form.status" class="form-select mt-1" required>
            <option value="pending">Pending</option>
            <option value="not_ready">Not Ready</option>
            <option value="designing">Designing</option>
            <option value="ready">Ready</option>
            <option value="uploaded">Uploaded</option>
          </select>
          <p v-if="form.errors.status" class="text-danger text-sm mt-1">{{ form.errors.status }}</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            Update Poster
          </button>
          <Link href="/admin/marketing/posters" class="btn btn-outline-secondary">Cancel</Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
