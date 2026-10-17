<template>
  <section>
    <h2>Quản trị không gian tham quan</h2>
    <form class="card space-form" @submit.prevent="submit">
      <h3>{{ editingId ? 'Cập nhật không gian' : 'Thêm không gian mới' }}</h3>
      <label>Tên không gian<input v-model.trim="form.name" required maxlength="255" /></label>
      <label>Mô tả<textarea v-model.trim="form.description" rows="3"></textarea></label>
      <label>Ảnh bìa<input type="file" accept="image/*" @change="onFile" /></label>
      <p v-if="error" class="error" role="alert">{{ error }}</p>
      <div class="actions"><button class="btn-primary" type="submit">{{ editingId ? 'Lưu thay đổi' : 'Thêm không gian' }}</button><button v-if="editingId" type="button" @click="reset">Hủy sửa</button></div>
    </form>
    <table class="admin-table"><thead><tr><th>Tên</th><th>Số điểm nhìn</th><th>Hành động</th></tr></thead>
      <tbody><tr v-for="space in spaces" :key="space.id"><td>{{ space.name }}</td><td>{{ space.scenes?.length || 0 }}</td><td class="actions"><button @click="edit(space)">Sửa</button><button @click="remove(space)">Xóa</button></td></tr></tbody>
    </table>
  </section>
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue';import apiClient from '../../api/client';
const spaces=ref([]);const file=ref(null);const editingId=ref(null);const error=ref('');const form=reactive({name:'',description:''});
function onFile(event){file.value=event.target.files?.[0]||null;}
async function load(){const response=await apiClient.get('/spaces');spaces.value=response.data;}
function edit(space){editingId.value=space.id;form.name=space.name||'';form.description=space.description||'';file.value=null;}
function reset(){editingId.value=null;form.name='';form.description='';file.value=null;error.value='';}
async function submit(){error.value='';const data=new FormData();data.append('name',form.name);data.append('description',form.description||'');if(file.value)data.append('cover_image',file.value);let url='/spaces';if(editingId.value){url+=`/${editingId.value}`;data.append('_method','PUT');}try{await apiClient.post(url,data,{headers:{'Content-Type':'multipart/form-data'}});reset();await load();}catch(err){error.value=err?.response?.data?.message||'Không thể lưu không gian.';}}
async function remove(space){if(!window.confirm(`Xóa không gian “${space.name}”? Các điểm nhìn gắn với nó có thể bị xóa theo quan hệ dữ liệu.`))return;try{await apiClient.delete(`/spaces/${space.id}`);await load();}catch(err){error.value=err?.response?.data?.message||'Không thể xóa không gian.';}}
onMounted(load);
</script>
<style scoped>.space-form{display:grid;gap:10px;margin-bottom:16px}.space-form label{display:grid;gap:5px}.space-form input,.space-form textarea{box-sizing:border-box;width:100%;padding:9px;border:1px solid var(--color-border);border-radius:8px}.actions{display:flex;gap:8px;flex-wrap:wrap}.admin-table{width:100%;border-collapse:collapse}.admin-table th,.admin-table td{border-bottom:1px solid var(--color-border);padding:8px;text-align:left}.error{color:#b42318}</style>
