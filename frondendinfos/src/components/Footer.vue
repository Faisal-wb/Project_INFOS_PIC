<script setup>
const currentYear = new Date().getFullYear()
const quickLinks = [
  { name: 'Beranda', path: '/' },
  { name: 'Informasi Libur', path: '/informasi' },
  { name: 'Kegiatan Sekolah', path: '/informasi' },
  { name: 'Jadwal Rapot', path: '/informasi' },
]
const contacts = [
  { icon: '📍', text: 'Jl. Raya Pati - Tayu KM. 4, Pati' },
  { icon: '📞', text: '(0295) 381 168' },
  { icon: '✉️', text: 'info@smkthpati.sch.id' },
]
</script>

<template>
  <footer class="footer">
    <div class="footer-wave">
      <svg viewBox="0 0 1440 120" preserveAspectRatio="none"><path d="M0,60 C360,120 720,0 1080,60 C1260,90 1380,30 1440,60 L1440,120 L0,120Z" fill="var(--primary)"/></svg>
    </div>
    <div class="footer-content">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <h3>SMK Tunas Harapan Pati</h3>
            <p class="footer-desc">Sistem Informasi Sekolah (INFOS) menyediakan akses cepat dan mudah ke berbagai informasi terkini seputar kegiatan sekolah.</p>
            <div class="footer-socials">
              <a href="#" aria-label="Facebook" class="social-icon">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
              </a>
              <a href="#" aria-label="Instagram" class="social-icon">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" stroke="currentColor" stroke-width="2" fill="none"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="none"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
              </a>
              <a href="#" aria-label="YouTube" class="social-icon">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75,15.02 15.5,11.75 9.75,8.48" fill="var(--primary)"/></svg>
              </a>
            </div>
          </div>
          <div class="footer-links">
            <h4>Tautan Cepat</h4>
            <ul>
              <li v-for="link in quickLinks" :key="link.name"><router-link :to="link.path">{{ link.name }}</router-link></li>
            </ul>
          </div>
          <div class="footer-contact">
            <h4>Kontak</h4>
            <ul>
              <li v-for="c in contacts" :key="c.text"><span class="contact-icon">{{ c.icon }}</span> {{ c.text }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">
        <p>&copy; {{ currentYear }} INFOS - SMK Tunas Harapan Pati. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer { position:relative;margin-top:0 }
.footer-wave { line-height:0 }
.footer-wave svg { width:100%;height:80px }
.footer-content { background:var(--primary);padding:40px 0 32px }
.footer-grid { display:grid;grid-template-columns:1.5fr 1fr 1fr;gap:48px }
.footer-brand h3 { color:var(--white);font-size:1.2rem;margin-bottom:12px }
.footer-desc { color:rgba(255,255,255,0.6);font-size:0.88rem;line-height:1.7;margin-bottom:20px }
.footer-socials { display:flex;gap:10px }
.social-icon { width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.7);transition:all var(--transition-fast) }
.social-icon:hover { background:var(--accent);color:var(--white);transform:translateY(-2px) }
.footer-links h4,.footer-contact h4 { color:var(--white);font-size:1rem;margin-bottom:16px;position:relative;padding-bottom:10px }
.footer-links h4::after,.footer-contact h4::after { content:'';position:absolute;bottom:0;left:0;width:30px;height:3px;background:var(--accent);border-radius:2px }
.footer-links ul,.footer-contact ul { display:flex;flex-direction:column;gap:10px }
.footer-links a { color:rgba(255,255,255,0.6);font-size:0.88rem;transition:all var(--transition-fast) }
.footer-links a:hover { color:var(--accent);padding-left:4px }
.footer-contact li { color:rgba(255,255,255,0.6);font-size:0.88rem;display:flex;align-items:center;gap:8px }
.contact-icon { font-size:1rem }
.footer-bottom { background:var(--primary-dark);padding:16px 0;text-align:center }
.footer-bottom p { color:rgba(255,255,255,0.4);font-size:0.8rem }
@media(max-width:768px) { .footer-grid{grid-template-columns:1fr;gap:32px} }
</style>
