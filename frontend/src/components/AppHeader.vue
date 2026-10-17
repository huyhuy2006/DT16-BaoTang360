<template>
  <header class="card app-header">
    <RouterLink to="/" class="brand" aria-label="Bảo tàng ảo - Trang chủ">
      <span class="brand-icon" aria-hidden="true">🏛️</span>
      <span class="brand-copy">
        <strong>{{ $t("brand.name") }}</strong>
        <small>{{ $t("brand.tagline") }}</small>
      </span>
    </RouterLink>

    <nav class="nav-links" aria-label="Điều hướng chính">
      <RouterLink to="/">{{ $t("nav.home") }}</RouterLink>
      <RouterLink to="/tours">{{ $t("nav.tours") }}</RouterLink>
      <RouterLink to="/hien-vat">{{ $t("nav.artifacts") }}</RouterLink>
      <RouterLink to="/so-luu-but">{{ $t("nav.guestbook") }}</RouterLink>
      <RouterLink to="/danh-gia">{{ $t("nav.feedback") }}</RouterLink>
      <RouterLink v-if="isAuthenticated" to="/bo-suu-tap">{{ $t("nav.collection") }}</RouterLink>
      <RouterLink v-if="isAuthenticated" to="/tai-khoan">{{ $t("nav.profile") }}</RouterLink>
      <RouterLink v-if="isEditor" to="/quan-tri/khong-gian">{{ $t("nav.admin") }}</RouterLink>
    </nav>

    <div class="nav-actions">
      <LanguageSwitcher />
      <ThemeSwitcher />
      <RouterLink v-if="!isAuthenticated" to="/dang-nhap" class="login-link">
        {{ $t("nav.login") }}
      </RouterLink>
      <button
        v-else
        type="button"
        class="btn-primary logout-button"
        @click="handleLogout"
      >
        {{ $t("nav.logout") }}
      </button>
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
const { isAuthenticated, isEditor } = storeToRefs(auth);
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  await router.push({ name: "home" });
}
</script>