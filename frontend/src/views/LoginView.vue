<template>
  <div class="container narrow">
    <h2>Đăng nhập</h2>
    <form class="card" @submit.prevent="submit">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mật khẩu" required />
      <p v-if="error" class="error">{{ error }}</p>
      <button class="btn-primary" type="submit">Đăng nhập</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const email = ref("");
const password = ref("");
const error = ref("");
const router = useRouter();
const auth = useAuthStore();

async function submit() {
  error.value = "";
  try {
    await auth.login(email.value, password.value);
    router.push({ name: "home" });
  } catch (e) {
    error.value = "Email hoặc mật khẩu không đúng.";
  }
}
</script>

<style scoped>
form { display: flex; flex-direction: column; gap: 10px; }
.error { color: #c0392b; }
</style>
