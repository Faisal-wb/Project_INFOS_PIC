<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

import axios from 'axios';

const router = useRouter();

const formData = ref({
  email: '',
  password: ''
});

const errorMessage = ref('');

async function handleLogin() {
  errorMessage.value = '';
  try {
    // Meminta Laravel set cookie session
    await axios.get('/sanctum/csrf-cookie');
    
    const response = await axios.post('/login', formData.value, {
      headers: {
        'Accept': 'application/json'
      }
    });

    if (response.data.status === 'success') {
      // Jika berhasil login, cek apakah admin
      if (response.data.is_admin) {
        router.push('/admin');
      } else {
        router.push('/beranda');
      }
    }
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan saat mencoba login.';
    }
  }
}

function handleKembali() {
  router.push('/');
}
</script>

<template>
  <div class="auth-page">
    <!-- Navbar (Sederhana tanpa menu) -->
    <nav class="navbar">
      <div class="navbar-container">
        <div class="brand">
          <span class="brand-text">SMK Tunas Harapan Pati</span>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
      <div class="auth-card-wrapper">
        <div class="auth-card">
          <h2 class="auth-title">Masuk ke Akun Anda</h2>
          <p class="auth-subtitle">Silakan login untuk mengakses informasi sekolah</p>
          
          <form @submit.prevent="handleLogin" class="auth-form">
            <div v-if="errorMessage" class="error-alert">
              {{ errorMessage }}
            </div>
            
            <div class="form-group">
              <label>Email / Username</label>
              <input type="text" v-model="formData.email" class="form-input" placeholder="Masukkan email atau NISN" required />
            </div>
            
            <div class="form-group">
              <label>Password</label>
              <input type="password" v-model="formData.password" class="form-input" placeholder="Masukkan password" required />
            </div>
            
            <button type="submit" class="btn btn-blue w-100">Masuk</button>
          </form>
          
          <div class="auth-footer">
            <p>Belum punya akun? <router-link to="/register" class="text-link">Daftar sekarang</router-link></p>
          </div>
        </div>
      </div>

      <!-- Bottom Nav Controls -->
      <div class="bottom-nav-controls">
        <button class="btn btn-outline nav-btn" @click="handleKembali">&larr; Kembali ke Home</button>
      </div>
    </main>
  </div>
</template>

<style scoped>
.auth-page {
  min-height: 100vh;
  background-color: var(--gray-bg);
  display: flex;
  flex-direction: column;
}

/* --- NAVBAR --- */
.navbar {
  background-color: var(--bg-white);
  height: 70px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  border-bottom: 1px solid #dee2e6;
}

.navbar-container {
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 30px;
  display: flex;
  justify-content: center; /* Logo di tengah untuk halaman login */
}

.brand-text {
  font-size: 20px;
  font-weight: 700;
  color: var(--text-dark);
}

/* --- MAIN CONTENT --- */
.main-content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 30px;
  position: relative;
}

.auth-card-wrapper {
  width: 100%;
  max-width: 420px;
  margin-top: -50px; /* Slight optical adjustment supaya tidak terlalu bawah */
}

.auth-card {
  background-color: var(--bg-white);
  padding: 40px;
  border-radius: 8px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
  border: 1px solid #dee2e6;
  text-align: center;
}

.auth-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text-dark);
  margin-bottom: 8px;
}

.auth-subtitle {
  font-size: 14px;
  color: #666;
  margin-bottom: 30px;
}

.auth-form {
  text-align: left;
}

.error-alert {
  background-color: #f8d7da;
  color: #721c24;
  padding: 10px 15px;
  border-radius: 4px;
  margin-bottom: 15px;
  font-size: 14px;
  border: 1px solid #f5c6cb;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

.form-group label {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 12px 15px;
  font-size: 15px;
  font-family: inherit;
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: #fafafa;
  transition: border-color 0.2s, background-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-blue);
  background-color: #fff;
}

.w-100 {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  margin-top: 10px;
}

.auth-footer {
  margin-top: 25px;
  font-size: 14px;
  color: #666;
}

.text-link {
  color: var(--primary-blue);
  font-weight: 600;
  text-decoration: none;
}

.text-link:hover {
  text-decoration: underline;
}

/* Bottom Nav Controls */
.bottom-nav-controls {
  position: absolute;
  bottom: 30px;
  left: 30px;
}

.nav-btn {
  font-size: 14px;
  padding: 8px 20px;
  background-color: var(--bg-white);
}

/* Responsive */
@media (max-width: 500px) {
  .auth-card {
    padding: 30px 20px;
  }
  .bottom-nav-controls {
    position: relative;
    left: 0;
    bottom: 0;
    margin-top: 40px;
    text-align: center;
  }
  .main-content {
    flex-direction: column;
    align-items: stretch;
    justify-content: flex-start;
  }
}
</style>
