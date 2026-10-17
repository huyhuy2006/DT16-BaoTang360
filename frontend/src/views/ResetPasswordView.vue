<template>
  <div class="container narrow">
    <h2>Đặt lại mật khẩu</h2>
    <form class="card" @submit.prevent="submit">
      <input v-model="form.password" type="password" placeholder="Mật khẩu mới" required />
      <input v-model="form.password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới" required />
      <p v-if="error" class="error">{{ error }}</p>
      <button class="btn-primary" type="submit">Đặt lại mật khẩu</button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const error = ref("");

const form = reactive({
  password: "",
  password_confirmation: "",
  token: route.query.token,
  email: route.query.email,
});

async function submit() {
  error.value = "";
  try {
    await auth.resetPassword({ ...form });
    router.push({ name: "login" });
  } catch (e) {
    error.value = "Liên kết không hợp lệ hoặc đã hết hạn.";
  }
}
</script>
