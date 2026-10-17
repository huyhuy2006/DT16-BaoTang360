<template>
  <header class="card app-header">
    <RouterLink to="/" class="brand">🏛 Bảo tàng ảo</RouterLink>

    <nav class="nav-links">
      <RouterLink to="/tours">{{ $t("nav.tours") }}</RouterLink>
      <RouterLink to="/hien-vat">Hiện vật</RouterLink>
      <RouterLink to="/so-luu-but">{{ $t("nav.guestbook") }}</RouterLink>
      <RouterLink to="/danh-gia">Đánh giá</RouterLink>
      <RouterLink v-if="isAuthenticated" to="/bo-suu-tap">{{ $t("nav.collection") }}</RouterLink>
      <RouterLink v-if="isAuthenticated" to="/tai-khoan">Tài khoản</RouterLink>
      <RouterLink v-if="isEditor" to="/quan-tri/khong-gian">{{ $t("nav.admin") }}</RouterLink>
    </nav>

    <div class="nav-actions">
      <LanguageSwitcher />
      <ThemeSwitcher />
      <RouterLink v-if="!isAuthenticated" to="/dang-nhap">{{ $t("nav.login") }}</RouterLink>
      <button v-else class="btn-primary" @click="handleLogout">{{ $t("nav.logout") }} ({{ user?.name }})</button>
    </div>
  </header>
</template>

<script setup>
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import ThemeSwitcher from "./ThemeSwitcher.vue";
import LanguageSwitcher from "./LanguageSwitcher.vue";

const auth = useAuthStore();
const { isAuthenticated, isEditor, user } = storeToRefs(auth);
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  router.push({ name: "home" });
}
</script>
