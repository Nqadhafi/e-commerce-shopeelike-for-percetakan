<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  item: Object,
  categories: Array,
  tags: Array,
})

const form = useForm({
  name: props.item.name,
  slug: props.item.slug,
  unit_label: props.item.unit_label,
  default_unit_price: props.item.default_unit_price ?? '',
  has_variants: props.item.has_variants,
  has_addons: props.item.has_addons,
  status: props.item.status,
  thumbnail_url: props.item.thumbnail_url ?? '',
  is_active: props.item.is_active,
})

const submit = () => {
  form.put(`/admin/products/${props.item.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="p-6 space-y-6">
    <div>
      <h1 class="text-xl font-bold">Edit Product — {{ item.name }}</h1>

      <form @submit.prevent="submit" class="grid gap-2 mt-3">
        <input class="border px-2 py-1" v-model="form.name" placeholder="Nama produk" />
        <input class="border px-2 py-1" v-model="form.slug" placeholder="Slug unik" />
        <input class="border px-2 py-1" v-model="form.unit_label" placeholder="Satuan (pcs, set, dst)" />

        <label><input type="checkbox" v-model="form.has_variants" /> Aktifkan Variasi</label>
        <label><input type="checkbox" v-model="form.has_addons" /> Aktifkan Add-on</label>

        <select class="border px-2 py-1" v-model="form.status">
          <option value="draft">draft</option>
          <option value="published">published</option>
        </select>

        <input
          class="border px-2 py-1"
          v-model="form.default_unit_price"
          placeholder="Harga default jika tanpa variasi"
        />

        <input class="border px-2 py-1" v-model="form.thumbnail_url" placeholder="Thumbnail URL" />

        <label><input type="checkbox" v-model="form.is_active" /> Aktif</label>

        <button
          :disabled="form.processing"
          class="px-3 py-1 bg-black text-white w-fit"
        >
          {{ form.processing ? 'Updating...' : 'Update' }}
        </button>

        <div v-if="form.errors && Object.keys(form.errors).length" class="text-red-600 text-sm">
          <div v-for="(msg, key) in form.errors" :key="key">{{ msg }}</div>
        </div>
      </form>
    </div>

    <div v-if="item.has_variants" class="border-t pt-4">
      <h2 class="font-semibold mb-2">Variants</h2>
      <div v-for="v in item.variants" :key="v.id" class="border rounded p-3 mb-3">
        <div class="font-medium">{{ v.name }}</div>
        <div class="mt-2">
          <div
            v-for="o in v.options"
            :key="o.id"
            class="flex items-center gap-2"
          >
            <span class="w-56">{{ o.label }}</span>
            <span class="w-28">Rp {{ o.unit_price }}</span>
          </div>
        </div>
      </div>
      <!-- editor variant/option kita tambahkan di checkpoint berikutnya -->
    </div>

    <div v-if="item.has_addons" class="border-t pt-4">
      <h2 class="font-semibold mb-2">Add-ons</h2>
      <p>Sinkronisasi add-on akan dibuat di step berikutnya.</p>
    </div>
  </div>
</template>
