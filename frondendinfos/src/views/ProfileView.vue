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
    <!-- Navbar Custom for Profile -->
    <nav class="profile-navbar">
      <div class="nav-left">
        <div class="logo-text">
          <span class="logo-title">LOGO SMK THP</span>
          <span class="logo-subtitle">SMK Bisa SMK Hebat</span>
        </div>
      </div>
      <div class="nav-right">
        <span class="school-name">SMK Tunas Harapan Pati</span>
      </div>
    </nav>

    <main class="main-content">
      
      <!-- Notifications -->
      <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
      <div v-if="errorMessage" class="alert alert-error">{{ errorMessage }}</div>

      <!-- Profile Header -->
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
            <button class="btn-edit" @click="isEditing = true">Edit Profil</button>
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

      <!-- About Me Section -->
      <div class="about-section">
        <div class="about-box">
          <div class="about-header">
            <h3>About me</h3>
          </div>
          
          <div v-if="!isEditing" class="about-content">
            <p>{{ user.about_me || 'Belum ada deskripsi tentang saya.' }}</p>
          </div>
          
          <div v-else class="about-edit">
            <textarea v-model="user.about_me" class="form-textarea" rows="6" placeholder="Ceritakan tentang diri Anda..."></textarea>
          </div>
        </div>
      </div>

      <!-- Edit Actions -->
      <div v-if="isEditing" class="edit-actions">
        <button class="btn btn-outline" @click="isEditing = false; fetchProfile()">Batal</button>
        <button class="btn btn-blue" @click="handleSave">Simpan Perubahan</button>
      </div>

      <!-- Footer Actions -->
      <div class="footer-actions">
        <div class="logout-container">
          <a href="#" @click.prevent="handleLogout" class="logout-link">Log out?</a>
        </div>
        
        <div class="beranda-container">
          <button class="btn-beranda" @click="goToBeranda">Beranda</button>
        </div>
      </div>

    </main>
  </div>
</template>

<style scoped>
.profile-page {
  min-height: 100vh;
  background-color: #fafafa;
  display: flex;
  flex-direction: column;
}

/* --- NAVBAR --- */
.profile-navbar {
  background-color: #d8d8d8; /* Light gray from wireframe */
  height: 65px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 40px;
  border-bottom: 2px solid #ccc;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-title {
  font-size: 14px;
  font-weight: 700;
  color: #333;
}

.logo-subtitle {
  font-size: 12px;
  color: #555;
}

.school-name {
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

/* --- MAIN CONTENT --- */
.main-content {
  flex: 1;
  max-width: 900px;
  margin: 0 auto;
  width: 100%;
  padding: 40px 30px;
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

/* --- PROFILE HEADER --- */
.profile-header-card {
  display: flex;
  gap: 40px;
  align-items: flex-start;
  margin-bottom: 40px;
}

.profile-photo-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.photo-wrapper {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  background-color: #ddd;
  position: relative;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  border: 4px solid white;
}

.profile-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.5);
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
  font-size: 18px;
  font-weight: 500;
  color: #333;
}

.profile-info-box {
  flex: 1;
  background-color: #e0e0e0;
  border-radius: 8px;
  padding: 30px;
  min-height: 120px;
  position: relative;
}

.info-view {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.username-text {
  font-size: 24px;
  font-weight: 600;
  color: #222;
  margin: 0;
}

.phone-text {
  font-size: 18px;
  color: #555;
  margin: 0;
}

.btn-edit {
  position: absolute;
  top: 20px;
  right: 20px;
  background-color: transparent;
  border: 1px solid #999;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-edit:hover {
  background-color: #ccc;
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
  gap: 5px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #444;
}

.form-input {
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  width: 100%;
  max-width: 400px;
}

/* --- ABOUT ME --- */
.about-section {
  background-color: #dcdcdc;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 30px;
}

.about-header h3 {
  font-size: 18px;
  font-weight: 500;
  color: #333;
  margin-bottom: 15px;
}

.about-content {
  background-color: transparent;
  border: 1px solid #999;
  padding: 20px;
  min-height: 150px;
  font-size: 16px;
  line-height: 1.6;
  color: #444;
}

.form-textarea {
  width: 100%;
  padding: 15px;
  border: 1px solid #999;
  border-radius: 4px;
  font-size: 16px;
  resize: vertical;
  background-color: #fafafa;
  font-family: inherit;
}

/* --- EDIT ACTIONS --- */
.edit-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-bottom: 40px;
}

/* --- FOOTER ACTIONS --- */
.footer-actions {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 60px;
}

.logout-container {
  margin-bottom: 30px;
}

.logout-link {
  color: #0d6efd;
  font-size: 18px;
  text-decoration: none;
  font-weight: 500;
}

.logout-link:hover {
  text-decoration: underline;
}

.beranda-container {
  position: absolute;
  right: 0;
  bottom: 0;
}

.btn-beranda {
  background-color: white;
  border: 1px solid #333;
  padding: 10px 30px;
  font-size: 18px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-beranda:hover {
  background-color: #f0f0f0;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-header-card {
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }
  
  .profile-info-box {
    width: 100%;
  }
  
  .beranda-container {
    position: static;
    margin-top: 20px;
  }
  
  .profile-navbar {
    padding: 0 20px;
  }
}
</style>
