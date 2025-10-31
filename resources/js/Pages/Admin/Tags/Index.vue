<script setup>
import { useForm } from '@inertiajs/vue3'
defineProps({ items: Array })
const form = useForm({ name:'', slug:'', is_active:true })
const submit = () => form.post('/admin/tags', { onSuccess: () => form.reset('name','slug') })
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Tags</h1>

    <form @submit.prevent="submit" class="mb-6 flex gap-2">
      <input class="border px-2 py-1" v-model="form.name" placeholder="Name">
      <input class="border px-2 py-1" v-model="form.slug" placeholder="slug">
      <button class="px-3 py-1 bg-black text-white" :disabled="form.processing">Add</button>
    </form>

    <ul class="space-y-2">
      <li v-for="t in items" :key="t.id" class="border p-2 rounded">{{ t.name }} ({{ t.slug }})</li>
    </ul>
  </div>
</template>
