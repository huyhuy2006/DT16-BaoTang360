<template>
  <section class="container narrow">
    <h1>Thông tin tài khoản</h1>
    <form class="card profile-form" @submit.prevent="saveProfile">
      <label>Họ và tên<input v-model.trim="profile.name" required minlength="2" maxlength="255" autocomplete="name" /></label>
      <label>Email<input v-model.trim="profile.email" required type="email" maxlength="255" autocomplete="email" /></label>
      <p class="muted">Vai trò hiện tại: {{ user?.role || 'visitor' }}</p>
      <p v-if="message" class="success" role="status">{{ message }}</p>
      <p v-if="error" class="error" role="alert">{{ error }}</p>
      <button class="btn-primary" type="submit" :disabled="busy">{{ busy ? 'Đang lưu…' : 'Lưu thông tin' }}</button>
    </form>

    <h2>Đổi mật khẩu</h2>
    <form class="card profile-form" @submit.prevent="changePassword">
      <label>Mật khẩu hiện tại<input v-model="passwords.current_password" type="password" autocomplete="current-password" required /></label>
      <label>Mật khẩu mới<input v-model="passwords.password" type="password" autocomplete="new-password" minlength="8" required /></label>
      <label>Nhập lại mật khẩu mới<input v-model="passwords.password_confirmation" type="password" autocomplete="new-password" minlength="8" required /></label>
      <p v-if="passwordMessage" class="success" role="status">{{ passwordMessage }}</p>
      <p v-if="passwordError" class="error" role="alert">{{ passwordError }}</p>
      <button class="btn-primary" type="submit" :disabled="passwordBusy">Đổi mật khẩu</button>
    </form>
  </section>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import apiClient from '../api/client';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();
const { user } = storeToRefs(auth);
const profile = reactive({ name: '', email: '' });
const passwords = reactive({ current_password: '', password: '', password_confirmation: '' });
const busy = ref(false); const error = ref(''); const message = ref('');
const passwordBusy = ref(false); const passwordError = ref(''); const passwordMessage = ref('');

function responseMessage(err, fallback) {
  const errors = err?.response?.data?.errors;
  if (errors) return Object.values(errors).flat().join(' ');
  return err?.response?.data?.message || fallback;
}
async function loadProfile() {
  const response = await apiClient.get('/me');
  profile.name = response.data.name || '';
  profile.email = response.data.email || '';
  auth.user = response.data;
}
async function saveProfile() {
  busy.value = true; error.value = ''; message.value = '';
  try {
    const response = await apiClient.put('/me', { name: profile.name, email: profile.email });
    auth.user = response.data;
    message.value = 'Đã cập nhật thông tin tài khoản.';
  } catch (err) { error.value = responseMessage(err, 'Không thể lưu thông tin.'); }
  finally { busy.value = false; }
}
async function changePassword() {
  passwordBusy.value = true; passwordError.value = ''; passwordMessage.value = '';
  try {
    await auth.changePassword({ ...passwords });
    passwordMessage.value = 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.';
    // The API already revoked all tokens during the password-change request.
    auth.user = null; auth.token = null; localStorage.removeItem('auth_token');
    window.alert(passwordMessage.value);
    await router.push({ name: 'login' });
  } catch (err) { passwordError.value = responseMessage(err, 'Không thể đổi mật khẩu.'); }
  finally { passwordBusy.value = false; }
}
onMounted(loadProfile);
</script>

<style scoped>
.profile-form{display:grid;gap:12px;margin-bottom:24px}.profile-form label{display:grid;gap:6px}.profile-form input{width:100%;box-sizing:border-box;padding:10px;border:1px solid var(--color-border);border-radius:8px}.success{color:#187443}.error{color:#b42318}
</style>
