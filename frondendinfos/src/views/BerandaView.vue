<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { database } from '../firebase';
import { ref as dbRef, onValue, push, set } from "firebase/database";

const router = useRouter();

// State
const viewMode = ref('index'); // 'index' or 'detail'
const currentCategory = ref('libur');
const selectedInfo = ref(null);
const currentUser = ref(null);

// Data
const categories = [
  { id: 'libur', name: 'Info Libur Sekolah' },
  { id: 'kegiatan', name: 'Kegiatan Sekolah' },
  { id: 'rapot', name: 'Jadwal Pengambilan Rapot' }
];

const infos = ref({
  libur: [],
  kegiatan: [],
  rapot: []
});

const comments = ref([]);

const newComment = ref('');

// Computed
const currentInfos = computed(() => infos.value[currentCategory.value] || []);
const categoryName = computed(() => categories.find(c => c.id === currentCategory.value)?.name);

// Methods
async function fetchBeranda() {
  try {
    const response = await axios.get('/', {
      headers: {
        'Accept': 'application/json'
      }
    });
    
    if (response.data && response.data.status === 'success') {
      const data = response.data.data;
      
      const formatItem = (item) => {
        const dateObj = new Date(item.tanggal);
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        
        return {
          id: item.id,
          title: item.judul,
          description: item.deskripsi,
          gambar: item.gambar,
          file_lampiran: item.file_lampiran,
          dateLabel: dateObj.toLocaleDateString('id-ID', dateOptions)
        };
      };

      infos.value.libur = data.info_libur.map(formatItem);
      infos.value.kegiatan = data.kegiatan_sekolah.map(formatItem);
      infos.value.rapot = data.jadwal_rapot.map(formatItem);
    }


  } catch (error) {
    console.error('Failed to fetch data from Laravel:', error);
  }

  // Fetch current user if logged in
  try {
    const resUser = await axios.get('/user');
    if (resUser.data && resUser.data.name) {
      currentUser.value = resUser.data;
    }
  } catch (err) {
    // Not logged in, that's fine
    currentUser.value = null;
  }
}

onMounted(() => {
  fetchBeranda();
});

// Methods
function setCategory(id) {
  currentCategory.value = id;
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
      // Urutkan komentar dari yang terbaru
      loadedComments.sort((a, b) => b.timestamp - a.timestamp);
      comments.value = loadedComments;
    } else {
      comments.value = [];
    }
  }, (error) => {
    console.error('Failed to fetch comments', error);
  });
}

function openDetail(item) {
  selectedInfo.value = item;
  viewMode.value = 'detail';
  fetchComments(item.id, currentCategory.value);
}

function goBack() {
  if (commentsUnsubscribe) {
    commentsUnsubscribe();
    commentsUnsubscribe = null;
  }
  selectedInfo.value = null;
  viewMode.value = 'index';
}

function goHome() {
  router.push('/');
}

function getImageUrl(path) {
  if (!path) return '';
  return `http://localhost:8080/storage/${path}`;
}

async function addComment() {
  if (newComment.value.trim() && selectedInfo.value) {
    try {
      const commenterName = currentUser.value ? currentUser.value.name : 'Siswa / Orang Tua';
      const commentsListRef = dbRef(database, `comments/${currentCategory.value}_${selectedInfo.value.id}`);
      const newCommentRef = push(commentsListRef);
      
      await set(newCommentRef, {
        author: commenterName,
        text: newComment.value.trim(),
        timestamp: Date.now()
      });
      
      newComment.value = '';
    } catch (err) {
      console.error(err);
      alert('Gagal mengirim komentar');
    }
  }
}
</script>

<template>
  <div class="user-page">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <div class="brand" style="display: flex; align-items: center; gap: 15px; cursor: pointer;" @click="router.push('/')">
          <img src="../assets/logo.png" alt="Logo SMK" style="height: 40px; width: auto;" />
          <img src="../assets/Vokasi-Indonesia.png" alt="Logo Vokasi" style="height: 40px; width: auto;" />
          <span class="brand-text">SMK Tunas Harapan Pati</span>
        </div>
        <div class="nav-action" style="display: flex; gap: 20px; align-items: center;">
          <span class="profile-label" @click="router.push('/cek-administrasi')">Cek Administrasi</span>
          <span 
            v-if="currentUser && currentUser.email === 'admin@gmail.com'" 
            class="profile-label" 
            @click="router.push('/admin')"
          >
            Dashboard Admin
          </span>
          <span v-else class="profile-label" @click="router.push('/profile')">Profile</span>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      
      <!-- === INDEX VIEW === -->
      <div v-if="viewMode === 'index'" class="index-layout">
        <!-- Sidebar / Categories -->
        <div class="sidebar">
          <button 
            v-for="cat in categories" 
            :key="cat.id" 
            :class="['category-btn', { active: currentCategory === cat.id }]"
            @click="setCategory(cat.id)"
          >
            {{ cat.name }}
          </button>
        </div>

        <!-- Info List Content -->
        <div class="info-content">
          <div class="title-box">
            <h2>Pengumuman Terbaru</h2>
          </div>
          
          <div class="info-list">
            <div v-for="item in currentInfos" :key="item.id" class="info-item-wrapper">
              <div class="date-label">{{ item.dateLabel }}</div>
              <div class="info-card clickable" @click="openDetail(item)">
                <span class="info-title">{{ item.title }}</span>
              </div>
            </div>
            
            <div v-if="currentInfos.length === 0" class="empty-state">
              Tidak ada pengumuman saat ini.
            </div>
          </div>
        </div>
      </div>

      <!-- === DETAIL VIEW === -->
      <div v-if="viewMode === 'detail' && selectedInfo" class="view-container">
        <div class="header-row">
          <h1 class="page-title">{{ categoryName }}</h1>
          <p class="page-subtitle">Deskripsi informasi</p>
        </div>

        <div class="content-box">
          <div class="detail-content">
            <div class="detail-date">{{ selectedInfo.dateLabel }}</div>
            <h2 class="detail-title">{{ selectedInfo.title }}</h2>
            <div v-if="selectedInfo.gambar" class="detail-image" style="margin: 15px 0; text-align: center;">
              <img :src="getImageUrl(selectedInfo.gambar)" alt="Gambar Informasi" style="max-width: 100%; max-height: 500px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" />
            </div>
            <div class="detail-desc">
              <p>{{ selectedInfo.description }}</p>
            </div>
            <div v-if="selectedInfo.file_lampiran" class="detail-attachment" style="margin-top: 20px;">
              <a :href="getImageUrl(selectedInfo.file_lampiran)" target="_blank" download class="btn btn-blue" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download File Lampiran
              </a>
            </div>
          </div>
        </div>

        <!-- Comments Section -->
        <div class="content-box comments-section">
          <div class="comments-layout">
            <div class="comments-left">
              <div class="comments-header">
                <h2>Komen/pertanyaan</h2>
              </div>
              <!-- Input Komen Tambahan -->
              <div class="comment-input-area">
                <textarea v-model="newComment" placeholder="Tulis komentar atau pertanyaan..." class="comment-input" rows="3"></textarea>
                <button class="btn btn-blue" @click="addComment">Kirim</button>
              </div>
            </div>
            
            <div class="comments-list">
              <div v-for="comment in comments" :key="comment.id" class="comment-card">
                <div class="comment-avatar">
                  {{ comment.author ? comment.author.charAt(0).toUpperCase() : '?' }}
                </div>
                <div class="comment-content">
                  <span class="comment-author">{{ comment.author }}</span>
                  <p class="comment-text">{{ comment.text }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Nav Controls -->
      <div class="bottom-nav-controls" v-if="viewMode === 'detail'">
        <button class="btn btn-outline nav-btn" @click="goBack">&larr; Kembali ke Daftar</button>
      </div>

    </main>
  </div>
</template>

<style scoped>
.user-page {
  min-height: 100vh;
  background-color: var(--bg-white);
  display: flex;
  flex-direction: column;
}

/* --- NAVBAR --- */
.navbar {
  background-color: var(--gray-bg);
  height: 70px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  border-bottom: 1px solid #e9ecef;
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

.profile-label {
  font-size: 16px;
  font-weight: 500;
  color: #555;
  cursor: pointer;
}

.profile-label:hover {
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

/* --- INDEX LAYOUT --- */
.index-layout {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 60px;
  align-items: start;
}

.sidebar {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.category-btn {
  background-color: var(--bg-white);
  color: var(--text-dark);
  font-size: 18px;
  font-weight: 600;
  padding: 20px;
  border-radius: 6px;
  text-align: left;
  transition: all 0.2s;
  border: 1px solid #dee2e6;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.category-btn:hover {
  border-color: var(--primary-blue);
  background-color: #f8f9fa;
}

.category-btn.active {
  background-color: var(--primary-blue);
  color: white;
}

.info-content {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.title-box {
  background-color: var(--bg-white);
  border: 1px solid #ccc;
  padding: 15px 30px;
  border-radius: 4px;
  display: inline-block;
  align-self: flex-start;
}

.title-box h2 {
  font-size: 22px;
  font-weight: 500;
  color: var(--text-dark);
}

/* --- SHARED INFO STYLES --- */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.info-item-wrapper {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.date-label {
  font-size: 16px;
  font-weight: 500;
  color: #444;
}

.info-card {
  background-color: var(--bg-white);
  padding: 20px 25px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
  border: 1px solid #dee2e6;
  border-left: 5px solid var(--primary-orange);
}

.info-card.clickable {
  cursor: pointer;
}

.info-card.clickable:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.08);
  border-left-color: var(--primary-blue);
}

.info-title {
  font-size: 20px;
  font-weight: 600;
  color: var(--text-dark);
}

.empty-state {
  color: #888;
  font-style: italic;
  font-size: 16px;
  margin-top: 20px;
}

/* --- DETAIL VIEW --- */
.view-container {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.header-row {
  margin-bottom: 10px;
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

.content-box {
  background-color: var(--gray-bg);
  border-radius: 8px;
  padding: 30px;
}

.detail-content {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.detail-date {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.detail-title {
  font-size: 26px;
  font-weight: 600;
  color: var(--text-dark);
}

.detail-desc {
  font-size: 16px;
  line-height: 1.6;
  color: #444;
  margin-top: 10px;
  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  border: 1px solid #e9ecef;
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

.comments-left {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.comments-header h2 {
  font-size: 20px;
  font-weight: 600;
  background-color: #f8f9fa;
  color: var(--primary-blue);
  padding: 15px 20px;
  border-radius: 6px;
  border-left: 4px solid var(--primary-blue);
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
  align-items: flex-start;
  gap: 15px;
  background-color: #fff;
  border: 1px solid #eaeaea;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
  transition: transform 0.2s;
}

.comment-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-blue), #4a90e2);
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
  background-color: white;
}

/* Responsive Overrides */
@media (max-width: 992px) {
  .index-layout {
    grid-template-columns: 1fr;
    gap: 40px;
  }
}

@media (max-width: 768px) {
  .comments-layout {
    grid-template-columns: 1fr;
    gap: 30px;
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
