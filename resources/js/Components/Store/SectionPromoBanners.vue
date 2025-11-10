<script setup>

defineProps({
  banners: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <section class="py-4">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div
          v-for="banner in banners"
          :key="banner.id"
          class="overflow-hidden rounded-lg shadow"
        >
          <a :href="banner.link_url || '#'">
            <img
              :src="banner.image_url || '/images/placeholder-landscape.jpg'"
              :alt="banner.title"
              class="w-full h-full object-cover"
            />
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
