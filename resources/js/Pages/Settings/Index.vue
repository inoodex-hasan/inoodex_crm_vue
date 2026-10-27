<template>
  <AdminLayout title="System Settings">
    <div class="max-w-4xl mx-auto space-y-6">
      <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Global CRM Settings</h2>
        <p class="text-sm text-slate-500">Configure application identity, company contact info, invoice branding, and SEO metadata.</p>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <form @submit.prevent="submit" class="p-6 space-y-8">
          <!-- General Branding -->
          <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
              Organization & Branding
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company / App Name</label>
                <input v-model="form.app_name" type="text" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm" />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date Format</label>
                <select v-model="form.date_format" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm">
                  <option value="d M Y">15 Oct 2026 (d M Y)</option>
                  <option value="Y-m-d">2026-10-15 (Y-m-d)</option>
                  <option value="d/m/Y">15/10/2026 (d/m/Y)</option>
                  <option value="m/d/Y">10/15/2026 (m/d/Y)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Contact Email</label>
                <input v-model="form.contact_email" type="email" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm" />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Contact Hotline</label>
                <input v-model="form.contact_phone" type="text" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Physical Head Office Address</label>
                <textarea v-model="form.address" rows="2" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm"></textarea>
              </div>
            </div>
          </div>

          <!-- Social Links -->
          <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
              Social Profiles
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Facebook Page</label>
                <input v-model="form.social_facebook" type="url" placeholder="https://facebook.com/..." class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm" />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Twitter / X</label>
                <input v-model="form.social_twitter" type="url" placeholder="https://x.com/..." class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm" />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">LinkedIn</label>
                <input v-model="form.social_linkedin" type="url" placeholder="https://linkedin.com/company/..." class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm" />
              </div>
            </div>
          </div>

          <!-- System Switches -->
          <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
              System Controls
            </h3>
            <div class="space-y-3">
              <label class="flex items-center space-x-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                <input v-model="form.enable_registration" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                <span>Allow public student registration on portal</span>
              </label>

              <label class="flex items-center space-x-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                <input v-model="form.maintenance_mode" type="checkbox" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                <span class="text-rose-600 dark:text-rose-400 font-medium">Activate Maintenance Mode (system locked for non-admins)</span>
              </label>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200 dark:border-slate-700">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50"
            >
              Save System Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  settings: Object,
})

const form = useForm({
  app_name: props.settings?.app_name || 'Inoodex CRM',
  date_format: props.settings?.date_format || 'd M Y',
  contact_email: props.settings?.contact_email || '',
  contact_phone: props.settings?.contact_phone || '',
  address: props.settings?.address || '',
  social_facebook: props.settings?.social_facebook || '',
  social_twitter: props.settings?.social_twitter || '',
  social_linkedin: props.settings?.social_linkedin || '',
  enable_registration: Boolean(Number(props.settings?.enable_registration || 0)),
  maintenance_mode: Boolean(Number(props.settings?.maintenance_mode || 0)),
})

const submit = () => {
  form.post(route('admin.settings.update'))
}
</script>
