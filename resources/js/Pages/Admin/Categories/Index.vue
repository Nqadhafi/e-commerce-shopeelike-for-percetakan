<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  items: Array,
  parents: Array,
  q: String,
})

const q = ref(props.q || '')
const doSearch = () => router.visit(route('admin.categories.index'), { method:'get', data:{ q: q.value }, replace:true, preserveState:true })

// Create
const createForm = useForm({
  name:'', slug:'', parent_id:null, position:0, is_active:true, description:'', image: null
})
const submitCreate = () => createForm.post(route('admin.categories.store'), {
  forceFormData: true,
  onSuccess: () => createForm.reset('name','slug','description','image')
})

// Edit
const editing = ref(null)
const editForm = useForm({
  id:null, name:'', slug:'', parent_id:null, position:0, is_active:true, description:'', image: null
})
const startEdit = (c) => {
  editing.value = c.id
  editForm.id = c.id
  editForm.name = c.name
  editForm.slug = c.slug
  editForm.parent_id = c.parent_id
  editForm.position = c.position
  editForm.is_active = c.is_active
  editForm.description = c.description || ''
  editForm.image = null
}
const cancelEdit = () => { editing.value = null }

const submitEdit = () => editForm.post(route('admin.categories.update', editForm.id), {
  method:'put', forceFormData: true, preserveScroll:true,
  onSuccess: () => { editing.value = null }
})

const toggle = (id) => router.patch(route('admin.categories.toggle', id))
const destroyCat = (id) => { if (confirm('Hapus kategori ini?')) router.delete(route('admin.categories.destroy', id)) }
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold">Categories</h1>
      <div class="flex gap-2">
        <input class="border px-2 py-1" v-model="q" placeholder="Search..." @keyup.enter="doSearch" />
        <button class="px-3 py-1 border" @click="doSearch">Cari</button>
      </div>
    </div>

    <!-- Create -->
    <div class="border rounded p-3">
      <div class="font-semibold mb-2">Tambah Kategori</div>
      <form @submit.prevent="submitCreate" class="grid gap-2 md:grid-cols-2">
        <input class="border px-2 py-1" v-model="createForm.name" placeholder="Nama" />
        <input class="border px-2 py-1" v-model="createForm.slug" placeholder="slug (opsional, auto dari nama)" />
        <select class="border px-2 py-1" v-model="createForm.parent_id">
          <option :value="null">— Parent (none)</option>
          <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
        <input class="border px-2 py-1" type="number" min="0" v-model.number="createForm.position" placeholder="Position" />
        <label class="inline-flex items-center gap-2"><input type="checkbox" v-model="createForm.is_active" /> Aktif</label>
        <input type="file" @change="e => createForm.image = e.target.files[0]" />
        <textarea class="border px-2 py-1 md:col-span-2" rows="3" v-model="createForm.description" placeholder="Deskripsi (opsional)"></textarea>
        <div class="md:col-span-2">
          <button class="px-3 py-1 bg-black text-white" :disabled="createForm.processing">
            {{ createForm.processing ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
      <div v-if="createForm.errors && Object.keys(createForm.errors).length" class="text-red-600 text-sm mt-2">
        <div v-for="(msg,key) in createForm.errors" :key="key">{{ msg }}</div>
      </div>
    </div>

    <!-- List -->
    <div class="border rounded overflow-auto">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left px-3 py-2 border">Nama</th>
            <th class="text-left px-3 py-2 border">Slug</th>
            <th class="text-left px-3 py-2 border">Parent</th>
            <th class="text-left px-3 py-2 border">Pos</th>
            <th class="text-left px-3 py-2 border">Aktif</th>
            <th class="text-left px-3 py-2 border">Gambar</th>
            <th class="px-3 py-2 border w-56">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in props.items" :key="c.id" class="border-t align-top">
            <template v-if="editing === c.id">
              <td class="px-3 py-2 border"><input class="border px-2 py-1 w-full" v-model="editForm.name" /></td>
              <td class="px-3 py-2 border"><input class="border px-2 py-1 w-full" v-model="editForm.slug" /></td>
              <td class="px-3 py-2 border">
                <select class="border px-2 py-1 w-full" v-model="editForm.parent_id">
                  <option :value="null">—</option>
                  <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
              </td>
              <td class="px-3 py-2 border"><input class="border px-2 py-1 w-20" type="number" min="0" v-model.number="editForm.position" /></td>
              <td class="px-3 py-2 border"><input type="checkbox" v-model="editForm.is_active" /></td>
              <td class="px-3 py-2 border">
                <div class="flex items-center gap-2">
                  <input type="file" @change="e => editForm.image = e.target.files[0]" />
                  <a v-if="c.image_url" :href="c.image_url" target="_blank" class="text-blue-600 underline text-sm">lihat</a>
                </div>
              </td>
              <td class="px-3 py-2 border">
                <div class="flex gap-2">
                  <button class="px-3 py-1 bg-black text-white" :disabled="editForm.processing" @click="submitEdit">Save</button>
                  <button class="px-3 py-1 border" @click="cancelEdit">Cancel</button>
                </div>
              </td>
            </template>
            <template v-else>
              <td class="px-3 py-2 border">
                <div class="font-medium">{{ c.name }}</div>
                <div class="text-xs text-gray-500" v-if="c.description">{{ c.description }}</div>
              </td>
              <td class="px-3 py-2 border">{{ c.slug }}</td>
              <td class="px-3 py-2 border">
                <span v-if="c.parent_id">
                  {{ (props.items.find(i => i.id === c.parent_id) || {}).name || '—' }}
                </span>
                <span v-else>—</span>
              </td>
              <td class="px-3 py-2 border">{{ c.position }}</td>
              <td class="px-3 py-2 border">
                <button class="px-2 py-0.5 border" @click="toggle(c.id)">
                  {{ c.is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
              </td>
              <td class="px-3 py-2 border">
                <a v-if="c.image_url" :href="c.image_url" target="_blank" class="text-blue-600 underline text-sm">lihat</a>
                <span v-else>—</span>
              </td>
              <td class="px-3 py-2 border">
                <div class="flex gap-2">
                  <button class="px-3 py-1 border" @click="startEdit(c)">Edit</button>
                  <button class="px-3 py-1 border text-red-600" @click="destroyCat(c.id)">Hapus</button>
                </div>
              </td>
            </template>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
