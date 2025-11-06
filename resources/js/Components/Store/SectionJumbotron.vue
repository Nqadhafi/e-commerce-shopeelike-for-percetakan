<script setup>
import { onMounted, onUpdated } from 'vue'
const init = () => {
  if (!window.Swiper) return
  new window.Swiper('.jumbotron-swiper', {
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
  })
}
onMounted(init)
onUpdated(() => { if (window.lucide?.createIcons) window.lucide.createIcons() })
</script>

<template>
  <div class="swiper jumbotron-swiper h-64 md:h-96 lg:h-[500px] w-full bg-gray-200">
    <div class="swiper-wrapper">
      <div class="swiper-slide relative">
        <img src="https://placehold.co/1600x500/3B82F6/FFFFFF?text=Promo+Spesial+11.11" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
          <div class="text-center text-white p-4">
            <h2 class="text-4xl md:text-6xl font-bold mb-4">Promo Spesial 11.11</h2>
            <p class="text-lg md:text-xl mb-6">Dapatkan diskon hingga 70% untuk produk pilihan!</p>
            <a href="#" class="bg-white text-blue-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-100">Belanja Sekarang</a>
          </div>
        </div>
      </div>

      <div class="swiper-slide relative">
        <img src="https://placehold.co/1600x500/10B981/FFFFFF?text=Koleksi+Terbaru" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/30 flex items-center justify-start">
          <div class="text-left text-white p-4 md:p-12 max-w-2xl">
            <h2 class="text-4xl md:text-6xl font-bold mb-4">Koleksi Musim Hujan</h2>
            <p class="text-lg md:text-xl mb-6">Temukan gaya terbaikmu di musim ini.</p>
            <a href="#" class="bg-white text-gray-900 font-bold py-3 px-6 rounded-lg hover:bg-gray-100">Lihat Koleksi</a>
          </div>
        </div>
      </div>

      <div class="swiper-slide relative">
        <img src="https://placehold.co/1600x500/EF4444/FFFFFF?text=Gratis+Ongkir" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
          <div class="text-center text-white p-4">
            <h2 class="text-4xl md:text-6xl font-bold mb-4">GRATIS ONGKIR</h2>
            <p class="text-lg md:text-xl mb-6">Tanpa minimum belanja ke seluruh Indonesia.</p>
            <a href="#" class="bg-red-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-600">Cek S&K</a>
          </div>
        </div>
      </div>
    </div>

    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>
</template>

<style scoped>
.swiper-button-next,.swiper-button-prev{
  background-color: rgba(0,0,0,.3); color:#fff; width:44px; height:44px; border-radius:50%;
}
.swiper-button-next::after,.swiper-button-prev::after{ font-size:20px; font-weight:700; }
.swiper-pagination-bullet-active{ background-color:#fff; }
</style>
