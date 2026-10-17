<template>
  <div class="container narrow">
    <h2>Đăng ký</h2>
    <form class="card" @submit.prevent="submit">
      <input v-model="form.name" placeholder="Họ và tên" required />
      <input v-model="form.email" type="email" placeholder="Email" required />
      <input v-model="form.password" type="password" placeholder="Mật khẩu (tối thiểu 8 ký tự)" required />
      <input v-model="form.password_confirmation" type="password" placeholder="Nhập lại mật khẩu" required />
      <p v-if="error" class="error">{{ error }}</p>
      <button class="btn-primary" type="submit">Tạo tài khoản</button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const form = reactive({ name: "", email: "", password: "", password_confirmation: "" });
const error = ref("");
const router = useRouter();
const auth = useAuthStore();

async function submit() {
  error.value = "";
  try {
    await auth.register({ ...form });
    router.push({ name: "home" });
  } catch (e) {
    error.value = e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(" ") : "Đăng ký thất bại.";
  }
}
</script>
