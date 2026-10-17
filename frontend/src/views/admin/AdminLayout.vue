<template>
  <div class="admin-shell">
    <aside class="card admin-menu">
      <RouterLink :to="{ name: 'admin-spaces' }">Không gian &amp; điểm nhìn</RouterLink>
      <RouterLink :to="{ name: 'admin-artifacts' }">Hiện vật</RouterLink>
      <RouterLink :to="{ name: 'admin-tours' }">Lộ trình</RouterLink>
      <RouterLink v-if="isAdmin" :to="{ name: 'admin-stats' }">Thống kê</RouterLink>
      <RouterLink v-if="isAdmin" :to="{ name: 'admin-users' }">Người dùng</RouterLink>
      <RouterLink v-if="isAdmin" :to="{ name: 'admin-feedback' }">Đánh giá &amp; phản hồi</RouterLink>
    </aside>
    <section class="admin-content">
      <RouterView />
    </section>
  </div>
</template>

<script setup>
import { storeToRefs } from "pinia";
import { useAuthStore } from "../../stores/auth";

const { isAdmin } = storeToRefs(useAuthStore());
</script>

<style scoped>
.admin-shell { display: grid; grid-template-columns: 220px 1fr; gap: 16px; }
.admin-menu { display: flex; flex-direction: column; gap: 8px; height: fit-content; }
@media (max-width: 768px) { .admin-shell { grid-template-columns: 1fr; } }
</style>
