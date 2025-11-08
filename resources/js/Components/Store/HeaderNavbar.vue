<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage();

const isLoggedIn = computed(() => !!page.props.auth?.user);
const cartCount = computed(() => page.props.cart?.items_count ?? 0);
</script>

<template>
  <header class="bg-white shadow z-50 relative">
    <div class="container mx-auto px-4 py-2 flex items-center justify-between">
      <!-- Logo -->
      <a href="/" class="text-xl font-bold text-blue-600">ShabatPrinting</a>

      <!-- Search (optional) -->
      <div class="hidden md:block flex-1 mx-6">
        <input type="text" placeholder="Cari produk..." class="w-full border rounded-lg px-4 py-2 text-sm" />
      </div>

      <!-- Right Icons -->
      <div class="flex items-center gap-4">
        <!-- Cart Icon -->
        <a href="/cart" class="relative text-gray-600 hover:text-blue-600">
          <i data-lucide="shopping-cart" class="w-6 h-6"></i>
          <span v-if="cartCount > 0" class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 rounded-full">{{ cartCount }}</span>
        </a>

        <!-- Login/Profile -->
        <a v-if="!isLoggedIn" href="/login" class="text-sm font-medium text-blue-600 hover:underline">Masuk</a>
        <a v-else href="/dashboard" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600">
          <i data-lucide="user" class="w-5 h-5"></i> Akun
        </a>
      </div>
    </div>
  </header>
</template>
