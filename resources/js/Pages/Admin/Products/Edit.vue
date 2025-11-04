<script setup>
import { reactive, computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  item: Object,        // with variants.options & variantMatrix
  categories: Array,
  tags: Array,
  addons: Array,       // {id,code,label,type,billing_basis,amount}
})

/* -------- form utama produk (sudah ada) -------- */
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

const submit = () => form.put(`/admin/products/${props.item.id}`, { preserveScroll: true })

/* -------- Matrix Editor -------- */
// build initial rows from existing matrix joined to option labels
const optionById = {}
props.item.variants?.forEach(v => v.options?.forEach(o => { optionById[o.id] = { label:o.label } }))

const matrixRows = reactive(
  (props.item.variant_matrix || props.item.variantMatrix || []).map(r => ({
    v1_option_id: r.variant1_option_id,
    v2_option_id: r.variant2_option_id,
    v1_label: optionById[r.variant1_option_id]?.label ?? '',
    v2_label: r.variant2_option_id ? (optionById[r.variant2_option_id]?.label ?? '') : null,
    unit_price: r.unit_price,
    image: null,
    existing_image: r.image_path || null,
  }))
)

const v1Options = computed(() => (props.item.variants?.[0]?.options ?? []))
const v2Options = computed(() => (props.item.variants?.[1]?.options ?? []))
const hasV2 = computed(() => (props.item.variants?.length ?? 0) > 1)

const addMatrixRow = () => {
  matrixRows.push({
    v1_option_id: v1Options.value[0]?.id || null,
    v2_option_id: hasV2.value ? (v2Options.value[0]?.id || null) : null,
    v1_label: v1Options.value[0]?.label || '',
    v2_label: hasV2.value ? (v2Options.value[0]?.label || '') : null,
    unit_price: '',
    image: null,
    existing_image: null,
  })
}
const removeMatrixRow = (idx) => { matrixRows.splice(idx,1) }

const payloadRows = () => matrixRows.map((r, i) => ({
  v1_option_id: r.v1_option_id,
  v2_option_id: r.v2_option_id,
  unit_price: r.unit_price,
  image: r.image || null,
}))

const syncing = ref(false)
const syncMatrix = async () => {
  syncing.value = true
  const fd = new FormData()
  payloadRows().forEach((row, i) => {
    fd.append(`rows[${i}][v1_option_id]`, row.v1_option_id ?? '')
    if (row.v2_option_id) fd.append(`rows[${i}][v2_option_id]`, row.v2_option_id)
    fd.append(`rows[${i}][unit_price]`, row.unit_price ?? '')
    if (row.image) fd.append(`rows[${i}][image]`, row.image)
  })
  await axios.post(`/admin/products/${props.item.id}/matrix-sync`, fd)
  syncing.value = false
}

/* -------- Add-ons editor (checklist) -------- */
const selectedAddon = reactive({})
// preload from pivot if loaded via item.addons (optional). If not available, skip.

const addonsForm = useForm({ addon_ids: [], defaults: [], requireds: [] })
const buildAddonsPayload = () => {
  const ids = Object.keys(selectedAddon).map(Number)
  addonsForm.addon_ids = ids
  addonsForm.defaults = ids.filter(id => selectedAddon[id]?.is_default)
  addonsForm.requireds = ids.filter(id => selectedAddon[id]?.is_required)
}
const toggleAddon = (id) => { selectedAddon[id] ? delete selectedAddon[id] : (selectedAddon[id] = {is_default:false,is_required:false}); buildAddonsPayload() }
const setAddonFlag = (id, key, val) => { if (!selectedAddon[id]) selectedAddon[id]={is_default:false,is_required:false}; selectedAddon[id][key]=val; buildAddonsPayload() }
const syncAddons = () => addonsForm.post(`/admin/products/${props.item.id}/addons-sync`, { preserveScroll: true })

/* -------- Price Preview -------- */
const preview = reactive({ qty:1, v1_option_id:null, v2_option_id:null, addon_ids:[], result:null, loading:false, error:null })
const doPreview = async () => {
  preview.loading = true; preview.error=null
  try{
    const { data } = await axios.post(`/admin/products/${props.item.id}/price-preview`, {
      qty: preview.qty,
      v1_option_id: preview.v1_option_id,
      v2_option_id: preview.v2_option_id,
      addon_ids: preview.addon_ids,
    })
    preview.result = data
  } catch(e){
    preview.error = e?.response?.data?.message || 'Gagal menghitung'
  } finally {
    preview.loading = false
  }
}
</script>

<template>
  <div class="p-6 space-y-8">
    <!-- Header -->
    <div>
      <h1 class="text-xl font-bold">Edit Product — {{ item.name }}</h1>
      <form @submit.prevent="submit" class="grid gap-2 mt-3">
        <input class="border px-2 py-1" v-model="form.name" />
        <input class="border px-2 py-1" v-model="form.slug" />
        <input class="border px-2 py-1" v-model="form.unit_label" />
        <label><input type="checkbox" v-model="form.has_variants" /> Variants</label>
        <label><input type="checkbox" v-model="form.has_addons" /> Add-ons</label>
        <select class="border px-2 py-1" v-model="form.status">
          <option value="draft">draft</option>
          <option value="published">published</option>
        </select>
        <input class="border px-2 py-1" v-model="form.default_unit_price" placeholder="Default price (if no variants)" />
        <button :disabled="form.processing" class="px-3 py-1 bg-black text-white w-fit">
          {{ form.processing ? 'Updating...' : 'Update' }}
        </button>
      </form>
    </div>

    <!-- Matrix Editor -->
    <div v-if="item.has_variants" class="border rounded p-4 space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Variant Matrix</h2>
        <button type="button" class="px-3 py-1 border rounded" @click="addMatrixRow">+ Row</button>
      </div>

      <div class="overflow-auto">
        <table class="min-w-[760px] border">
          <thead>
            <tr class="bg-gray-50">
              <th class="border px-2 py-1">V1</th>
              <th class="border px-2 py-1" v-if="hasV2">V2</th>
              <th class="border px-2 py-1">Harga</th>
              <th class="border px-2 py-1">Gambar</th>
              <th class="border px-2 py-1 w-16">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, idx) in matrixRows" :key="idx">
              <td class="border px-2 py-1">
                <select class="border px-2 py-1" v-model.number="row.v1_option_id">
                  <option v-for="o in v1Options" :key="o.id" :value="o.id">{{ o.label }}</option>
                </select>
              </td>
              <td class="border px-2 py-1" v-if="hasV2">
                <select class="border px-2 py-1" v-model.number="row.v2_option_id">
                  <option :value="null">—</option>
                  <option v-for="o in v2Options" :key="o.id" :value="o.id">{{ o.label }}</option>
                </select>
              </td>
              <td class="border px-2 py-1">
                <input class="border px-2 py-1 w-32" v-model="row.unit_price" placeholder="Rp" />
              </td>
              <td class="border px-2 py-1">
                <div class="flex items-center gap-2">
                  <input type="file" @change="e => row.image = e.target.files[0]" />
                  <a v-if="row.existing_image" :href="`/storage/${row.existing_image}`" target="_blank" class="text-blue-600 underline text-sm">lihat</a>
                </div>
              </td>
              <td class="border px-2 py-1 text-center">
                <button type="button" class="text-red-600" @click="removeMatrixRow(idx)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <button :disabled="syncing" @click="syncMatrix" class="px-3 py-1 bg-black text-white rounded">
          {{ syncing ? 'Menyimpan...' : 'Sync Matrix' }}
        </button>
      </div>
    </div>

    <!-- Add-ons -->
    <div v-if="item.has_addons" class="border rounded p-4 space-y-2">
      <h2 class="font-semibold">Add-ons</h2>
      <div class="grid gap-2">
        <div v-for="ad in addons" :key="ad.id" class="flex items-center gap-3 border p-2 rounded">
          <label class="flex items-center gap-2">
            <input type="checkbox" :checked="!!selectedAddon[ad.id]" @change="toggleAddon(ad.id)" />
            <span class="font-medium">{{ ad.label }}</span>
          </label>
          <span class="text-xs text-gray-600">({{ ad.type }}/{{ ad.billing_basis }} · Rp {{ ad.amount }})</span>
          <div class="ml-auto flex items-center gap-3" v-if="selectedAddon[ad.id]">
            <label class="text-sm flex items-center gap-1"><input type="checkbox" :checked="selectedAddon[ad.id].is_default" @change="setAddonFlag(ad.id,'is_default',$event.target.checked)" /> Default</label>
            <label class="text-sm flex items-center gap-1"><input type="checkbox" :checked="selectedAddon[ad.id].is_required" @change="setAddonFlag(ad.id,'is_required',$event.target.checked)" /> Required</label>
          </div>
        </div>
      </div>
      <button @click="syncAddons" class="px-3 py-1 bg-black text-white rounded">Sync Add-ons</button>
    </div>

    <!-- Quick Price Preview -->
    <div class="border rounded p-4 space-y-3">
      <h2 class="font-semibold">Preview Harga</h2>
      <div class="flex flex-wrap items-center gap-2">
        <label>Qty <input class="border px-2 py-1 w-20 ml-1" type="number" min="1" v-model.number="preview.qty" /></label>
        <label v-if="v1Options.length">V1
          <select class="border px-2 py-1 ml-1" v-model.number="preview.v1_option_id">
            <option :value="null">—</option>
            <option v-for="o in v1Options" :key="o.id" :value="o.id">{{ o.label }}</option>
          </select>
        </label>
        <label v-if="hasV2">V2
          <select class="border px-2 py-1 ml-1" v-model.number="preview.v2_option_id">
            <option :value="null">—</option>
            <option v-for="o in v2Options" :key="o.id" :value="o.id">{{ o.label }}</option>
          </select>
        </label>
        <button @click="doPreview" class="px-3 py-1 bg-black text-white rounded" :disabled="preview.loading">
          {{ preview.loading ? 'Hitung...' : 'Hitung' }}
        </button>
      </div>
      <div v-if="preview.error" class="text-red-600 text-sm">{{ preview.error }}</div>
      <div v-if="preview.result" class="text-sm">
        <div>Unit: Rp {{ preview.result.unit_price }}</div>
        <div>Qty: {{ preview.result.qty }}</div>
        <div>Total: <b>Rp {{ preview.result.total }}</b></div>
      </div>
    </div>
  </div>
</template>
