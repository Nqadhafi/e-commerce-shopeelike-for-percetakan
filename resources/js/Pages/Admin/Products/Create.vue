<script setup>
import { reactive, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  categories: Array,
  tags: Array,
  addons: Array,
})

const form = useForm({
  name: '', slug: '', unit_label: 'pcs',
  default_unit_price: '', has_variants: false, has_addons: false,
  status: 'draft', thumbnail_url: '', is_active: true,
  category_ids: [], primary_category_id: null, tag_ids: [],

  // VARIANTS (max 2)
  variants: [],

  // MATRIX rows: { v1_label, v2_label|null, unit_price, image(File|null) }
  matrix: [],

  // ADDONS
  addons: [],
})

/* === Variants helpers === */
const addVariantGroup = () => { if (form.variants.length < 2) form.variants.push({ name:'', position:form.variants.length, options:[] }) }
const removeVariantGroup = (i) => { form.variants.splice(i,1); rebuildMatrixFromVariants() }
const addOption = (g) => g.options.push({ label:'', sku_suffix:'', unit_price:'' })
const removeOption = (g,i) => { g.options.splice(i,1); rebuildMatrixFromVariants() }

const variantsReady = computed(() => {
  if (!form.has_variants || !form.variants.length) return false
  if ((form.variants[0]?.options?.length ?? 0) < 1) return false
  if (form.variants[1] && (form.variants[1].options?.length ?? 0) < 1) return false
  return true
})

const rebuildMatrixFromVariants = () => {
  if (!variantsReady.value) { form.matrix = []; return }
  const v1 = form.variants[0], v2 = form.variants[1] ?? null
  const next = []
  for (const o1 of v1.options) {
    if (v2) {
      for (const o2 of v2.options) {
        const old = form.matrix.find(m => m.v1_label===o1.label && m.v2_label===o2.label)
        next.push({
          v1_label: o1.label, v2_label: o2.label,
          unit_price: old?.unit_price ?? '',
          image: old?.image ?? null,
        })
      }
    } else {
      const old = form.matrix.find(m => m.v1_label===o1.label && (m.v2_label==null || m.v2_label===''))
      next.push({
        v1_label: o1.label, v2_label: null,
        unit_price: old?.unit_price ?? '',
        image: old?.image ?? null,
      })
    }
  }
  form.matrix = next
}

/* === Apply-to-all (harga saja) === */
const applyAll = reactive({ unit_price:'' })
const doApplyAll = () => {
  if (applyAll.unit_price === '') return
  form.matrix.forEach(r => r.unit_price = applyAll.unit_price)
}

/* === Addons === */
const selectedAddonMap = reactive({})
const toggleAddon = id => { selectedAddonMap[id] ? delete selectedAddonMap[id] : (selectedAddonMap[id]={is_default:false,is_required:false}); rebuildAddonsPayload() }
const setAddonFlag = (id,k,v) => { if (!selectedAddonMap[id]) selectedAddonMap[id]={is_default:false,is_required:false}; selectedAddonMap[id][k]=v; rebuildAddonsPayload() }
const rebuildAddonsPayload = () => { form.addons = Object.entries(selectedAddonMap).map(([id,v]) => ({ id:Number(id), is_default:!!v.is_default, is_required:!!v.is_required })) }

/* === Submit === */
const submit = () => {
  if (!form.has_variants) { form.variants=[]; form.matrix=[] }
  if (!form.has_addons) form.addons=[]
  form.post('/admin/products', { forceFormData: true })
}
</script>

<template>
  <div class="p-6 space-y-6">
    <h1 class="text-xl font-bold">New Product (Matrix)</h1>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- Basic -->
      <div class="grid gap-2">
        <input class="border px-2 py-1" v-model="form.name" placeholder="Nama" />
        <input class="border px-2 py-1" v-model="form.slug" placeholder="slug" />
        <input class="border px-2 py-1" v-model="form.unit_label" placeholder="Satuan (pcs/lembar/set)" />
        <input class="border px-2 py-1" v-model="form.thumbnail_url" placeholder="Thumbnail URL (opsional)" />
        <label class="inline-flex items-center gap-2"><input type="checkbox" v-model="form.is_active" /> Aktif</label>
        <div class="flex items-center gap-4">
          <label class="inline-flex items-center gap-2"><input type="checkbox" v-model="form.has_variants" @change="rebuildMatrixFromVariants" /> Aktifkan Variasi</label>
          <label class="inline-flex items-center gap-2"><input type="checkbox" v-model="form.has_addons" /> Aktifkan Add-on</label>
        </div>
        <select class="border px-2 py-1 w-full max-w-xs" v-model="form.status">
          <option value="draft">draft</option>
          <option value="published">published</option>
        </select>
        <div v-if="!form.has_variants">
          <input class="border px-2 py-1 w-full" v-model="form.default_unit_price" placeholder="Harga default (wajib jika tanpa variasi)" />
        </div>
      </div>

      <!-- Kategori & Tag -->
      <div class="border rounded p-3">
        <div class="font-semibold mb-2">Kategori & Tag (opsional)</div>
        <div class="flex flex-wrap gap-3">
          <div>
            <div class="text-sm text-gray-600 mb-1">Kategori (multi)</div>
            <select class="border px-2 py-1" multiple size="4" v-model="form.category_ids">
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <div class="text-sm text-gray-600 mb-1">Primary Category</div>
            <select class="border px-2 py-1" v-model="form.primary_category_id">
              <option :value="null">—</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <div class="text-sm text-gray-600 mb-1">Tags (multi)</div>
            <select class="border px-2 py-1" multiple size="4" v-model="form.tag_ids">
              <option v-for="t in tags" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Variasi (penamaan & opsi di atas) -->
      <div v-if="form.has_variants" class="space-y-3">
        <div class="flex items-center justify-between">
          <div class="font-semibold">Variasi</div>
          <button type="button" class="px-3 py-1 bg-black text-white rounded" @click="addVariantGroup" :disabled="form.variants.length>=2">+ Tambah Grup</button>
        </div>

        <div v-for="(g, gi) in form.variants" :key="gi" class="border rounded p-3 space-y-2">
          <div class="flex items-center gap-2">
            <input class="border px-2 py-1 w-64" v-model="g.name" placeholder="Variasi{{ gi+1 }}" @change="rebuildMatrixFromVariants" />
            <button type="button" class="text-red-600 ml-auto" @click="removeVariantGroup(gi)">Hapus</button>
          </div>

          <div class="flex flex-wrap gap-2">
            <template v-for="(o, oi) in g.options" :key="oi">
              <div class="flex items-center gap-2 border rounded px-2 py-1">
                <input class="outline-none" v-model="o.label" placeholder="Opsi" @change="rebuildMatrixFromVariants" />
                <button type="button" title="Hapus" @click="removeOption(g,oi)">✕</button>
              </div>
            </template>
            <button type="button" class="px-2 py-1 border rounded" @click="addOption(g)">+ Tambah Opsi</button>
          </div>
        </div>
      </div>

      <!-- Daftar Variasi (matrix pricing) -->
      <div v-if="form.has_variants" class="border rounded">
        <div class="p-3 border-b flex items-center gap-2">
          <input class="border px-2 py-1 w-32" v-model="applyAll.unit_price" placeholder="Rp | Harga" />
          <button type="button" class="ml-2 px-3 py-1 bg-black text-white rounded" @click="doApplyAll">Terapkan Ke Semua</button>
        </div>

        <div class="p-3">
          <div v-if="!variantsReady" class="text-sm text-gray-600">Lengkapi nama variasi dan opsi terlebih dahulu.</div>

          <!-- 1D -->
          <div v-else-if="!form.variants[1]">
            <table class="min-w-full border">
              <thead>
                <tr class="bg-gray-50">
                  <th class="border px-2 py-1">Gambar</th>
                  <th class="border px-2 py-1">{{ form.variants[0].name || 'Variasi1' }}</th>
                  <th class="border px-2 py-1">Harga</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, idx) in form.matrix" :key="idx">
                  <td class="border px-2 py-1"><input type="file" @change="e=> row.image = e.target.files[0]" /></td>
                  <td class="border px-2 py-1">{{ row.v1_label }}</td>
                  <td class="border px-2 py-1"><input class="border px-2 py-1 w-32" v-model="row.unit_price" placeholder="Rp" /></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- 2D -->
          <div v-else class="overflow-auto">
            <table class="min-w-[700px] border">
              <thead>
                <tr class="bg-gray-50">
                  <th class="border px-2 py-1">Gambar</th>
                  <th class="border px-2 py-1">{{ form.variants[0].name || 'Variasi1' }}</th>
                  <th class="border px-2 py-1">{{ form.variants[1].name || 'Variasi2' }}</th>
                  <th class="border px-2 py-1">Harga</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, idx) in form.matrix" :key="idx">
                  <td class="border px-2 py-1"><input type="file" @change="e=> row.image = e.target.files[0]" /></td>
                  <td class="border px-2 py-1">{{ row.v1_label }}</td>
                  <td class="border px-2 py-1">{{ row.v2_label }}</td>
                  <td class="border px-2 py-1"><input class="border px-2 py-1 w-32" v-model="row.unit_price" placeholder="Rp" /></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Add-ons -->
      <div v-if="form.has_addons" class="border rounded p-3 space-y-2">
        <div class="font-semibold">Add-ons</div>
        <div class="grid gap-2">
          <div v-for="ad in props.addons" :key="ad.id" class="flex items-center gap-3 border p-2 rounded">
            <label class="flex items-center gap-2">
              <input type="checkbox" :checked="!!selectedAddonMap[ad.id]" @change="toggleAddon(ad.id)" />
              <span class="font-medium">{{ ad.label }}</span>
            </label>
            <span class="text-xs text-gray-600">({{ ad.type }}/{{ ad.billing_basis }} · Rp {{ ad.amount }})</span>
            <div class="ml-auto flex items-center gap-3" v-if="selectedAddonMap[ad.id]">
              <label class="text-sm flex items-center gap-1"><input type="checkbox" :checked="selectedAddonMap[ad.id].is_default" @change="setAddonFlag(ad.id,'is_default',$event.target.checked)" /> Default</label>
              <label class="text-sm flex items-center gap-1"><input type="checkbox" :checked="selectedAddonMap[ad.id].is_required" @change="setAddonFlag(ad.id,'is_required',$event.target.checked)" /> Required</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div>
        <button :disabled="form.processing" class="px-4 py-2 bg-black text-white rounded">
          {{ form.processing ? 'Saving...' : 'Save Product' }}
        </button>
      </div>

      <div v-if="form.errors && Object.keys(form.errors).length" class="text-red-600 text-sm">
        <div v-for="(msg, key) in form.errors" :key="key">{{ msg }}</div>
      </div>
    </form>
  </div>
</template>
