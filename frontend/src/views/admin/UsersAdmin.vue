<template>
  <section>
    <h2>Quản lý tài khoản</h2>
    <p class="muted">Chỉ tài khoản quản trị được đổi vai trò hoặc khóa/mở khóa người dùng khác.</p>
    <p v-if="error" class="error" role="alert">{{ error }}</p>
    <table class="admin-table">
      <thead><tr><th>Tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
      <tbody><tr v-for="user in users" :key="user.id">
        <td>{{ user.name }}</td><td>{{ user.email }}</td><td>
          <select v-model="roles[user.id]" :disabled="user.id === currentUserId"><option value="visitor">Khách</option><option value="editor">Biên tập viên</option><option value="admin">Quản trị</option></select>
        </td><td>{{ user.is_active ? 'Đang hoạt động' : 'Đã khóa' }}</td><td class="actions">
          <button :disabled="user.id === currentUserId || roles[user.id] === user.role" @click="saveRole(user)">Lưu vai trò</button>
          <button :disabled="user.id === currentUserId" @click="setActive(user,!user.is_active)">{{ user.is_active ? 'Khóa' : 'Mở khóa' }}</button>
        </td>
      </tr></tbody>
    </table>
  </section>
</template>
<script setup>
import { onMounted, reactive, ref } from 'vue';import { useAuthStore } from '../../stores/auth';import apiClient from '../../api/client';
const auth=useAuthStore();const users=ref([]);const roles=reactive({});const error=ref('');const currentUserId=auth.user?.id;
async function load(){const response=await apiClient.get('/users');users.value=response.data;for(const user of users.value)roles[user.id]=user.role;}
async function saveRole(user){error.value='';try{await apiClient.put(`/users/${user.id}/role`,{role:roles[user.id]});await load();}catch(err){error.value=err?.response?.data?.message||'Không thể cập nhật vai trò.';}}
async function setActive(user,is_active){error.value='';try{await apiClient.put(`/users/${user.id}/status`,{is_active});await load();}catch(err){error.value=err?.response?.data?.message||'Không thể cập nhật trạng thái.';}}
onMounted(load);
</script>
<style scoped>.admin-table{width:100%;border-collapse:collapse}.admin-table th,.admin-table td{border-bottom:1px solid var(--color-border);padding:8px;text-align:left}.actions{display:flex;gap:6px;flex-wrap:wrap}.error{color:#b42318}select{max-width:100%}</style>
