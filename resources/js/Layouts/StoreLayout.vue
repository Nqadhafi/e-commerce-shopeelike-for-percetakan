<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { onMounted, onUpdated } from 'vue'
import HeaderNav from '@/Components/Store/HeaderNav.vue'
import FooterSection from '@/Components/Store/FooterSection.vue'

const page = usePage()
const cartCount = page.props.cart?.items_count ?? 0
const isLoggedIn = !!page.props.auth?.user

const initIcons = () => { if (window.lucide?.createIcons) window.lucide.createIcons() }
onMounted(initIcons)
onUpdated(initIcons);
</script>

<template>

  <div class="min-h-screen bg-gray-50 font-sans">
    <HeaderNav :cart-count="cartCount" :is-logged-in="isLoggedIn" />
    <main>
      <slot />
    </main>
    <FooterSection />
  </div>
</template>

<style scoped>
:global(html){ font-family:'Inter',ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial; }
</style>
