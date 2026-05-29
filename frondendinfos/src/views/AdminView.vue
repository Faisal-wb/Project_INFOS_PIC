<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// 'list' | 'add' | 'edit'
const viewMode = ref('list');

const infoList = ref([]);
const comments = ref([]);
const currentUser = ref(null);

const newComment = ref('');

const formData = ref({ id: null, kategori: 'libur', judul: '', tanggal: '', keterangan: '' });

async function fetchAdminData() {
  try {
    const resLibur = await axios.get('/admin/libur');
    const resKegiatan = await axios.get('/admin/kegiatan');
    const resRapot = await axios.get('/admin/rapot');

    let all = [];
    if (resLibur.data.status === 'success') {
      all = all.concat(resLibur.data.data.map(i => ({...i, kategori: 'libur', dateLabel: new Date(i.tanggal).toLocaleDateString('id-ID', {weekday: 'long', day:'numeric', month:'long', year:'numeric'}), title: i.judul})));
    }
    if (resKegiatan.data.status === 'success') {
      all = all.concat(resKegiatan.data.data.map(i => ({...i, kategori: 'kegiatan', dateLabel: new Date(i.tanggal).toLocaleDateString('id-ID', {weekday: 'long', day:'numeric', month:'long', year:'numeric'}), title: i.judul})));
    }
    if (resRapot.data.status === 'success') {
      all = all.concat(resRapot.data.data.map(i => ({...i, kategori: 'rapot', dateLabel: new Date(i.tanggal).toLocaleDateString('id-ID', {weekday: 'long', day:'numeric', month:'long', year:'numeric'}), title: i.judul})));
    }

    all.sort((a,b) => new Date(b.tanggal) - new Date(a.tanggal));
    infoList.value = all;

    // Comments are now loaded per info in edit view

    const resUser = await axios.get('/user');
    if (resUser.data && resUser.data.name) {
      currentUser.value = resUser.data;
    }
  } catch (err) {
    console.error('Failed to fetch admin data or user', err);
  }
}

onMounted(() => {
  fetchAdminData();
});

function goToAdd() {
  formData.value = { id: null, kategori: 'libur', judul: '', tanggal: '', keterangan: '' };
  viewMode.value = 'add';
}

function goToEdit(item) {
  formData.value = {
    id: item.id,
    kategori: item.kategori,
    judul: item.title,
    tanggal: item.tanggal ? item.tanggal.substring(0, 10) : '',
    keterangan: item.deskripsi
  };
  viewMode.value = 'edit';
  fetchComments(item.id, item.kategori);
}

async function fetchComments(infoId, kategori) {
  try {
    const resComments = await axios.get(`/komentar?info_id=${infoId}&kategori=${kategori}`);
    if (resComments.data.status === 'success') {
      comments.value = resComments.data.data.map(c => ({
        id: c.id,
        author: c.nama,
        text: c.isi
      }));
    }
  } catch (err) {
    console.error('Failed to fetch comments', err);
  }
}

function goToList() {
  viewMode.value = 'list';
}

async function savePost() {
  try {
    const url = `/admin/${formData.value.kategori}`;
    const payload = {
      judul: formData.value.judul,
      tanggal: formData.value.tanggal,
      keterangan: formData.value.keterangan,
      deskripsi: formData.value.keterangan
    };

    if (viewMode.value === 'add') {
      await axios.post(url, payload);
    } else {
      await axios.put(`${url}/${formData.value.id}`, payload);
    }
    
    await fetchAdminData();
    goToList();
  } catch (err) {
    console.error(err);
    if (err.response && err.response.data && err.response.data.message) {
      alert('Error: ' + err.response.data.message);
    } else {
      alert('Error: ' + err.message);
    }
  }
}

async function deletePost() {
  if (formData.value.id) {
    if (confirm('Yakin ingin menghapus pengumuman ini?')) {
      try {
        await axios.delete(`/admin/${formData.value.kategori}/${formData.value.id}`);
        await fetchAdminData();
        goToList();
      } catch (err) {
        console.error(err);
        alert('Gagal menghapus data');
      }
    }
  } else {
    goToList();
  }
}

async function deleteComment(id) {
  if (confirm('Yakin ingin menghapus komentar ini?')) {
    try {
      await axios.delete(`/komentar/${id}`);
      if (formData.value.id) {
        await fetchComments(formData.value.id, formData.value.kategori);
      }
    } catch (err) {
      console.error(err);
      alert('Gagal menghapus komentar');
    }
  }
}

async function addComment() {
  if (newComment.value.trim() && formData.value.id) {
    try {
      const commenterName = currentUser.value ? currentUser.value.name : 'Admin';
      await axios.post('/komentar', {
        nama: commenterName,
        isi: newComment.value.trim(),
        info_id: formData.value.id,
        kategori: formData.value.kategori
      });
      newComment.value = '';
      await fetchComments(formData.value.id, formData.value.kategori);
    } catch (err) {
      console.error(err);
      alert('Gagal menambah komentar');
    }
  }
}

function handleKembali() {
  // Logic for '< Kembali'
  if (viewMode.value !== 'list') {
    goToList();
  } else {
    router.push('/beranda');
  }
}

function handleBeranda() {
  router.push('/beranda');
}
</script>

<template>
  <div class="admin-page">
    <!-- Admin Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <div class="brand">
          <span class="brand-text">SMK Tunas Harapan Pati</span>
        </div>
        <div class="nav-action">
          <span class="admin-label clickable-profile" @click="router.push('/profile')">Admin</span>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- === LIST VIEW === -->
      <div v-if="viewMode === 'list'" class="view-container">
        <div class="header-action-row">
          <div>
            <h1 class="page-title">Manajemen Informasi</h1>
            <p class="page-subtitle">Pilih kategori, tambah, edit, atau hapus info sekolah</p>
          </div>
          <button class="btn btn-blue" @click="goToAdd">Tambah</button>
        </div>

        <div class="content-box">
          <!-- Info List -->
          <div class="info-list">
            <div v-for="item in infoList" :key="item.id" class="info-item-wrapper">
              <div class="date-label">{{ item.dateLabel }}</div>
              <div class="info-card">
                <span class="info-title">{{ item.title }}</span>
                <button class="btn btn-blue" @click="goToEdit(item)">Edit</button>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- === ADD / EDIT VIEW === -->
      <div v-if="viewMode === 'add' || viewMode === 'edit'" class="view-container form-view">
        <div class="form-card">
          <h2 class="form-title">{{ viewMode === 'add' ? 'Tambahkan Info' : 'Edit' }}</h2>
          
          <div class="form-group">
            <label>Kategori</label>
            <select v-model="formData.kategori" class="form-input">
              <option value="libur">Info Libur Sekolah</option>
              <option value="kegiatan">Kegiatan Sekolah</option>
              <option value="rapot">Jadwal Pengambilan Rapot</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Judul</label>
            <input type="text" v-model="formData.judul" class="form-input" />
          </div>
          
          <div class="form-group">
            <label>Tanggal</label>
            <input type="date" v-model="formData.tanggal" class="form-input" />
          </div>
          
          <div class="form-group">
            <label>Keterangan</label>
            <textarea v-model="formData.keterangan" class="form-input textarea" rows="4"></textarea>
          </div>
          
          <div class="form-actions">
            <button class="btn btn-outline" @click="goToList">Batal</button>
            <button v-if="viewMode === 'edit'" class="btn btn-red" @click="deletePost">Delete</button>
            <button class="btn btn-blue" @click="savePost">Post</button>
          </div>
        </div>

        <div v-if="viewMode === 'edit'" class="content-box comments-section" style="max-width: 650px; width: 100%; margin-top: 20px;">
          <div class="comments-layout" style="grid-template-columns: 1fr;">
            <div class="comments-left">
              <div class="comments-header">
                <h2>Komen/pertanyaan</h2>
              </div>
              <div class="comment-input-area">
                <textarea v-model="newComment" placeholder="Tulis balasan atau komentar sebagai Admin..." class="comment-input" rows="3"></textarea>
                <button class="btn btn-blue" @click="addComment">Kirim</button>
              </div>
            </div>
            <div class="comments-list">
              <div v-for="comment in comments" :key="comment.id" class="comment-card">
                <div class="comment-content">
                  <span class="comment-author">{{ comment.author }}</span>
                  <p class="comment-text">{{ comment.text }}</p>
                </div>
                <button class="btn btn-red" @click="deleteComment(comment.id)">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Navigation Controls -->
      <div class="bottom-nav-controls">
        <button class="btn btn-outline nav-btn" @click="handleKembali">&larr; Kembali</button>
        <button class="btn btn-outline nav-btn" @click="handleBeranda">Beranda</button>
      </div>

    </main>
  </div>
</template>

<style scoped>
.admin-page {
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
}

.navbar-container {
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand-text {
  font-size: 18px;
  font-weight: 700;
  color: var(--text-dark);
}

.admin-label {
  font-size: 16px;
  font-weight: 500;
  color: #555;
}

.clickable-profile {
  cursor: pointer;
  transition: color 0.2s;
}

.clickable-profile:hover {
  color: var(--primary-blue);
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

.view-container {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* Header & Action Row */
.header-action-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

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

/* Content Boxes */
.content-box {
  background-color: var(--bg-white);
  border-radius: 8px;
  padding: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
}

/* Info List */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-item-wrapper {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.date-label {
  font-size: 15px;
  font-weight: 500;
  color: #444;
}

.info-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: var(--bg-white);
  border: 1px solid #dee2e6;
  border-left: 5px solid var(--primary-orange);
  padding: 15px 25px;
  border-radius: 6px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.info-card:hover {
  border-left-color: var(--primary-blue);
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.info-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--text-dark);
}

/* Comments Section */
.comments-section {
  background-color: var(--bg-white);
  border: 1px solid #dee2e6;
}

.comments-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
}

.comments-header h2 {
  font-size: 20px;
  font-weight: 600;
  background-color: #f8f9fa;
  color: var(--primary-blue);
  padding: 15px 20px;
  border-radius: 6px;
  display: inline-block;
  border-left: 4px solid var(--primary-blue);
}

.comments-left {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.comment-input-area {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.comment-input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-family: inherit;
  font-size: 14px;
  resize: vertical;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.comment-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #fff;
  border: 1px solid #e9ecef;
  padding: 12px 15px;
  border-radius: 6px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}

.comment-content {
  display: flex;
  flex-direction: column;
}

.comment-author {
  font-size: 12px;
  color: #888;
  font-weight: 500;
}

.comment-text {
  font-size: 14px;
  color: var(--text-dark);
  font-weight: 500;
}

/* Form View (Add/Edit) */
.form-view {
  align-items: center;
  padding-top: 20px;
}

.form-card {
  width: 100%;
  max-width: 650px;
  background-color: var(--bg-white);
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.form-title {
  font-size: 26px;
  font-weight: 600;
  color: var(--text-dark);
  margin-bottom: 30px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

.form-group label {
  font-size: 16px;
  font-weight: 500;
  color: #333;
}

.form-input {
  width: 100%;
  padding: 12px 15px;
  font-size: 16px;
  font-family: inherit;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #fafafa;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-blue);
  background-color: #fff;
}

.textarea {
  resize: vertical;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 30px;
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
  font-size: 16px;
  padding: 8px 24px;
}

/* Responsive Overrides */
@media (max-width: 768px) {
  .comments-layout {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .header-action-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
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
