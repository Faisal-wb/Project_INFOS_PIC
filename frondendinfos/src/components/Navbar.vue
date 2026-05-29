<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

const navLinks = [
  { name: 'Beranda', path: '/' },
  { name: 'Informasi', path: '/beranda' },
  { name: 'Cek Administrasi', path: '/cek-administrasi' },
]

function handleScroll() { isScrolled.value = window.scrollY > 50 }
function toggleMobileMenu() { isMobileMenuOpen.value = !isMobileMenuOpen.value }
function closeMobileMenu() { isMobileMenuOpen.value = false }

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <header class="navbar" :class="{ scrolled: isScrolled }">
    <div class="navbar-container container">
      <router-link to="/" class="navbar-brand" @click="closeMobileMenu">
        <div class="brand-icon" style="display: flex; gap: 10px; align-items: center;">
          <img src="../assets/logo.png" alt="Logo SMK" style="height: 35px; width: auto;" />
          <img src="../assets/Vokasi-Indonesia.png" alt="Logo Vokasi" style="height: 35px; width: auto;" />
        </div>
        <div class="brand-text">
          <span class="brand-name">SMK Tunas Harapan Pati</span>
          <span class="brand-tagline">Sistem Informasi Sekolah</span>
        </div>
      </router-link>
      <nav class="navbar-nav">
        <router-link v-for="link in navLinks" :key="link.name" :to="link.path" class="nav-link" active-class="nav-link--active">{{ link.name }}</router-link>
      </nav>
      <div class="navbar-actions">
        <router-link to="/login" class="btn btn-primary btn-nav">Masuk</router-link>
        <button class="hamburger" :class="{ active: isMobileMenuOpen }" @click="toggleMobileMenu" aria-label="Toggle menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
    <Transition name="slide-down">
      <div v-if="isMobileMenuOpen" class="mobile-menu">
        <nav class="mobile-nav">
          <router-link v-for="link in navLinks" :key="link.name" :to="link.path" class="mobile-link" @click="closeMobileMenu">{{ link.name }}</router-link>
          <router-link to="/login" class="btn btn-primary mobile-cta" @click="closeMobileMenu">Masuk</router-link>
        </nav>
      </div>
    </Transition>
  </header>
</template>

<style scoped>
.navbar { position:fixed;top:0;left:0;right:0;z-index:1000;padding:16px 0;transition:all var(--transition-normal);background:transparent }
.navbar.scrolled { background:rgba(26,35,50,0.95);backdrop-filter:blur(12px);padding:10px 0;box-shadow:0 4px 20px rgba(0,0,0,0.15) }
.navbar-container { display:flex;align-items:center;justify-content:space-between }
.navbar-brand { display:flex;align-items:center;gap:12px }
.brand-text { display:flex;flex-direction:column }
.brand-name { font-family:var(--font-heading);font-size:1.05rem;font-weight:700;color:var(--white);line-height:1.2 }
.brand-tagline { font-size:0.7rem;color:rgba(255,255,255,0.6);letter-spacing:0.5px }
.navbar-nav { display:flex;align-items:center;gap:6px }
.nav-link { padding:8px 18px;font-size:0.9rem;font-weight:500;color:rgba(255,255,255,0.8);border-radius:var(--radius-full);transition:all var(--transition-fast);position:relative }
.nav-link:hover { color:var(--white);background:rgba(255,255,255,0.1) }
.nav-link--active { color:var(--white)!important;background:rgba(232,134,42,0.2)!important }
.navbar-actions { display:flex;align-items:center;gap:12px }
.btn-nav { padding:8px 24px;font-size:0.85rem }
.hamburger { display:none;flex-direction:column;gap:5px;padding:8px }
.hamburger span { display:block;width:24px;height:2.5px;background:var(--white);border-radius:2px;transition:all var(--transition-fast) }
.hamburger.active span:nth-child(1) { transform:rotate(45deg) translate(5px,5px) }
.hamburger.active span:nth-child(2) { opacity:0 }
.hamburger.active span:nth-child(3) { transform:rotate(-45deg) translate(6px,-6px) }
.mobile-menu { background:rgba(26,35,50,0.98);backdrop-filter:blur(16px);border-top:1px solid rgba(255,255,255,0.08) }
.mobile-nav { display:flex;flex-direction:column;padding:16px 24px 24px;gap:4px }
.mobile-link { padding:14px 16px;font-size:1rem;font-weight:500;color:rgba(255,255,255,0.85);border-radius:var(--radius-md);transition:all var(--transition-fast) }
.mobile-link:hover { background:rgba(255,255,255,0.08);color:var(--white) }
.mobile-cta { margin-top:12px;text-align:center }
.slide-down-enter-active,.slide-down-leave-active { transition:all 0.3s ease }
.slide-down-enter-from,.slide-down-leave-to { opacity:0;transform:translateY(-10px) }
@media(max-width:900px) { .navbar-nav{display:none} .btn-nav{display:none} .hamburger{display:flex} }
@media(max-width:480px) { .brand-name{font-size:0.9rem} .brand-tagline{font-size:0.65rem} }
</style>
