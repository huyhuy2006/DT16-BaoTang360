<template>
  <div class="container narrow">
    <h2>Quên mật khẩu</h2>
    <form class="card" @submit.prevent="submit">
      <input v-model="email" type="email" placeholder="Email đã đăng ký" required />
      <button class="btn-primary" type="submit">Gửi liên kết đặt lại mật khẩu</button>
      <p v-if="sent">Nếu email tồn tại, liên kết đặt lại mật khẩu đã được gửi. Kiểm tra hộp thư của bạn.</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";

const email = ref("");
const sent = ref(false);
const auth = useAuthStore();

async function submit() {
  await auth.forgotPassword(email.value);
  sent.value = true;
}
</script>
