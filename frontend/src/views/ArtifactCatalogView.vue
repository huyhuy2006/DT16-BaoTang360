<template>
  <section class="container artifact-catalog">
    <header class="catalog-header">
      <div>
        <h1>Danh mục hiện vật</h1>
        <p class="muted">Tìm kiếm, xem chi tiết và lưu hiện vật yêu thích trong bảo tàng ảo.</p>
      </div>
      <form class="search-form" role="search" @submit.prevent="search">
        <label class="sr-only" for="artifact-query">Từ khóa hiện vật</label>
        <input id="artifact-query" v-model.trim="query" type="search" maxlength="120" placeholder="Tìm theo tên hiện vật…" />
        <button class="btn-primary" type="submit" :disabled="loading">Tìm kiếm</button>
        <button type="button" @click="clearSearch" :disabled="loading">Xóa lọc</button>
      </form>
    </header>

    <p v-if="error" class="error" role="alert">{{ error }}</p>
    <p v-if="loading" role="status">Đang tải danh mục…</p>
    <p v-else class="muted">{{ total }} hiện vật trong danh mục{{ query ? ` khớp với “${query}”` : '' }}.</p>

    <div v-if="artifacts.length" class="artifact-grid">
      <article v-for="artifact in artifacts" :key="artifact.id" class="card artifact-card">
        <img v-if="artifact.image" :src="storageUrl(artifact.image)" :alt="`Ảnh minh họa ${artifact.name}`" loading="lazy" decoding="async" />
        <div class="artifact-body">
          <h2>{{ artifact.name }}</h2>
          <p class="muted">{{ [artifact.period, artifact.material].filter(Boolean).join(' · ') }}</p>
          <p>{{ excerpt(artifact.description) }}</p>
          <div class="actions">
            <button type="button" @click="openDetails(artifact)">Xem chi tiết</button>
            <FavoriteButton target-type="artifact" :target-id="artifact.id" />
          </div>
        </div>
      </article>
    </div>
    <p v-else-if="!loading">Không tìm thấy hiện vật phù hợp. Hãy thử từ khóa khác.</p>

    <nav v-if="lastPage > 1" class="pagination" aria-label="Phân trang hiện vật">
      <button type="button" :disabled="page <= 1 || loading" @click="goToPage(page - 1)">Trang trước</button>
      <span>Trang {{ page }} / {{ lastPage }}</span>
      <button type="button" :disabled="page >= lastPage || loading" @click="goToPage(page + 1)">Trang sau</button>
    </nav>

    <div v-if="selected" class="details-backdrop" @click.self="selected = null">
      <article class="details-panel card" role="dialog" aria-modal="true" :aria-label="`Chi tiết ${selected.name}`">
        <button class="close-button" type="button" aria-label="Đóng chi tiết" @click="selected = null">Đóng ✕</button>
        <h2>{{ selected.name }}</h2>
        <img v-if="selected.image" :src="storageUrl(selected.image)" :alt="`Ảnh ${selected.name}`" class="detail-image" />
        <p v-if="selected.period"><strong>Niên đại:</strong> {{ selected.period }}</p>
        <p v-if="selected.material"><strong>Chất liệu:</strong> {{ selected.material }}</p>
        <p class="description">{{ selected.description || 'Chưa có mô tả cho hiện vật này.' }}</p>
        <video v-if="selected.video_url" :src="selected.video_url" controls preload="metadata" class="detail-video">Trình duyệt không hỗ trợ video.</video>
        <FavoriteButton target-type="artifact" :target-id="selected.id" />
        <section v-if="selected.narrations?.length" class="narrations">
          <h3>Thuyết minh đa ngữ</h3>
          <article v-for="narration in selected.narrations" :key="narration.id" class="narration-item">
            <strong>{{ narration.language?.name || 'Thuyết minh' }}</strong>
            <p v-if="narration.transcript">{{ narration.transcript }}</p>
            <audio v-if="narration.audio_path" controls preload="metadata" :src="storageUrl(narration.audio_path)">Trình duyệt không hỗ trợ âm thanh.</audio>
          </article>
        </section>
        <p v-else class="muted">Chưa có bản thuyết minh gắn trực tiếp với hiện vật.</p>
      </article>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import apiClient from '../api/client';
import FavoriteButton from '../components/FavoriteButton.vue';

const artifacts = ref([]);
const query = ref('');
const page = ref(1);
const lastPage = ref(1);
const total = ref(0);
const loading = ref(false);
const error = ref('');
const selected = ref(null);
const baseStorage = import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8080/storage';
function storageUrl(path) { return path?.startsWith?.('http') ? path : `${baseStorage}/${String(path || '').replace(/^\/+/, '')}`; }
function excerpt(text) { const value = String(text || '').trim(); return value.length > 150 ? `${value.slice(0,147)}…` : value; }
async function load() {
  loading.value = true; error.value = '';
  try {
    const response = await apiClient.get('/artifacts', { params: { q: query.value || undefined, page: page.value } });
    const data = response.data;
    artifacts.value = Array.isArray(data) ? data : (data.data || []);
    page.value = Number(data.current_page || page.value);
    lastPage.value = Number(data.last_page || 1);
    total.value = Number(data.total ?? artifacts.value.length);
  } catch (err) {
    error.value = err?.response?.data?.message || 'Không tải được danh mục hiện vật. Hãy kiểm tra API và dữ liệu mẫu.';
  } finally { loading.value = false; }
}
async function search() { page.value = 1; await load(); }
async function clearSearch() { query.value = ''; page.value = 1; await load(); }
async function goToPage(nextPage) { page.value = nextPage; await load(); }
async function openDetails(artifact) {
  selected.value = artifact;
  try { const response = await apiClient.get(`/artifacts/${artifact.id}`); selected.value = response.data; }
  catch { /* Keep the list summary available if detail API is temporarily unavailable. */ }
}
onMounted(load);
</script>

<style scoped>
.catalog-header{display:grid;gap:12px;margin-bottom:18px}.search-form{display:flex;gap:8px;flex-wrap:wrap}.search-form input{flex:1;min-width:180px;padding:10px;border:1px solid var(--color-border);border-radius:8px;background:var(--color-card);color:inherit}.artifact-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px}.artifact-card{padding:0;overflow:hidden}.artifact-card>img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover;background:var(--color-bg)}.artifact-body{padding:14px}.artifact-body h2{font-size:1.1rem;margin-top:0}.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.pagination{display:flex;justify-content:center;align-items:center;gap:12px;padding:20px}.error{color:#b42318}.details-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.62);display:flex;align-items:center;justify-content:center;padding:18px;z-index:90}.details-panel{position:relative;max-width:760px;width:100%;max-height:90vh;overflow:auto}.close-button{float:right}.detail-image{width:100%;max-height:390px;object-fit:contain;background:var(--color-bg);border-radius:8px}.description{white-space:pre-wrap}.detail-video{width:100%;max-height:360px;background:#000}.narrations{margin-top:20px}.narration-item{border-top:1px solid var(--color-border);padding:10px 0}.narration-item audio{display:block;width:100%;margin-top:8px}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}@media(max-width:560px){.details-backdrop{padding:8px}.details-panel{max-height:95vh}.search-form{display:grid;grid-template-columns:1fr 1fr}.search-form input{grid-column:1/-1}}
</style>
