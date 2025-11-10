<script setup>
import { onMounted } from 'vue'

const props = defineProps({
  banners: {
    type: Array,
    default: () => [],
  },
})

const initSwiper = () => {
  if (!window.Swiper) return

  const loopEnabled = props.banners.length >= 2

  new window.Swiper('.banner-swiper', {
    loop: loopEnabled,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  })
}

onMounted(() => {
  initSwiper()
  if (window.lucide?.createIcons) window.lucide.createIcons()
})
console.log({ props })
</script>

<template>
  <section class="py-6">
    <div class="container mx-auto">
      <div class="swiper banner-swiper">
        <div class="swiper-wrapper">
          <div
            class="swiper-slide"
            v-for="banner in banners"
            :key="banner.id"
          >
            <a :href="banner.link_url || '#'" class="block overflow-hidden rounded-xl shadow-md">
              <img
                :src="banner.image_url || 'https://freihardt.com/media/k2/items/cache/9caa2793658f3cc387f216157300b1ce_L.jpg'"
                class="w-full h-auto object-cover"
                :alt="banner.title || 'Banner Placeholder'"
              />
            </a>
          </div>
        </div>

        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.swiper-button-next,
.swiper-button-prev {
  background-color: rgba(0, 0, 0, 0.3);
  color: #fff;
  width: 44px;
  height: 44px;
  border-radius: 50%;
}

.swiper-button-next::after,
.swiper-button-prev::after {
  font-size: 20px;
  font-weight: 700;
}

.swiper-pagination-bullet-active {
  background-color: #fff;
}
</style>
