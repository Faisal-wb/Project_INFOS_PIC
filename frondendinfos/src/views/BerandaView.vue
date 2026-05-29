<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

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

function openDetail(item) {
  selectedInfo.value = item;
  viewMode.value = 'detail';
  fetchComments(item.id, currentCategory.value);
}

function goBack() {
  selectedInfo.value = null;
  viewMode.value = 'index';
}

function goHome() {
  router.push('/');
}

async function addComment() {
  if (newComment.value.trim() && selectedInfo.value) {
    try {
      const commenterName = currentUser.value ? currentUser.value.name : 'Siswa / Orang Tua';
      await axios.post('/komentar', {
        nama: commenterName,
        isi: newComment.value.trim(),
        info_id: selectedInfo.value.id,
        kategori: currentCategory.value
      });
      newComment.value = '';
      await fetchComments(selectedInfo.value.id, currentCategory.value);
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
        <div class="brand">
          <span class="brand-text">SMK Tunas Harapan Pati</span>
        </div>
        <div class="nav-action">
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
            <div class="detail-desc">
              <p>{{ selectedInfo.description }}</p>
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
  flex-direction: column;
  background-color: #e9ecef;
  padding: 10px 15px;
  border-radius: 4px;
}

.comment-author {
  font-size: 12px;
  color: #555;
  font-weight: 500;
}

.comment-text {
  font-size: 14px;
  color: var(--text-dark);
  font-weight: 500;
  background-color: #e2e2e2;
  padding: 5px 8px;
  margin-top: 5px;
  display: inline-block;
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
