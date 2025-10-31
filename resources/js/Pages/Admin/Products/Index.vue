<script setup>
defineProps({ items: Object });
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Products</h1>
    <a href="/admin/products/create" class="px-3 py-1 bg-black text-white">New Product</a>
    <div class="mt-4 grid gap-3">
      <div v-for="p in items.data" :key="p.id" class="border p-3 rounded">
        <div class="font-semibold">{{ p.name }}</div>
        <div class="text-sm text-gray-600">Slug: {{ p.slug }}</div>
        <div class="text-sm">Variants: {{ p.has_variants ? 'Yes':'No' }} | Addons: {{ p.has_addons ? 'Yes':'No' }}</div>
        <a :href="`/admin/products/${p.id}/edit`" class="text-blue-600 underline">Edit</a>
      </div>
    </div>
  </div>
</template>
