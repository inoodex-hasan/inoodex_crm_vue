<template>
  <AdminLayout title="Roles & Permissions">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Roles & Permissions</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Configure access levels, privileges, and system roles.</p>
        </div>
        <Link
          :href="route('tyro-dashboard.roles.create')"
          class="btn btn-primary gap-2 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add Role
        </Link>
      </div>

      <div class="panel">
        <!-- Search Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applySearch" class="flex flex-wrap items-center gap-2.5">
            <div class="relative w-60 shrink-0">
              <input
                v-model="search"
                type="text"
                placeholder="Search roles..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2 w-full"
              />
              <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8" stroke-width="2"/>
                  <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                </svg>
              </span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3 shrink-0">Filter</button>
              <button @click="resetSearch" type="button" class="btn btn-outline-dark btn-sm text-xs py-2 px-3 shrink-0">Reset</button>
            </div>
          </form>
        </div>

        <!-- Roles Table -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Role Name</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Slug</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Users</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Privileges</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="role in roles.data" :key="role.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4 font-bold text-[#0e1726] dark:text-white text-sm">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" :class="role.is_active ? 'bg-success' : 'bg-gray-300'"></span>
                    <Link :href="route('tyro-dashboard.roles.show', role.id)" class="hover:text-primary transition">
                      {{ role.name }}
                    </Link>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs bg-slate-100 dark:bg-dark-light/20 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">
                    {{ role.slug }}
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <button
                    type="button"
                    @click="toggleRoleStatus(role)"
                    :class="role.is_active ? 'badge bg-success/20 text-success hover:bg-success/30' : 'badge bg-danger/20 text-danger hover:bg-danger/30'"
                    class="rounded-full px-3 py-1 font-bold text-xs uppercase tracking-wide transition cursor-pointer"
                    title="Click to toggle status"
                  >
                    {{ role.is_active ? 'Active' : 'Inactive' }}
                  </button>
                </td>
                <td class="py-3.5 px-4 font-semibold text-xs text-[#0e1726] dark:text-white">
                  <span class="badge bg-primary/10 text-primary rounded-full px-2.5 py-0.5 font-bold">
                    {{ role.users_count ?? 0 }} users
                  </span>
                </td>
                <td class="py-3.5 px-4 font-semibold text-xs text-[#0e1726] dark:text-white">
                  <span class="badge bg-info/10 text-info rounded-full px-2.5 py-0.5 font-bold">
                    {{ role.privileges_count ?? 0 }} privileges
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <TableDropdown>
                    <div class="py-1">
                      <Link
                        :href="route('tyro-dashboard.roles.show', role.id)"
                        class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        View Details
                      </Link>
                      <Link
                        :href="route('tyro-dashboard.roles.edit', role.id)"
                        class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Role
                      </Link>
                      <button
                        v-if="!isProtected(role)"
                        type="button"
                        @click="deleteRole(role)"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition"
                      >
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Role
                      </button>
                    </div>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="!roles.data || roles.data.length === 0">
                <td colspan="6" class="text-center py-8 text-gray-400 text-xs">
                  No roles found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="roles" class="mt-4" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TableDropdown from '@/Components/TableDropdown.vue';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  roles: Object,
  protectedRoles: {
    type: Array,
    default: () => [],
  },
  filters: Object,
});

const search = ref(props.filters?.search || '');

function applySearch() {
  router.get(
    route('tyro-dashboard.roles.index'),
    { search: search.value || undefined },
    { preserveState: true, replace: true }
  );
}

function resetSearch() {
  search.value = '';
  router.get(route('tyro-dashboard.roles.index'), {}, { preserveState: true, replace: true });
}

function isProtected(role) {
  return props.protectedRoles.includes(role.slug) || props.protectedRoles.includes(role.id);
}

function toggleRoleStatus(role) {
  router.post(route('tyro-dashboard.roles.toggle', role.id), {}, { preserveScroll: true });
}

function deleteRole(role) {
  Swal.fire({
    title: 'Delete Role?',
    text: `Are you sure you want to delete role "${role.name}"? All assigned users will be detached.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    confirmButtonColor: '#e7515a',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('tyro-dashboard.roles.destroy', role.id));
    }
  });
}
</script>
