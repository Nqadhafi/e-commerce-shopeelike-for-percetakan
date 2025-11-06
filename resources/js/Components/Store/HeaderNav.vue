<script setup>
import { ref, onMounted, onUpdated } from 'vue'
const props = defineProps({
  cartCount: { type: Number, default: 0 },
  isLoggedIn: { type: Boolean, default: false },
})
const openCategories = ref(false)
const closeOnOutside = (e) => {
  const t = document.getElementById('btn-all-cats')
  const p = document.getElementById('panel-all-cats')
  if (!t || !p) return
  if (!t.contains(e.target) && !p.contains(e.target)) openCategories.value = false
}
onMounted(() => window.addEventListener('click', closeOnOutside))
onUpdated(() => { if (window.lucide?.createIcons) window.lucide.createIcons() });
</script>

<template>
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <!-- Top bar -->
    <div class="bg-gray-900 text-white text-xs">
      <div class="container mx-auto px-4 py-1.5 flex justify-between items-center">
        <p class="hidden md:block">Jam Buka: Sen - Jum (08:00 - 17:00)</p>
        <p class="text-center md:text-left">Tagline Singkat Toko Keren Kami!</p>
        <div class="flex items-center gap-4">
          <a href="tel:0812345678" class="hover:text-blue-300">0812-345-678</a>
          <a href="#" class="hidden md:block hover:text-blue-300">Bantuan</a>
        </div>
      </div>
    </div>

    <!-- Middle row -->
    <div class="container mx-auto px-4 py-4">
      <div class="flex justify-between items-center gap-4">
        <a href="/" class="text-3xl font-bold text-blue-600">LOGO</a>

        <div class="hidden lg:block w-full max-w-xl">
          <form class="relative" @submit.prevent>
            <input type="search" placeholder="Cari produk, kategori, atau merek..."
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-blue-600">
              <i data-lucide="search" class="w-5 h-5"></i>
            </button>
          </form>
        </div>

        <div class="flex items-center gap-4">
          <a href="/cart" class="relative p-2 text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-full">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            <span v-if="cartCount>0" class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
              {{ cartCount }}
            </span>
          </a>

          <template v-if="!isLoggedIn">
            <a href="/login" class="hidden md:inline-flex px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-100">Masuk</a>
            <a href="/register" class="hidden md:inline-flex px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Daftar</a>
          </template>
          <template v-else>
            <a href="/dashboard" class="p-2 text-gray-700 hover:text-blue-600 hover:bg-gray-100 rounded-full">
              <i data-lucide="user" class="w-6 h-6"></i>
            </a>
          </template>
        </div>
      </div>

      <!-- Search mobile -->
      <div class="mt-4 lg:hidden">
        <form class="relative" @submit.prevent>
          <input type="search" placeholder="Cari produk..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          <button class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-blue-600">
            <i data-lucide="search" class="w-5 h-5"></i>
          </button>
        </form>
      </div>
    </div>

    <!-- Category nav -->
    <nav class="border-t border-gray-200 bg-white">
      <div class="container mx-auto px-4 flex items-center gap-6">
        <div class="relative">
          <button id="btn-all-cats"
                  @click.stop="openCategories = !openCategories"
                  class="flex items-center gap-2 px-4 py-3 font-semibold text-gray-800 bg-gray-100 hover:bg-gray-200">
            <i data-lucide="menu" class="w-5 h-5"></i>
            Semua Kategori
            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openCategories }"></i>
          </button>
          <div id="panel-all-cats" v-show="openCategories"
               class="absolute top-full left-0 mt-1 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
            <!-- TODO: inject kategori dari server -->
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Elektronik</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Fashion Pria</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Fashion Wanita</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Kesehatan & Kecantikan</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Otomotif</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">Buku & Hobi</a>
          </div>
        </div>

        <div class="hidden md:flex items-center gap-6">
          <a href="#" class="py-3 text-sm font-medium text-gray-600 hover:text-blue-600 border-b-2 border-transparent hover:border-blue-600">Flash Sale</a>
          <a href="#" class="py-3 text-sm font-medium text-gray-600 hover:text-blue-600 border-b-2 border-transparent hover:border-blue-600">Produk Baru</a>
          <a href="#" class="py-3 text-sm font-medium text-gray-600 hover:text-blue-600 border-b-2 border-transparent hover:border-blue-600">Terlaris</a>
          <a href="#" class="py-3 text-sm font-medium text-gray-600 hover:text-blue-600 border-b-2 border-transparent hover:border-blue-600">Voucher</a>
        </div>
      </div>
    </nav>
  </header>
</template>
