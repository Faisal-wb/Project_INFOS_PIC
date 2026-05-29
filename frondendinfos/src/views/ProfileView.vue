<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const user = ref({
  name: '',
  no_telp: '',
  about_me: '',
  foto_profile: ''
});

const isEditing = ref(false);
const fileInput = ref(null);
const errorMessage = ref('');
const successMessage = ref('');

const backendUrl = 'http://localhost:8080'; // adjust to backend domain for asset loading if needed
const profileImageUrl = computed(() => {
  if (user.value.foto_profile) {
    return `${backendUrl}/storage/${user.value.foto_profile}`;
  }
  return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.value.name || 'User') + '&background=random';
});

async function fetchProfile() {
  try {
    const response = await axios.get('/profile', {
      headers: { 'Accept': 'application/json' }
    });
    if (response.data.status === 'success') {
      user.value = response.data.data;
    }
  } catch (error) {
    if (error.response?.status === 401) {
      router.push('/login');
    } else {
      console.error('Failed to load profile', error);
    }
  }
}

onMounted(() => {
  fetchProfile();
});

async function handleSave() {
  errorMessage.value = '';
  successMessage.value = '';
  
  try {
    const response = await axios.put('/profile/update', {
      name: user.value.name,
      no_telp: user.value.no_telp,
      about_me: user.value.about_me
    }, {
      headers: { 'Accept': 'application/json' }
    });

    if (response.data.status === 'success') {
      successMessage.value = 'Profil berhasil disimpan!';
      isEditing.value = false;
      user.value = response.data.data;
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan profil';
  }
}

function triggerFileUpload() {
  fileInput.value.click();
}

async function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('foto_profile', file);

  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post('/profile/foto', formData, {
      headers: { 
        'Accept': 'application/json',
        'Content-Type': 'multipart/form-data'
      }
    });

    if (response.data.status === 'success') {
      successMessage.value = 'Foto profil berhasil diperbarui!';
      user.value = response.data.data;
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Gagal mengupload foto';
  }
}

async function handleLogout() {
  try {
    await axios.post('/logout');
    router.push('/login');
  } catch (err) {
    console.error('Logout failed', err);
    // Force redirect anyway
    router.push('/login');
  }
}

function goToBeranda() {
  router.push('/beranda');
}
</script>

<template>
  <div class="profile-page">
    <main class="main-content">
      
      <!-- Header Action Row (Like AdminView) -->
      <div class="header-action-row" style="margin-bottom: 30px;">
        <div>
          <h1 class="page-title">Profil Pengguna</h1>
          <p class="page-subtitle">Kelola informasi profil dan detail kontak Anda</p>
        </div>
      </div>

      <!-- Notifications -->
      <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
      <div v-if="errorMessage" class="alert alert-error">{{ errorMessage }}</div>

      <!-- Content Box for Profile Header -->
      <div class="content-box">
        <div class="profile-header-card">
          <div class="profile-photo-container">
            <div class="photo-wrapper" @click="triggerFileUpload">
              <img :src="profileImageUrl" alt="Foto Profile" class="profile-photo" />
              <div class="photo-overlay">
                <span>Ubah Foto</span>
              </div>
            </div>
            <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden-input" />
            <span class="foto-label">Foto profile</span>
          </div>

          <div class="profile-info-box">
            <div v-if="!isEditing" class="info-view">
              <h2 class="username-text">{{ user.name || 'Username' }}</h2>
              <p class="phone-text">{{ user.no_telp || 'No telp belum diatur' }}</p>
              <button class="btn btn-outline btn-edit" @click="isEditing = true">Edit Profil</button>
            </div>
            
            <div v-else class="info-edit">
              <div class="form-group">
                <label>Username</label>
                <input type="text" v-model="user.name" class="form-input" />
              </div>
              <div class="form-group">
                <label>No telp</label>
                <input type="text" v-model="user.no_telp" class="form-input" placeholder="08xxxxxxxxxx" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Box for About Me -->
      <div class="content-box" style="margin-top: 30px;">
        <div class="about-header">
          <h3>About me</h3>
        </div>
        
        <div v-if="!isEditing" class="about-content">
          <p>{{ user.about_me || 'Belum ada deskripsi tentang saya.' }}</p>
        </div>
        
        <div v-else class="about-edit">
          <textarea v-model="user.about_me" class="form-input textarea" rows="6" placeholder="Ceritakan tentang diri Anda..."></textarea>
        </div>

        <!-- Edit Actions -->
        <div v-if="isEditing" class="edit-actions" style="margin-top: 20px;">
          <button class="btn btn-outline" @click="isEditing = false; fetchProfile()">Batal</button>
          <button class="btn btn-blue" @click="handleSave">Simpan Perubahan</button>
        </div>
      </div>

      <!-- Bottom Nav Controls -->
      <div class="bottom-nav-controls">
        <button class="btn btn-red nav-btn" @click.prevent="handleLogout">Log out</button>
        <button class="btn btn-outline nav-btn" @click="goToBeranda">Beranda</button>
      </div>

    </main>
  </div>
</template>

<style scoped>
.profile-page {
  min-height: 100vh;
  background-color: var(--gray-bg);
  display: flex;
  flex-direction: column;
}

/* --- MAIN CONTENT --- */
.main-content {
  flex: 1;
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 30px 100px; /* space for bottom nav */
  position: relative;
}

/* Header Text */
.page-title {
  font-size: 32px;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 5px;
}

.page-subtitle {
  font-size: 18px;
  color: #666;
}

/* Notifications */
.alert {
  padding: 12px 15px;
  border-radius: 6px;
  margin-bottom: 20px;
  font-size: 14px;
  font-weight: 500;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

/* Content Box (Standard for App) */
.content-box {
  background-color: var(--bg-white);
  border-radius: 8px;
  padding: 40px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
  border: 1px solid #dee2e6;
}

/* --- PROFILE HEADER --- */
.profile-header-card {
  display: flex;
  gap: 50px;
  align-items: stretch;
}

.profile-photo-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.photo-wrapper {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-color: var(--gray-bg);
  position: relative;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  border: 4px solid var(--bg-white);
}

.profile-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.2s;
}

.photo-wrapper:hover .photo-overlay {
  opacity: 1;
}

.photo-overlay span {
  color: white;
  font-size: 14px;
  font-weight: 600;
}

.hidden-input {
  display: none;
}

.foto-label {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-dark);
}

.profile-info-box {
  flex: 1;
  background-color: var(--gray-bg);
  border-radius: 8px;
  padding: 30px;
  min-height: 120px;
  position: relative;
  border: 1px solid #dee2e6;
}

.info-view {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.username-text {
  font-size: 26px;
  font-weight: 700;
  color: var(--text-dark);
  margin: 0;
}

.phone-text {
  font-size: 16px;
  color: #666;
  margin: 0;
}

.btn-edit {
  position: absolute;
  top: 25px;
  right: 25px;
  font-size: 13px;
  padding: 6px 15px;
}

/* Edit Form */
.info-edit {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

.form-input {
  padding: 12px 15px;
  font-size: 15px;
  font-family: inherit;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #fff;
  transition: border-color 0.2s;
  width: 100%;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-blue);
}

.textarea {
  resize: vertical;
}

/* --- ABOUT ME --- */
.about-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: var(--text-dark);
  margin-bottom: 15px;
}

.about-content {
  background-color: var(--gray-bg);
  border: 1px solid #dee2e6;
  padding: 20px;
  min-height: 120px;
  font-size: 15px;
  line-height: 1.6;
  color: #444;
  border-radius: 6px;
}

/* --- EDIT ACTIONS --- */
.edit-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
}

/* Bottom Nav Controls */
.bottom-nav-controls {
  position: absolute;
  bottom: 30px;
  left: 30px;
  right: 30px;
  display: flex;
  justify-content: space-between;
}

.nav-btn {
  font-size: 15px;
  padding: 8px 24px;
}

/* Responsive Overrides */
@media (max-width: 768px) {
  .profile-header-card {
    flex-direction: column;
    align-items: center;
    gap: 25px;
  }
  
  .profile-info-box {
    width: 100%;
  }
  
  .btn-edit {
    position: relative;
    top: 0;
    right: 0;
    margin-top: 15px;
    align-self: flex-start;
  }
  
  .bottom-nav-controls {
    position: relative;
    left: 0;
    right: 0;
    bottom: 0;
    margin-top: 40px;
  }
  
  .main-content {
    padding: 30px 20px;
  }
}
</style>
