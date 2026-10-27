<template>
  <AdminLayout title="Edit Role">
    <div class="space-y-6 max-w-4xl">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Edit Role: {{ role.name }}</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Update role properties and permission assignments.</p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('tyro-dashboard.roles.show', role.id)" class="btn btn-outline-info btn-sm">
            View Role
          </Link>
          <Link :href="route('tyro-dashboard.roles.index')" class="btn btn-outline-dark btn-sm">
            ← Back to Roles
          </Link>
        </div>
      </div>

      <div class="panel">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Role Name <span class="text-danger">*</span></label>
              <input
                v-model="form.name"
                type="text"
                class="form-input text-xs"
                required
              />
              <span v-if="form.errors.name" class="text-danger text-xs mt-1 block">{{ form.errors.name }}</span>
            </div>

            <div>
              <label class="form-label text-xs font-bold text-[#0e1726] dark:text-white-light">Role Slug</label>
              <input
                v-model="form.slug"
                type="text"
                class="form-input text-xs font-mono"
                :disabled="isProtected"
                :class="{ 'opacity-60 cursor-not-allowed': isProtected }"
              />
              <span v-if="isProtected" class="text-xs text-warning mt-1 block">Protected role slug cannot be modified.</span>
              <span v-if="form.errors.slug" class="text-danger text-xs mt-1 block">{{ form.errors.slug }}</span>
            </div>
          </div>

          <div>
            <label class="inline-flex items-center cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="form-checkbox text-primary" />
              <span class="text-xs font-semibold text-[#0e1726] dark:text-white ltr:ml-2 rtl:mr-2">Active</span>
            </label>
          </div>

          <!-- Privileges Selection -->
          <div class="border-t border-[#e0e6ed] dark:border-[#1b2e4b] pt-5">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-bold text-[#0e1726] dark:text-white">Assigned Privileges</h3>
              <div class="flex gap-2">
                <button type="button" @click="selectAllPrivileges" class="text-xs text-primary font-semibold hover:underline">Select All</button>
                <span class="text-gray-300">|</span>
                <button type="button" @click="deselectAllPrivileges" class="text-xs text-gray-500 hover:underline">Deselect All</button>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
              <div
                v-for="p in privileges"
                :key="p.id"
                class="border border-[#e0e6ed] dark:border-[#1b2e4b] rounded-lg p-3 hover:border-primary/50 transition bg-[#f9fafb] dark:bg-[#121e32]"
              >
                <label class="flex items-start gap-2.5 cursor-pointer">
                  <input
                    type="checkbox"
                    :value="p.id"
                    v-model="form.privileges"
                    class="form-checkbox text-primary mt-0.5"
                  />
                  <div>
                    <span class="text-xs font-bold text-[#0e1726] dark:text-white block">{{ p.name }}</span>
                    <span class="text-[11px] font-mono text-primary block">{{ p.slug }}</span>
                    <span v-if="p.description" class="text-[11px] text-gray-500 dark:text-gray-400 block mt-0.5">{{ p.description }}</span>
                  </div>
                </label>
              </div>
            </div>
            <span v-if="form.errors.privileges" class="text-danger text-xs mt-2 block">{{ form.errors.privileges }}</span>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
            <Link :href="route('tyro-dashboard.roles.index')" class="btn btn-outline-dark btn-sm">
              Cancel
            </Link>
            <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing">
              {{ form.processing ? 'Saving...' : 'Update Role' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  role: Object,
  privileges: {
    type: Array,
    default: () => [],
  },
  protectedRoles: {
    type: Array,
    default: () => [],
  },
});

const isProtected = computed(() => {
  return props.protectedRoles.includes(props.role.slug) || props.protectedRoles.includes(props.role.id);
});

const form = useForm({
  name: props.role.name || '',
  slug: props.role.slug || '',
  is_active: Boolean(props.role.is_active),
  privileges: (props.role.privileges || []).map((p) => p.id),
});

function selectAllPrivileges() {
  form.privileges = props.privileges.map((p) => p.id);
}

function deselectAllPrivileges() {
  form.privileges = [];
}

function submit() {
  form.put(route('tyro-dashboard.roles.update', props.role.id));
}
</script>
