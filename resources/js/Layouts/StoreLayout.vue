
<script setup >
import { onMounted } from 'vue'

onMounted(() => {
  if (window.lucide) lucide.createIcons()
})
import HeaderNavbar from '@/Components/Store/HeaderNavbar.vue'
import FooterStore from '@/Components/Store/FooterStore.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white text-gray-900">
    <!-- Header -->
    <HeaderNavbar />

    <!-- Page Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <FooterStore />
  </div>
</template>
