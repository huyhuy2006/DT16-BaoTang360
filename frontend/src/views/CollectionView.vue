<template>
  <section class="container">
    <h1>Bộ sưu tập của tôi</h1>
    <p class="muted">Các hiện vật đã lưu và tour được yêu thích được lưu theo tài khoản hiện tại.</p>
    <p v-if="error" class="error" role="alert">{{ error }}</p>

    <h2>Hiện vật đã lưu</h2>
    <div v-if="collectionItems.length" class="item-grid">
      <article v-for="item in collectionItems" :key="item.id" class="card">
        <img v-if="item.artifact?.image" :src="storageUrl(item.artifact.image)" :alt="item.artifact.name" loading="lazy" />
        <h3>{{ item.artifact?.name || 'Hiện vật không còn tồn tại' }}</h3>
        <p>{{ item.artifact?.description }}</p>
        <button @click="removeCollectionItem(item)">Bỏ lưu</button>
      </article>
    </div>
    <p v-else>Chưa có hiện vật nào được lưu bằng nút trong tour 360°.</p>

    <h2>Tour và mục yêu thích</h2>
    <div v-if="favorites.length" class="item-grid">
      <article v-for="favorite in favorites" :key="favorite.id" class="card">
        <img v-if="favorite.target?.cover_image" :src="storageUrl(favorite.target.cover_image)" :alt="favorite.target.name" loading="lazy" />
        <img v-else-if="favorite.target?.image" :src="storageUrl(favorite.target.image)" :alt="favorite.target.name" loading="lazy" />
        <h3>{{ favorite.target?.name || 'Mục yêu thích không còn tồn tại' }}</h3>
        <p class="muted">{{ favorite.target_type === 'tour' ? 'Tour tham quan' : 'Hiện vật' }}</p>
        <RouterLink v-if="favorite.target_type === 'tour' && favorite.target" :to="{name:'tour-player',params:{id:favorite.target.id}}">Mở tour</RouterLink>
        <button @click="removeFavorite(favorite)">Bỏ yêu thích</button>
      </article>
    </div>
    <p v-else>Chưa có tour hoặc mục yêu thích nào.</p>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import apiClient from '../api/client';
const collectionItems = ref([]); const favorites = ref([]); const error = ref('');
function storageUrl(path) { const base=import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8000/storage'; return path?.startsWith?.('http') ? path : `${base}/${path || ''}`; }
async function load() {
  error.value = '';
  try {
    const [collection, favoriteResponse] = await Promise.all([apiClient.get('/collection-items'), apiClient.get('/favorites')]);
    collectionItems.value = collection.data; favorites.value = favoriteResponse.data;
  } catch (err) { error.value = err?.response?.data?.message || 'Không thể tải bộ sưu tập.'; }
}
async function removeCollectionItem(item) { await apiClient.delete(`/collection-items/${item.id}`); await load(); }
async function removeFavorite(item) { await apiClient.delete(`/favorites/${item.target_type}/${item.target_id}`); await load(); }
onMounted(load);
</script>

<style scoped>.item-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px}.item-grid img{width:100%;max-height:180px;object-fit:cover;border-radius:8px}.item-grid button{margin-top:8px}.error{color:#b42318}</style>
