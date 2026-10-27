<template>
  <AdminLayout :title="`Role: ${role.name}`">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">{{ role.name }}</h2>
            <span
              :class="role.is_active ? 'badge bg-success/20 text-success' : 'badge bg-danger/20 text-danger'"
              class="rounded-full px-2.5 py-0.5 text-xs font-bold uppercase"
            >
              {{ role.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Role Slug: <span class="font-mono font-bold text-primary">{{ role.slug }}</span></p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('tyro-dashboard.roles.edit', role.id)" class="btn btn-primary btn-sm">
            Edit Role
          </Link>
          <Link :href="route('tyro-dashboard.roles.index')" class="btn btn-outline-dark btn-sm">
            ← Back to Roles
          </Link>
        </div>
      </div>

      <!-- Assigned Privileges -->
      <div class="panel">
        <h3 class="text-sm font-bold text-[#0e1726] dark:text-white uppercase tracking-wider mb-3">
          Assigned Privileges ({{ role.privileges?.length || 0 }})
        </h3>
        <div v-if="role.privileges && role.privileges.length > 0" class="flex flex-wrap gap-2">
          <span
            v-for="p in role.privileges"
            :key="p.id"
            class="badge bg-primary/10 text-primary border border-primary/20 rounded-lg px-3 py-1 text-xs font-semibold"
          >
            {{ p.name }} <span class="text-gray-400 font-mono text-[10px] ltr:ml-1 rtl:mr-1">({{ p.slug }})</span>
          </span>
        </div>
        <p v-else class="text-xs text-gray-400">No privileges assigned to this role.</p>
      </div>

      <!-- Assigned Users -->
      <div class="panel">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-[#0e1726] dark:text-white uppercase tracking-wider">
            Users with this Role ({{ role.users?.length || 0 }})
          </h3>
          <Link :href="route('tyro-dashboard.users.index', { role: role.slug })" class="text-xs text-primary font-semibold hover:underline">
            View in Users List →
          </Link>
        </div>

        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">User</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Username</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Designation</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="user in role.users" :key="user.id" class="hover:bg-primary/5 transition">
                <td class="py-3 px-4">
                  <div>
                    <span class="font-bold text-xs text-[#0e1726] dark:text-white block">{{ user.name }}</span>
                    <span class="text-[11px] text-gray-400 block">{{ user.email }}</span>
                  </div>
                </td>
                <td class="py-3 px-4 font-mono text-xs text-gray-600 dark:text-gray-300">
                  {{ user.username }}
                </td>
                <td class="py-3 px-4 text-xs capitalize text-gray-600 dark:text-gray-300">
                  {{ user.designation || '—' }}
                </td>
                <td class="py-3 px-4 text-right">
                  <button
                    type="button"
                    @click="removeUser(user)"
                    class="btn btn-outline-danger btn-sm text-[11px] py-1 px-2.5"
                  >
                    Remove from Role
                  </button>
                </td>
              </tr>
              <tr v-if="!role.users || role.users.length === 0">
                <td colspan="4" class="text-center py-6 text-gray-400 text-xs">
                  No users are currently assigned to this role.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  role: Object,
});

function removeUser(user) {
  Swal.fire({
    title: 'Remove User?',
    text: `Remove ${user.name} from role "${props.role.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, remove',
    confirmButtonColor: '#e7515a',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(
        route('tyro-dashboard.roles.remove-user', { id: props.role.id, userId: user.id }),
        { preserveScroll: true }
      );
    }
  });
}
</script>
