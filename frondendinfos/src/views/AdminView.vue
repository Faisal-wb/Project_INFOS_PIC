<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { database } from '../firebase';
import { ref as dbRef, onValue, push, set, remove } from "firebase/database";

const router = useRouter();

const viewMode = ref('list');
const activeTab = ref('informasi'); // 'informasi' or 'administrasi'

const infoList = ref([]);
const comments = ref([]);
const currentUser = ref(null);

const newComment = ref('');

const formData = ref({ id: null, kategori: 'libur', judul: '', tanggal: '', keterangan: '' });
const selectedFile = ref(null);
const selectedLampiran = ref(null);

// Administrasi state
const adminisKelas = ref('');
const adminisAsesmen = ref('');
const adminisTanggal = ref('');
const adminisFile = ref(null);
const adminisLoading = ref(false);
const administrasiList = ref([]);
const administrasiFilterKelas = ref('');
const expandedClasses = ref({});

function toggleKelas(kelas) {
  expandedClasses.value[kelas] = !expandedClasses.value[kelas];
}

const groupedAdministrasi = computed(() => {
  const groups = {};
  administrasiList.value.forEach(item => {
    const key = `${item.kelas}|${item.asesmen}`;
    if (!groups[key]) {
      groups[key] = [];
    }
    groups[key].push(item);
  });
  return groups;
});

async function fetchAdministrasiData() {
  try {
    const url = administrasiFilterKelas.value 
      ? `/admin/administrasi?kelas=${encodeURIComponent(administrasiFilterKelas.value)}`
      : '/admin/administrasi';
    const res = await axios.get(url);
    if (res.data.status === 'success') {
      administrasiList.value = res.data.data;
    }
  } catch (err) {
    console.error('Failed to fetch administrasi data', err);
  }
}

async function deleteAdministrasiBatch(key) {
  const parts = key.split('|');
  const kelas = parts[0];
  const asesmen = parts[1];
  if (confirm(`Yakin ingin menghapus SEMUA data administrasi untuk kelas ${kelas} pada asesmen ${asesmen}?`)) {
    try {
      await axios.delete('/admin/administrasi/batch', { data: { kelas, asesmen } });
      alert('Data berhasil dihapus');
      fetchAdministrasiData();
    } catch (err) {
      console.error(err);
      alert('Gagal menghapus data batch');
    }
  }
}

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
  fetchAdministrasiData();
});

function goToAdd() {
  formData.value = { id: null, kategori: 'libur', judul: '', tanggal: '', keterangan: '' };
  selectedFile.value = null;
  selectedLampiran.value = null;
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
  selectedFile.value = null;
  selectedLampiran.value = null;
  viewMode.value = 'edit';
  fetchComments(item.id, item.kategori);
}

function handleFileChange(event) {
  selectedFile.value = event.target.files[0];
}

function handleLampiranChange(event) {
  selectedLampiran.value = event.target.files[0];
}

function handleAdminisFileChange(event) {
  adminisFile.value = event.target.files[0];
}

async function uploadAdministrasi() {
  if (!adminisKelas.value || !adminisAsesmen.value || !adminisTanggal.value || !adminisFile.value) {
    alert('Kelas, Asesmen, Tanggal, dan file Excel harus diisi!');
    return;
  }
  
  adminisLoading.value = true;
  try {
    const payload = new FormData();
    payload.append('kelas', adminisKelas.value);
    payload.append('asesmen', adminisAsesmen.value);
    payload.append('tanggal', adminisTanggal.value);
    payload.append('file', adminisFile.value);
    
    const res = await axios.post('/admin/administrasi/import', payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    alert(res.data.message || 'Berhasil upload data administrasi');
    adminisKelas.value = '';
    adminisAsesmen.value = '';
    adminisTanggal.value = '';
    adminisFile.value = null;
    // reset file input
    document.getElementById('adminisFile').value = '';
    
    // Refresh the table
    fetchAdministrasiData();
  } catch (err) {
    console.error(err);
    alert('Gagal mengupload data: ' + (err.response?.data?.message || err.message));
  } finally {
    adminisLoading.value = false;
  }
}

let commentsUnsubscribe = null;

function fetchComments(infoId, kategori) {
  const commentsRef = dbRef(database, `comments/${kategori}_${infoId}`);
  
  if (commentsUnsubscribe) {
    commentsUnsubscribe();
  }
  
  commentsUnsubscribe = onValue(commentsRef, (snapshot) => {
    const data = snapshot.val();
    if (data) {
      const loadedComments = [];
      for (const key in data) {
        loadedComments.push({
          id: key,
          author: data[key].author,
          text: data[key].text,
          timestamp: data[key].timestamp
        });
      }
      loadedComments.sort((a, b) => b.timestamp - a.timestamp);
      comments.value = loadedComments;
    } else {
      comments.value = [];
    }
  }, (error) => {
    console.error('Failed to fetch comments', error);
  });
}

function goToList() {
  if (commentsUnsubscribe) {
    commentsUnsubscribe();
    commentsUnsubscribe = null;
  }
  viewMode.value = 'list';
}

async function savePost() {
  try {
    const url = `/admin/${formData.value.kategori}`;
    const payload = new FormData();
    
    payload.append('judul', formData.value.judul);
    payload.append('tanggal', formData.value.tanggal);
    payload.append('keterangan', formData.value.keterangan);
    payload.append('deskripsi', formData.value.keterangan);
    
    if (selectedFile.value) {
      payload.append('gambar', selectedFile.value);
    }
    
    if (selectedLampiran.value) {
      payload.append('file_lampiran', selectedLampiran.value);
    }

    if (viewMode.value === 'add') {
      await axios.post(url, payload, { headers: { 'Content-Type': 'multipart/form-data' }});
    } else {
      payload.append('_method', 'PUT');
      await axios.post(`${url}/${formData.value.id}`, payload, { headers: { 'Content-Type': 'multipart/form-data' }});
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
      const commentRef = dbRef(database, `comments/${formData.value.kategori}_${formData.value.id}/${id}`);
      await remove(commentRef);
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
      const commentsListRef = dbRef(database, `comments/${formData.value.kategori}_${formData.value.id}`);
      const newCommentRef = push(commentsListRef);
      
      await set(newCommentRef, {
        author: commenterName,
        text: newComment.value.trim(),
        timestamp: Date.now()
      });
      
      newComment.value = '';
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
        <div class="brand" style="display: flex; align-items: center; gap: 15px;">
          <img src="../assets/logo.png" alt="Logo SMK" style="height: 40px; width: auto;" />
          <img src="../assets/Vokasi-Indonesia.png" alt="Logo Vokasi" style="height: 40px; width: auto;" />
          <span class="brand-text">SMK Tunas Harapan Pati</span>
        </div>
        <div class="nav-action">
          <span class="admin-label clickable-profile" @click="router.push('/profile')">Admin</span>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
      
      <!-- Top Tabs -->
      <div class="admin-tabs">
        <button :class="['tab-btn', { active: activeTab === 'informasi' }]" @click="activeTab = 'informasi'">
          Manajemen Informasi
        </button>
        <button :class="['tab-btn', { active: activeTab === 'administrasi' }]" @click="activeTab = 'administrasi'">
          Data Administrasi
        </button>
      </div>

      <!-- === INFORMASI TAB === -->
      <div v-if="activeTab === 'informasi'">
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
            <label>Gambar Info (Opsional)</label>
            <input type="file" @change="handleFileChange" accept="image/*" class="form-input" style="padding: 9px 12px;" />
          </div>
          
          <div class="form-group">
            <label>File Lampiran (PDF/Doc - Opsional)</label>
            <input type="file" @change="handleLampiranChange" accept=".pdf,.doc,.docx,.xls,.xlsx" class="form-input" style="padding: 9px 12px;" />
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
                <div style="display: flex; gap: 15px; align-items: flex-start;">
                  <div class="comment-avatar">
                    {{ comment.author ? comment.author.charAt(0).toUpperCase() : '?' }}
                  </div>
                  <div class="comment-content">
                    <span class="comment-author">{{ comment.author }}</span>
                    <p class="comment-text">{{ comment.text }}</p>
                  </div>
                </div>
                <button class="btn btn-red btn-sm" @click="deleteComment(comment.id)">Hapus</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div> <!-- Close activeTab === informasi -->

      <!-- === ADMINISTRASI TAB === -->
      <div v-if="activeTab === 'administrasi'" class="view-container form-view">
        <div class="form-card">
          <h2 class="form-title">Upload Data Administrasi</h2>
          <p style="margin-bottom: 20px; color: #555;">Upload file Excel berisi data pembayaran. Pastikan ada kolom "nis", "nama", "status_bayar".</p>
          
          <div class="form-group">
            <label>Kelas</label>
            <input type="text" v-model="adminisKelas" placeholder="Contoh: X RPL 1" class="form-input" />
          </div>
          
          <div class="form-group">
            <label>Asesmen</label>
            <select v-model="adminisAsesmen" class="form-input">
              <option value="" disabled>Pilih Asesmen</option>
              <option value="ASTS (Asesmen Sumatif Tengah Semester)">ASTS (Asesmen Sumatif Tengah Semester)</option>
              <option value="ASAS (Asesmen Sumatif Akhir Semester)">ASAS (Asesmen Sumatif Akhir Semester)</option>
              <option value="ASAT (Asesmen Sumatif Akhir Tahun)">ASAT (Asesmen Sumatif Akhir Tahun)</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Tanggal Tagihan</label>
            <input type="date" v-model="adminisTanggal" class="form-input" />
          </div>
          
          <div class="form-group">
            <label>File Excel</label>
            <input type="file" id="adminisFile" @change="handleAdminisFileChange" accept=".xlsx,.xls,.csv" class="form-input" style="padding: 9px 12px;" />
          </div>
          
          <div class="form-actions">
            <button class="btn btn-blue" @click="uploadAdministrasi" :disabled="adminisLoading">
              {{ adminisLoading ? 'Mengupload...' : 'Upload Data' }}
            </button>
          </div>
        </div>

        <div class="content-box" style="margin-top: 30px;">
          <div class="header-action-row" style="margin-bottom: 20px;">
            <h2 class="form-title" style="margin-bottom: 0;">Daftar Data Administrasi</h2>
            <div style="display: flex; gap: 10px; align-items: center;">
              <input type="text" v-model="administrasiFilterKelas" placeholder="Filter Kelas..." class="form-input" style="padding: 8px 12px; width: 150px;" @keyup.enter="fetchAdministrasiData" />
              <button class="btn btn-blue" @click="fetchAdministrasiData">Filter</button>
            </div>
          </div>
          
          <div style="overflow-x: auto;">
            <template v-if="Object.keys(groupedAdministrasi).length > 0">
              <div v-for="(items, key) in groupedAdministrasi" :key="key" style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f8f9fa; padding: 10px 15px; border-radius: 4px; border-left: 4px solid var(--primary-blue); margin-bottom: 10px; cursor: pointer;" @click="toggleKelas(key)">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 14px; font-weight: bold; color: #555; width: 15px; text-align: center;">{{ expandedClasses[key] ? '▼' : '▶' }}</span>
                    <h3 style="margin: 0; font-size: 18px; color: #333;">Kelas: {{ key.split('|')[0] }} - {{ key.split('|')[1] }}</h3>
                  </div>
                  <button class="btn btn-red" style="padding: 4px 10px; font-size: 12px;" @click.stop="deleteAdministrasiBatch(key)">Hapus Data Ini</button>
                </div>
                <table class="data-table" v-if="expandedClasses[key]">
                  <thead>
                    <tr>
                      <th>NIS</th>
                      <th>Nama Siswa</th>
                      <th>Asesmen</th>
                      <th>Tanggal Tagihan</th>
                      <th>Status Bayar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in items" :key="item.id">
                      <td>{{ item.nis }}</td>
                      <td>{{ item.nama_siswa }}</td>
                      <td>{{ item.asesmen || '-' }}</td>
                      <td>{{ item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-' }}</td>
                      <td>
                        <span class="status-badge" :class="item.status_bayar.toLowerCase().includes('belum') ? 'belum' : 'lunas'">
                          {{ item.status_bayar }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </template>
            <div v-else style="text-align: center; padding: 20px; color: #777;">
              Tidak ada data ditemukan.
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
.admin-tabs {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
  border-bottom: 1px solid #ddd;
  padding-bottom: 10px;
}

.tab-btn {
  background: none;
  border: none;
  font-size: 18px;
  font-weight: 600;
  color: #777;
  padding: 10px 15px;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: all 0.2s;
}

.tab-btn:hover {
  color: var(--primary-blue);
}

.tab-btn.active {
  color: var(--primary-blue);
  border-bottom-color: var(--primary-blue);
}

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
  align-items: flex-start;
  background-color: #fff;
  border: 1px solid #eaeaea;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
}

.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-orange), #ff8c42);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: bold;
  flex-shrink: 0;
}

.comment-content {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.comment-author {
  font-size: 14px;
  color: #555;
  font-weight: 600;
}

.comment-text {
  font-size: 15px;
  color: var(--text-dark);
  line-height: 1.5;
  margin: 0;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
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

/* --- TABLE STYLES --- */
.data-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.data-table th, .data-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.data-table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #333;
}

.data-table tr:hover {
  background-color: #f1f3f5;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}
.status-badge.lunas {
  background-color: #d4edda;
  color: #155724;
}
.status-badge.belum {
  background-color: #f8d7da;
  color: #721c24;
}
</style>
