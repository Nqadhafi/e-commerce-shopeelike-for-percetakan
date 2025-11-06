<script setup>
import { defineOptions, ref } from 'vue'
import StoreLayout from '@/Layouts/StoreLayout.vue'
import SectionJumbotron from '@/Components/Store/SectionJumbotron.vue'

defineOptions({ layout: StoreLayout })
const tab = ref('unggulan');
</script>

<template>
  <SectionJumbotron />

  <!-- Cara Order -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-4 text-center">
      <div class="relative inline-block mb-12">
        <span class="absolute inset-x-0 bottom-0 h-4 bg-blue-100 -z-10 rounded-lg -skew-x-12"></span>
        <h2 class="text-3xl font-bold text-gray-900">Cara Order Mudah</h2>
      </div>

      <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-0">
        <div class="flex flex-col items-center p-4 max-w-xs">
          <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
            <i data-lucide="package-search" class="w-10 h-10"></i>
          </div>
          <h3 class="text-lg font-semibold mb-1">1. Pilih Produk</h3>
          <p class="text-gray-600 text-sm">Cari dan temukan produk yang kamu inginkan.</p>
        </div>
        <div class="flex-1 w-full md:w-auto md:max-w-xs h-0.5 border-t-2 border-dashed border-gray-300 md:mt-[-40px]"></div>
        <div class="flex flex-col items-center p-4 max-w-xs">
          <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
            <i data-lucide="credit-card" class="w-10 h-10"></i>
          </div>
          <h3 class="text-lg font-semibold mb-1">2. Bayar Pesanan</h3>
          <p class="text-gray-600 text-sm">Lakukan pembayaran dengan metode pilihanmu.</p>
        </div>
        <div class="flex-1 w-full md:w-auto md:max-w-xs h-0.5 border-t-2 border-dashed border-gray-300 md:mt-[-40px]"></div>
        <div class="flex flex-col items-center p-4 max-w-xs">
          <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
            <i data-lucide="truck" class="w-10 h-10"></i>
          </div>
          <h3 class="text-lg font-semibold mb-1">3. Pengiriman</h3>
          <p class="text-gray-600 text-sm">Pesananmu akan segera kami kirimkan ke alamatmu.</p>
        </div>
        <div class="flex-1 w-full md:w-auto md:max-w-xs h-0.5 border-t-2 border-dashed border-gray-300 md:mt-[-40px]"></div>
        <div class="flex flex-col items-center p-4 max-w-xs">
          <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
            <i data-lucide="package-check" class="w-10 h-10"></i>
          </div>
          <h3 class="text-lg font-semibold mb-1">4. Pesanan Tiba</h3>
          <p class="text-gray-600 text-sm">Selamat! Pesanan telah sampai di tanganmu.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Showcase Tabs -->
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">Showcase Produk</h2>

      <div class="flex justify-center border-b border-gray-200 mb-8">
        <button @click="tab='unggulan'"
                :class="tab==='unggulan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">Produk Unggulan</button>
        <button @click="tab='terbaru'"
                :class="tab==='terbaru' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">Produk Terbaru</button>
        <button @click="tab='terlaris'"
                :class="tab==='terlaris' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors">Produk Terlaris</button>
      </div>

      <!-- Panel Unggulan -->
      <div v-show="tab==='unggulan'" class="h-[300px] md:h-[500px] grid grid-cols-4 grid-rows-2 gap-4">
        <a href="#" class="col-span-2 row-span-2 relative overflow-hidden group">
          <img src="https://placehold.co/600x600/E2E8F0/94A3B8?text=Produk+Unggulan+1" class="collage-img">
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end p-4">
            <h3 class="text-white text-lg font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Nama Produk 1</h3>
          </div>
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/E2E8F0/94A3B8?text=Produk+2" class="collage-img">
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end p-2">
            <h3 class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Produk 2</h3>
          </div>
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/E2E8F0/94A3B8?text=Produk+3" class="collage-img">
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end p-2">
            <h3 class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Produk 3</h3>
          </div>
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/E2E8F0/94A3B8?text=Produk+4" class="collage-img">
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end p-2">
            <h3 class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Produk 4</h3>
          </div>
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/E2E8F0/94A3B8?text=Produk+5" class="collage-img">
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end p-2">
            <h3 class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Produk 5</h3>
          </div>
        </a>
      </div>

      <!-- Panel Terbaru -->
      <div v-show="tab==='terbaru'" class="h-[300px] md:h-[500px] grid grid-cols-4 grid-rows-2 gap-4">
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/D1FAE5/10B981?text=Produk+Baru+1" class="collage-img">
        </a>
        <a href="#" class="col-span-2 row-span-2 relative overflow-hidden group">
          <img src="https://placehold.co/600x600/D1FAE5/10B981?text=Produk+Baru+2" class="collage-img">
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/D1FAE5/10B981?text=Produk+Baru+3" class="collage-img">
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/D1FAE5/10B981?text=Produk+Baru+4" class="collage-img">
        </a>
        <a href="#" class="col-span-1 row-span-1 relative overflow-hidden group">
          <img src="https://placehold.co/300x300/D1FAE5/10B981?text=Produk+Baru+5" class="collage-img">
        </a>
      </div>

      <div v-show="tab==='terlaris'">
        <p class="text-center text-gray-500">Konten produk terlaris akan tampil di sini...</p>
      </div>

      <div class="text-center mt-12">
        <a href="#" class="inline-block bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg hover:bg-blue-700 transition-colors">
          Lihat Selengkapnya
        </a>
      </div>
    </div>
  </section>

  <!-- Banner 8:4 -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-6">
      <div class="lg:col-span-8 h-64 rounded-lg bg-cover bg-center p-8 flex flex-col justify-end" style="background-image:url('https://placehold.co/900x400/1F2937/FFFFFF?text=Info+Promo+Besar')">
        <h3 class="text-3xl font-bold text-white mb-2">Diskon Akhir Tahun!</h3>
        <p class="text-white mb-4 max-w-md">Semua produk elektronik turun harga. Jangan sampai kelewatan.</p>
        <a href="#" class="bg-white text-gray-900 font-semibold py-2 px-5 rounded-lg self-start hover:bg-gray-100">Cek Sekarang</a>
      </div>
      <div class="lg:col-span-4 h-64 rounded-lg bg-cover bg-center p-8 flex flex-col justify-end" style="background-image:url('https://placehold.co/400x400/F59E0B/FFFFFF?text=Voucher+Gratis')">
        <h3 class="text-3xl font-bold text-white mb-2">Jadi Member</h3>
        <p class="text-white mb-4">Daftar & dapatkan voucher gratis!</p>
        <a href="#" class="bg-white text-gray-900 font-semibold py-2 px-5 rounded-lg self-start hover:bg-gray-100">Daftar</a>
      </div>
    </div>
  </section>

  <!-- Promo Bulan Ini (cards dummy) -->
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">Promo Bulan Ini</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
        <a href="#" class="bg-white rounded-lg shadow-md overflow-hidden group block">
          <div class="relative">
            <img src="https://placehold.co/400x400/FEF2F2/DC2626?text=PRODUK+SALE" class="w-full h-48 object-cover group-hover:opacity-90 transition-opacity">
            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">-30%</span>
          </div>
          <div class="p-4">
            <h3 class="text-sm md:text-base font-semibold text-gray-800 mb-1 group-hover:text-blue-600">Nama Produk Promo 1</h3>
            <p class="text-xs text-gray-500 mb-2">Kategori Produk</p>
            <div class="flex items-center gap-2">
              <span class="text-lg font-bold text-red-600">Rp70.000</span>
              <span class="text-sm text-gray-500 line-through">Rp100.000</span>
            </div>
          </div>
        </a>
        <!-- tambahkan card lain sesuai kebutuhan -->
      </div>
    </div>
  </section>
</template>

<style scoped>
.collage-img{ width:100%; height:100%; object-fit:cover; border-radius:.5rem; transition:transform .3s ease }
.collage-img:hover{ transform:scale(1.05) }
</style>
