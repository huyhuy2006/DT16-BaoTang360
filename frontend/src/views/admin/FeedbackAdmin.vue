<template>
  <section>
    <h2>Kiểm duyệt đánh giá và phản hồi</h2>
    <p class="muted">Chỉ đánh giá ở trạng thái “Đã duyệt” mới hiển thị công khai.</p>
    <p v-if="error" class="error" role="alert">{{ error }}</p>
    <table class="admin-table">
      <thead><tr><th>Người dùng</th><th>Đối tượng</th><th>Sao</th><th>Nhận xét</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
      <tbody><tr v-for="item in feedback" :key="item.id">
        <td>{{ item.user?.name || `#${item.user_id}` }}</td><td>{{ item.target_type }} #{{ item.target_id }}</td><td>{{ item.rating }}/5</td><td class="comment">{{ item.comment }}</td><td>{{ label(item.status) }}</td>
        <td class="actions"><button @click="moderate(item,'approved')">Duyệt</button><button @click="moderate(item,'rejected')">Từ chối</button><button @click="moderate(item,'pending')">Chờ duyệt</button></td>
      </tr></tbody>
    </table>
    <p v-if="!feedback.length">Chưa có phản hồi cần kiểm duyệt.</p>
  </section>
</template>
<script setup>
import { onMounted, ref } from 'vue';import apiClient from '../../api/client';
const feedback=ref([]);const error=ref('');
const labels={pending:'Chờ duyệt',approved:'Đã duyệt',rejected:'Từ chối'};function label(status){return labels[status]||status;}
async function load(){try{const response=await apiClient.get('/feedback/moderation');feedback.value=response.data.data||response.data;}catch(err){error.value=err?.response?.data?.message||'Không thể tải phản hồi.';}}
async function moderate(item,status){error.value='';try{await apiClient.put(`/feedback/${item.id}/status`,{status});await load();}catch(err){error.value=err?.response?.data?.message||'Không thể đổi trạng thái phản hồi.';}}
onMounted(load);
</script>
<style scoped>.comment{max-width:360px;white-space:normal}.actions{display:flex;gap:4px;flex-wrap:wrap}.error{color:#b42318}.admin-table{width:100%;border-collapse:collapse}.admin-table th,.admin-table td{border-bottom:1px solid var(--color-border);padding:8px;text-align:left;vertical-align:top}</style>
