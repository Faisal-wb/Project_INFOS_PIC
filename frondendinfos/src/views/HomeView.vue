<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import fotoawal1 from '../assets/fotoawal1.webp';
import fotoawal2 from '../assets/fotoawal2.webp';
import fotoawal3 from '../assets/fotoawal3.webp';

const router = useRouter();
const images = [fotoawal1, fotoawal2, fotoawal3];
const currentIndex = ref(0);
let interval = null;


function nextImage() {
  currentIndex.value = (currentIndex.value + 1) % images.length;
}

onMounted(() => {
  interval = setInterval(nextImage, 6000);
});

onUnmounted(() => {
  clearInterval(interval);
});

function handleEntry() {
  // Can be mapped to the actual application entry point if needed.
  router.push('/beranda');
}
</script>

<template>
  <div class="landing-page">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <div class="brand">
          <span class="brand-text">SMK Tunas Harapan Pati</span>
        </div>

      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
      <!-- Background Slideshow -->
      <div class="hero-bg">
        <img 
          v-for="(img, i) in images" 
          :key="i" 
          :src="img" 
          :class="['bg-img', { active: currentIndex === i }]" 
          alt="Background" 
        />
        <div class="bg-overlay"></div>
      </div>

      <!-- Hero Content -->
      <div class="hero-content">
        <div class="hero-text-box">
          <h1 class="hero-title">
            Selamat Datang <br />
            di Informasi SMK Tunas Harapan Pati
          </h1>
          <p class="hero-subtitle">
            Tidak ada yang lebih baik dari hari ini untuk memulai kesuksesan masa depanmu. Awali dengan langkah dan tempat yang baik.
          </p>
        </div>
      </div>

      <div class="bottom-actions">
        <button class="btn btn-orange" @click="router.push('/register')">Daftar</button>
        <button class="btn btn-blue" @click="router.push('/login')">Masuk</button>
      </div>
    </section>
  </div>
</template>

<style scoped>
.landing-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* --- NAVBAR --- */
.navbar {
  background-color: var(--bg-white);
  height: 80px;
  display: flex;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.navbar-container {
  width: 100%;
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand-text {
  font-size: 20px;
  font-weight: 800;
  color: var(--text-dark);
}



/* --- HERO SECTION --- */
.hero {
  position: relative;
  flex: 1;
  display: flex;
  align-items: center;
  padding-top: 80px; /* Offset for navbar */
  min-height: 100vh;
}

.hero-bg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
  overflow: hidden;
}

.bg-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 1.5s ease-in-out;
}

.bg-img.active {
  opacity: 1;
}

.bg-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6); /* Dark overlay */
  z-index: 2;
}

.hero-content {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 30px;
}

.hero-text-box {
  max-width: 600px;
}

.hero-title {
  color: var(--text-light);
  font-size: 42px;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 20px;
}

.hero-subtitle {
  color: #e0e0e0;
  font-size: 15px;
  line-height: 1.6;
  margin-bottom: 30px;
  font-weight: 400;
}

.bottom-actions {
  position: absolute;
  bottom: 40px;
  right: 40px;
  z-index: 10;
  display: flex;
  gap: 15px;
}

@media (max-width: 768px) {
  .bottom-actions {
    bottom: 20px;
    right: 20px;
  }
}

/* Responsive */
@media (max-width: 992px) {
  .hero-title {
    font-size: 32px;
  }
}
</style>
