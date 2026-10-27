<template>
  <AdminLayout title="Users & Impersonation">
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold uppercase tracking-wider text-[#0e1726] dark:text-white">Users & Team Management</h2>
          <p class="text-xs text-[#506690] dark:text-[#888ea8]">Manage system operators, assign access roles, and control account security.</p>
        </div>
        <Link
          :href="route('tyro-dashboard.users.create')"
          class="btn btn-primary gap-2 self-start sm:self-auto"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add User
        </Link>
      </div>

      <div class="panel">
        <!-- Filter Toolbar -->
        <div class="mb-5">
          <form @submit.prevent="applyFilters" class="flex flex-wrap items-center gap-2.5">
            <!-- Search -->
            <div class="relative w-60 shrink-0">
              <input
                v-model="filtersForm.search"
                type="text"
                placeholder="Search name, email, username..."
                class="form-input ltr:pr-9 rtl:pl-9 text-xs py-2 w-full"
              />
              <span class="absolute inset-y-0 ltr:right-0 rtl:left-0 pr-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8" stroke-width="2"/>
                  <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                </svg>
              </span>
            </div>

            <!-- Role Filter -->
            <select
              v-model="filtersForm.role"
              class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9"
            >
              <option value="">All Roles</option>
              <option v-for="r in roles" :key="r.id" :value="r.slug">
                {{ r.name }}
              </option>
            </select>

            <!-- Status Filter -->
            <select
              v-model="filtersForm.status"
              class="form-select !w-auto max-w-fit shrink-0 text-xs py-2 ltr:pr-9 rtl:pl-9"
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
            </select>

            <!-- Buttons -->
            <div class="flex items-center gap-2 shrink-0">
              <button type="submit" class="btn btn-primary btn-sm text-xs py-2 px-3 shrink-0">Filter</button>
              <button @click="resetFilters" type="button" class="btn btn-outline-dark btn-sm text-xs py-2 px-3 shrink-0">Reset</button>
            </div>
          </form>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
          <table class="table-hover whitespace-nowrap w-full">
            <thead>
              <tr>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">User</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Username</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Designation</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Roles</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Status</th>
                <th class="text-left font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">2FA</th>
                <th class="text-right font-bold text-[#0e1726] dark:text-white-light text-xs uppercase tracking-wider py-3.5 px-4 bg-[#f6f8fa] dark:bg-[#1a2941]">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#e0e6ed] dark:divide-[#1b2e4b]">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-primary/5 transition">
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-xs shrink-0">
                      {{ (user.name || 'U').charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="font-bold text-sm text-[#0e1726] dark:text-white">{{ user.name }}</div>
                      <div class="text-xs text-[#506690] dark:text-[#888ea8]">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-mono text-xs text-gray-700 dark:text-gray-300">
                  {{ user.username }}
                </td>
                <td class="py-3.5 px-4 text-xs font-semibold capitalize text-gray-700 dark:text-gray-300">
                  {{ user.designation || '—' }}
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="r in user.roles"
                      :key="r.id"
                      class="badge bg-primary/10 text-primary rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                    >
                      {{ r.name }}
                    </span>
                    <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-gray-400">No Role</span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="user.suspended_at"
                    class="badge bg-danger/20 text-danger rounded-full px-2.5 py-0.5 text-xs font-bold uppercase"
                  >
                    Suspended
                  </span>
                  <span
                    v-else
                    class="badge bg-success/20 text-success rounded-full px-2.5 py-0.5 text-xs font-bold uppercase"
                  >
                    Active
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    v-if="user.two_factor_confirmed_at"
                    class="badge bg-info/20 text-info rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
                  >
                    Enabled
                  </span>
                  <span v-else class="text-xs text-gray-400">Disabled</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <TableDropdown>
                    <div class="py-1">
                      <Link
                        :href="route('tyro-dashboard.users.edit', user.id)"
                        class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#0e1726] dark:text-[#d0d2d6] hover:bg-[#ebedf2] dark:hover:bg-[#121e32] hover:text-primary transition"
                      >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit User
                      </Link>

                      <button
                        v-if="canLoginAs(user)"
                        type="button"
                        @click="impersonate(user)"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs font-semibold text-info hover:bg-info/10 transition"
                      >
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Login As
                      </button>

                      <button
                        v-if="canSuspend(user)"
                        type="button"
                        @click="toggleSuspension(user)"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs font-semibold text-warning hover:bg-warning/10 transition"
                      >
                        <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        {{ user.suspended_at ? 'Unsuspend User' : 'Suspend User' }}
                      </button>

                      <button
                        v-if="canDelete(user)"
                        type="button"
                        @click="deleteUser(user)"
                        class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs font-semibold text-danger hover:bg-danger/10 transition"
                      >
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete User
                      </button>
                    </div>
                  </TableDropdown>
                </td>
              </tr>
              <tr v-if="!users.data || users.data.length === 0">
                <td colspan="7" class="text-center py-8 text-gray-400 text-xs">
                  No users found matching your filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="users" class="mt-4" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TableDropdown from '@/Components/TableDropdown.vue';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  users: Object,
  roles: Array,
  protectedUsers: {
    type: Array,
    default: () => [],
  },
  filters: Object,
});

const page = usePage();
const currentUser = page.props.auth?.user;

const filtersForm = reactive({
  search: props.filters?.search || '',
  role: props.filters?.role || '',
  status: props.filters?.status || '',
});

function applyFilters() {
  router.get(
    route('tyro-dashboard.users.index'),
    {
      search: filtersForm.search || undefined,
      role: filtersForm.role || undefined,
      status: filtersForm.status || undefined,
    },
    { preserveState: true, replace: true }
  );
}

function resetFilters() {
  filtersForm.search = '';
  filtersForm.role = '';
  filtersForm.status = '';
  router.get(route('tyro-dashboard.users.index'), {}, { preserveState: true, replace: true });
}

function canLoginAs(user) {
  return currentUser && user.id !== currentUser.id;
}

function canSuspend(user) {
  return currentUser && user.id !== currentUser.id;
}

function canDelete(user) {
  return (
    currentUser &&
    user.id !== currentUser.id &&
    !props.protectedUsers.includes(user.id)
  );
}

function impersonate(user) {
  Swal.fire({
    title: 'Login As User?',
    text: `You are about to switch sessions into ${user.name}. You can leave at any time using the top safety banner.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Login As',
    confirmButtonColor: '#4361ee',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('tyro-dashboard.users.login-as', user.id));
    }
  });
}

function toggleSuspension(user) {
  if (user.suspended_at) {
    router.post(route('tyro-dashboard.users.unsuspend', user.id), {}, { preserveScroll: true });
  } else {
    Swal.fire({
      title: 'Suspend User?',
      text: `Reason for suspending ${user.name}:`,
      input: 'text',
      inputPlaceholder: 'Optional suspension reason...',
      showCancelButton: true,
      confirmButtonText: 'Suspend',
      confirmButtonColor: '#e2a03f',
      cancelButtonText: 'Cancel',
    }).then((result) => {
      if (result.isConfirmed) {
        router.post(
          route('tyro-dashboard.users.suspend', user.id),
          { reason: result.value || '' },
          { preserveScroll: true }
        );
      }
    });
  }
}

function deleteUser(user) {
  Swal.fire({
    title: 'Delete User?',
    text: `Are you sure you want to delete user "${user.name}"? This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    confirmButtonColor: '#e7515a',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('tyro-dashboard.users.destroy', user.id));
    }
  });
}
</script>
