<template>
  <section class="container">
    <h1>Lộ trình tham quan theo chủ đề</h1>
    <div v-if="recommendations.length" class="recommendations card">
      <strong>Chủ đề gợi ý từ mô-đun Python</strong>
      <span v-for="topic in recommendations" :key="topic" class="topic-chip">{{ topic }}</span>
      <small class="muted">Nguồn xử lý: {{ recommendationEngine === 'python' ? 'Python service' : 'phương án dự phòng' }}</small>
    </div>
    <p v-if="error" class="error" role="alert">{{ error }}</p>
    <div class="tour-grid">
      <article v-for="tour in tours" :key="tour.id" class="card tour-card">
        <RouterLink :to="{ name: 'tour-player', params: { id: tour.id } }" class="tour-link">
          <img v-if="tour.cover_image" :src="storageUrl(tour.cover_image)" :alt="`Ảnh bìa ${tour.name}`" loading="lazy" decoding="async" />
          <h2>{{ tour.name }}</h2>
          <p>{{ tour.theme }}</p>
          <p class="muted">{{ tour.description }}</p>
          <small>{{ tour.steps_count }} điểm dừng chân</small>
        </RouterLink>
        <FavoriteButton target-type="tour" :target-id="tour.id" />
      </article>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '../api/client';
import FavoriteButton from '../components/FavoriteButton.vue';
const tours=ref([]);const recommendations=ref([]);const recommendationEngine=ref('fallback');const error=ref('');
function storageUrl(path){const base=import.meta.env.VITE_STORAGE_BASE_URL||'http://localhost:8000/storage';return path?.startsWith?.('http')?path:`${base}/${path||''}`;}
onMounted(async()=>{const [tourResponse,recommendationResponse]=await Promise.allSettled([apiClient.get('/tours'),apiClient.get('/recommendations')]);if(tourResponse.status==='fulfilled')tours.value=tourResponse.value.data;else error.value='Không thể tải danh sách tour.';if(recommendationResponse.status==='fulfilled'){recommendations.value=recommendationResponse.value.data.recommendations||[];recommendationEngine.value=recommendationResponse.value.data.engine||'fallback';}});
</script>

<style scoped>.tour-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}.tour-card{display:grid;gap:10px}.tour-link{text-decoration:none;color:inherit}.tour-card img{width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:8px}.recommendations{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px}.topic-chip{border:1px solid var(--color-border);border-radius:999px;padding:3px 8px;font-size:.9rem}.error{color:#b42318}</style>
