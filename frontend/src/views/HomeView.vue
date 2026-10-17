<template>
  <div class="container">
    <section class="hero card">
      <h1>Bảo tàng ảo — Hành trình 360°</h1>
      <p>Khám phá hiện vật, nghe thuyết minh đa ngữ và tự viết nên hành trình của riêng bạn.</p>
      <!-- Dùng ảnh demo hoặc thay bằng hình ảnh có quyền sử dụng khi biên tập nội dung. -->
      <div class="hero-actions">
        <RouterLink class="btn-primary" :to="{ name: 'tours' }">Xem lộ trình gợi ý</RouterLink>
        <RouterLink class="btn-primary" :to="{ name: 'artifacts' }">Khám phá hiện vật</RouterLink>
        <RouterLink v-if="spaces.length" class="btn-primary" :to="{ name: 'free-explore', params: { spaceId: spaces[0].id } }">
          Tham quan tự do
        </RouterLink>
      </div>
    </section>

    <section>
      <h2>Không gian trưng bày</h2>
      <div class="space-grid">
        <RouterLink
          v-for="space in spaces"
          :key="space.id"
          :to="{ name: 'free-explore', params: { spaceId: space.id } }"
          class="card"
        >
          <h3>{{ space.name }}</h3>
          <p>{{ space.description }}</p>
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import apiClient from "../api/client";

const spaces = ref([]);

onMounted(async () => {
  const res = await apiClient.get("/spaces");
  spaces.value = res.data;
});
</script>

<style scoped>
.hero-actions { display: flex; gap: 12px; margin-top: 12px; }
.space-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
</style>
