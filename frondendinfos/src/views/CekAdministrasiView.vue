<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const nis = ref('');
const asesmen = ref('');
const isLoading = ref(false);
const errorMessage = ref('');
const result = ref(null);

async function checkAdministrasi() {
  if (!nis.value.trim() || !asesmen.value) {
    errorMessage.value = 'NIS dan Asesmen harus diisi.';
    return;
  }

  isLoading.value = true;
  errorMessage.value = '';
  result.value = null;

  try {
    const response = await axios.get(`/administrasi/cek?nis=${nis.value.trim()}&asesmen=${encodeURIComponent(asesmen.value)}`);
    if (response.data.status === 'success') {
      result.value = response.data.data;
    }
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan pada server. Coba lagi nanti.';
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="cek-page">
    <main class="main-content">
      
      <div class="top-actions">
        <button class="btn btn-outline" @click="router.push('/beranda')">&larr; Kembali</button>
      </div>

      <div class="cek-container">
        <h1 class="page-title">Cek Status Administrasi</h1>
        <p class="page-subtitle">Masukkan Nomor Induk Siswa (NIS) untuk melihat status pembayaran administrasi sekolah Anda.</p>

        <div class="form-card">
          <div class="form-group">
            <label for="nis">Nomor Induk Siswa (NIS)</label>
            <input 
              type="text" 
              id="nis" 
              v-model="nis" 
              class="form-input" 
              placeholder="Contoh: 12345678" 
              @keyup.enter="checkAdministrasi"
            />
          </div>

          <div class="form-group">
            <label for="asesmen">Pilih Asesmen</label>
            <select v-model="asesmen" id="asesmen" class="form-input">
              <option value="" disabled>Pilih Asesmen</option>
              <option value="ASTS (Asesmen Sumatif Tengah Semester)">ASTS (Asesmen Sumatif Tengah Semester)</option>
              <option value="ASAS (Asesmen Sumatif Akhir Semester)">ASAS (Asesmen Sumatif Akhir Semester)</option>
              <option value="ASAT (Asesmen Sumatif Akhir Tahun)">ASAT (Asesmen Sumatif Akhir Tahun)</option>
            </select>
          </div>

          <button 
            class="btn btn-blue" 
            style="width: 100%; margin-top: 15px; padding: 14px; font-size: 16px;"
            @click="checkAdministrasi" 
            :disabled="isLoading"
          >
            {{ isLoading ? 'Mengecek...' : 'Cek Status' }}
          </button>
          
          <p v-if="errorMessage" class="error-msg" style="text-align: center; margin-top: 15px;">{{ errorMessage }}</p>
        </div>

        <div v-if="result" class="result-card" :class="{'belum': result.status_bayar.toLowerCase().includes('belum'), 'lunas': !result.status_bayar.toLowerCase().includes('belum')}">
          <div class="result-header">
            <h2>Hasil Pengecekan</h2>
          </div>
          <div class="result-body">
            <div class="info-row">
              <span class="label">Nama Siswa:</span>
              <span class="value">{{ result.nama_siswa }}</span>
            </div>
            <div class="info-row">
              <span class="label">NIS:</span>
              <span class="value">{{ result.nis }}</span>
            </div>
            <div class="info-row">
              <span class="label">Kelas:</span>
              <span class="value">{{ result.kelas }}</span>
            </div>
            <div class="info-row" v-if="result.asesmen">
              <span class="label">Asesmen:</span>
              <span class="value">{{ result.asesmen }}</span>
            </div>
            <div class="info-row" v-if="result.tanggal">
              <span class="label">Tanggal Tagihan:</span>
              <span class="value">{{ new Date(result.tanggal).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
            </div>
            <div class="info-row status-row">
              <span class="label">Status Administrasi:</span>
              <span class="value status-badge">{{ result.status_bayar }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.cek-page {
  min-height: 100vh;
  background-color: var(--bg-white);
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 40px 30px 60px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.top-actions {
  width: 100%;
  max-width: 650px;
  margin-bottom: 20px;
  display: flex;
  justify-content: flex-start;
}

.cek-container {
  width: 100%;
  max-width: 650px;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: var(--text-dark);
  text-align: center;
  margin-bottom: 5px;
}

.page-subtitle {
  font-size: 16px;
  color: #666;
  text-align: center;
  line-height: 1.5;
}

.form-card {
  background-color: var(--gray-bg);
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.03);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.form-group label {
  font-weight: 600;
  font-size: 16px;
  color: var(--text-dark);
}

.input-row {
  display: flex;
  gap: 15px;
}

.form-input {
  flex: 1;
  padding: 14px 18px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-family: inherit;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-blue);
}

.btn-blue {
  padding: 0 30px;
  white-space: nowrap;
}

.error-msg {
  color: #dc3545;
  font-size: 14px;
  margin-top: 5px;
}

.result-card {
  background-color: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,0.08);
  border: 1px solid #eaeaea;
  border-top: 5px solid var(--primary-blue);
}

.result-card.lunas {
  border-top-color: #28a745;
}

.result-card.belum {
  border-top-color: #dc3545;
}

.result-header {
  background-color: #f8f9fa;
  padding: 20px 30px;
  border-bottom: 1px solid #eaeaea;
}

.result-header h2 {
  font-size: 20px;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0;
}

.result-body {
  padding: 30px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 15px;
  border-bottom: 1px dashed #eaeaea;
}

.info-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.label {
  font-weight: 500;
  color: #666;
  font-size: 15px;
}

.value {
  font-weight: 600;
  color: var(--text-dark);
  font-size: 16px;
  text-align: right;
}

.status-row {
  margin-top: 10px;
  padding-top: 20px;
  border-top: 1px solid #eaeaea;
  border-bottom: none;
}

.status-badge {
  padding: 8px 16px;
  border-radius: 30px;
  background-color: #f8d7da;
  color: #721c24;
}

.result-card.lunas .status-badge {
  background-color: #d4edda;
  color: #155724;
}

@media (max-width: 600px) {
  .main-content {
    padding: 20px 15px 40px;
  }
  
  .page-title {
    font-size: 26px;
  }
  
  .form-card {
    padding: 20px;
  }

  .input-row {
    flex-direction: column;
  }
  
  .btn-blue {
    padding: 14px;
    width: 100%;
  }

  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }

  .value {
    text-align: left;
  }
}
</style>
