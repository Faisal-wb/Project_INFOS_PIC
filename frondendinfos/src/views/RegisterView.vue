<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

onMounted(async () => {
  try {
    const res = await axios.get('/user');
    if (res.data && res.data.name) {
      if (res.data.email === 'admin@gmail.com') {
        router.push('/admin');
      } else {
        router.push('/beranda');
      }
    }
  } catch (error) {
    // User not authenticated, proceed to register
  }
});

const formData = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const isLoading = ref(false)
const message = ref('')
const messageType = ref('')

async function onSubmit() {
  message.value = ''
  messageType.value = ''

  if (formData.value.password !== formData.value.password_confirmation) {
    message.value = 'Password dan konfirmasi password harus sama.'
    messageType.value = 'error'
    return
  }

  if (formData.value.password.length < 6) {
    message.value = 'Password minimal 6 karakter.'
    messageType.value = 'error'
    return
  }

  isLoading.value = true

  try {
    await axios.get('/sanctum/csrf-cookie')
    const response = await axios.post('/register', formData.value, {
      headers: {
        'Accept': 'application/json'
      }
    })

    if (response.data.status === 'success') {
      message.value = 'Register berhasil! Silakan login.'
      messageType.value = 'success'
      
      setTimeout(() => {
        router.push('/login')
      }, 1500)
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      if (err.response.data.errors) {
        // Ambil error pertama
        const firstError = Object.values(err.response.data.errors)[0][0]
        message.value = firstError
      } else {
        message.value = err.response.data.message
      }
    } else {
      message.value = 'Terjadi kesalahan saat mencoba mendaftar.'
    }
    messageType.value = 'error'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="page-container">

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="section-title-wrapper">
        <h1 class="section-title">Buat Akun Baru</h1>
        <div class="title-underline"></div>
      </div>

      <div class="register-card">
        <form @submit.prevent="onSubmit">
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" v-model="formData.name" placeholder="Masukkan nama" required />
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="formData.email" placeholder="contoh@email.com" required />
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" v-model="formData.password" placeholder="Minimal 6 karakter" required />
          </div>

          <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" v-model="formData.password_confirmation" placeholder="Ulangi password" required />
          </div>

          <div v-if="message" :class="['alert', messageType === 'error' ? 'alert-error' : 'alert-success']">
            {{ message }}
          </div>

          <button type="submit" class="submit-btn" :disabled="isLoading">
            {{ isLoading ? 'Memproses...' : 'Daftar Sekarang' }}
          </button>
          
          <div class="auth-footer" style="margin-top: 20px; text-align: center; font-size: 14px; color: #666;">
            <p>Sudah punya akun? <router-link to="/login" style="color: #0056b3; font-weight: 600; text-decoration: none;">Masuk sekarang</router-link></p>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  /* Background diubah ke putih polos */
  background-color: #ffffff;
  font-family: 'Montserrat', 'Segoe UI', system-ui, sans-serif;
}

/* ---------- NAVBAR ---------- */
.navbar {
  background-color: #ffffff;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 50px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.navbar-brand {
  font-size: 24px;
  font-weight: 800;
  color: #000000;
  letter-spacing: -0.5px;
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 24px;
}

.nav-link {
  text-decoration: none;
  font-size: 15px;
  font-weight: 700;
  color: #000000;
  transition: color 0.2s ease;
}

.nav-link:hover {
  color: #ff9800;
}

.nav-btn {
  background-color: #ffa000;
  color: #ffffff;
  border: none;
  padding: 12px 24px;
  border-radius: 6px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.2s ease;
  margin-left: 10px;
}

.nav-btn:hover {
  background-color: #f57c00;
}

/* ---------- MAIN CONTENT ---------- */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60px 20px;
}

/* Judul dan Garis Bawah Oranye */
.section-title-wrapper {
  text-align: center;
  margin-bottom: 40px;
}

.section-title {
  font-size: 32px;
  font-weight: 650;
  color: #000000;
  margin-bottom: 15px;
}

.title-underline {
  width: 80px;
  height: 5px;
  background-color: #003366;
  margin: 0 auto;
}

/* Form Card (mengikuti style kartu "Program Keahlian") */
.register-card {
  background-color: #e0e0e0;
  width: 100%;
  max-width: 480px;
  border-radius: 12px;
  padding: 40px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

.form-group {
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 15px;
  font-weight: 700;
  color: #000000;
  margin-bottom: 8px;
  text-align: center;
}

.form-group input {
  padding: 14px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 15px;
  color: #333333;
  outline: none;
  background-color: #fcfcfc;
  transition: border-color 0.2s, background-color 0.2s;
}

.form-group input::placeholder {
  color: #999999;
}

.form-group input:focus {
  border-color: #003366;
  background-color: #ffffff;
}

/* Peringatan (Alert) */
.alert {
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 20px;
  text-align: center;
}

.alert-error {
  background-color: #fee2e2;
  color: #b91c1c;
}

.alert-success {
  background-color: #dcfce7;
  color: #15803d;
}

/* Tombol Submit */
.submit-btn {
  width: 100%;
  background-color: #003366;
  color: #ffffff;
  border: none;
  padding: 16px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.1s ease;
  margin-top: 10px;
}

.submit-btn:hover:not(:disabled) {
  background-color: #002244;
}

.submit-btn:active:not(:disabled) {
  transform: scale(0.98);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* ---------- RESPONSIVE ---------- */
@media (max-width: 900px) {
  .navbar {
    flex-direction: column;
    padding: 20px;
    gap: 20px;
  }
  
  .navbar-menu {
    flex-wrap: wrap;
    justify-content: center;
  }
}

@media (max-width: 500px) {
  .register-card {
    padding: 30px 20px;
  }
  
  .section-title {
    font-size: 26px;
  }
}
</style>
