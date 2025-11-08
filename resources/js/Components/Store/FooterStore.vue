<template>
  <footer class="bg-gray-900 text-gray-200 py-12 mt-16">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Brand & Tagline -->
      <div>
        <h3 class="text-2xl font-bold text-white mb-2">Shabat Printing</h3>
        <p class="text-sm text-gray-400">
          Solusi percetakan cepat dan berkualitas untuk segala kebutuhanmu. Online & Offline!
        </p>
      </div>

      <!-- Menu Navigasi -->
      <div>
        <h4 class="text-sm font-semibold uppercase tracking-wide text-white mb-3">Navigasi</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="/" class="hover:underline">Beranda</a></li>
          <li><a href="/products" class="hover:underline">Produk</a></li>
          <li><a href="/about" class="hover:underline">Tentang Kami</a></li>
          <li><a href="/contact" class="hover:underline">Kontak</a></li>
        </ul>
      </div>

      <!-- Bantuan -->
      <div>
        <h4 class="text-sm font-semibold uppercase tracking-wide text-white mb-3">Bantuan</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="/faq" class="hover:underline">FAQ</a></li>
          <li><a href="/cara-order" class="hover:underline">Cara Order</a></li>
          <li><a href="/kebijakan-privasi" class="hover:underline">Privasi</a></li>
          <li><a href="/syarat-ketentuan" class="hover:underline">Syarat & Ketentuan</a></li>
        </ul>
      </div>

      <!-- Kontak -->
      <div>
        <h4 class="text-sm font-semibold uppercase tracking-wide text-white mb-3">Kontak</h4>
        <p class="text-sm text-gray-400 mb-1">WhatsApp: <a href="https://wa.me/6281234567890" class="text-blue-400 hover:underline">+62 812-3456-7890</a></p>
        <p class="text-sm text-gray-400 mb-1">Email: info@shabatprinting.com</p>
        <p class="text-sm text-gray-400">Jl. Percetakan No. 123, Surakarta</p>
      </div>
    </div>

    <div class="mt-10 border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
      © {{ new Date().getFullYear() }} Shabat Printing. All rights reserved.
    </div>
  </footer>
</template>
