<template>
  <section>
    <h2>Quản trị lộ trình tham quan theo chủ đề</h2>
    <form class="card tour-form" @submit.prevent="submitTour">
      <h3>{{ editingTourId ? 'Cập nhật lộ trình' : 'Tạo lộ trình mới' }}</h3>
      <input v-model.trim="form.name" placeholder="Tên lộ trình" required maxlength="255" />
      <input v-model.trim="form.theme" placeholder="Chủ đề (ví dụ: Di sản văn hóa)" />
      <textarea v-model.trim="form.description" placeholder="Mô tả lộ trình" rows="3"></textarea>
      <label>Chọn điểm nhìn theo thứ tự. Sau khi chọn, dùng nút lên/xuống để sắp xếp.</label>
      <div class="scene-picker">
        <label v-for="scene in allScenes" :key="scene.id" class="scene-option">
          <input type="checkbox" :value="scene.id" v-model="selectedSceneIds" />{{ scene.space?.name }} — {{ scene.name }}
        </label>
      </div>
      <div class="chosen-scenes" v-if="selectedSceneIds.length"><div v-for="(id,index) in selectedSceneIds" :key="id" class="chosen-scene"><span>{{ index+1 }}. {{ sceneName(id) }}</span><button type="button" :disabled="index===0" @click="move(index,-1)" aria-label="Chuyển điểm nhìn lên">↑</button><button type="button" :disabled="index===selectedSceneIds.length-1" @click="move(index,1)" aria-label="Chuyển điểm nhìn xuống">↓</button></div></div>
      <p v-if="error" class="error" role="alert">{{ error }}</p>
      <div class="actions"><button class="btn-primary" type="submit" :disabled="selectedSceneIds.length < 2">{{ editingTourId ? 'Lưu lộ trình' : 'Tạo lộ trình' }} ({{ selectedSceneIds.length }} điểm dừng)</button><button v-if="editingTourId" type="button" @click="reset">Hủy sửa</button></div>
    </form>
    <table class="admin-table"><thead><tr><th>Lộ trình</th><th>Chủ đề</th><th>Số điểm</th><th>Hành động</th></tr></thead><tbody>
      <tr v-for="tour in tours" :key="tour.id"><td>{{ tour.name }}</td><td>{{ tour.theme }}</td><td>{{ tour.steps_count ?? tour.steps?.length ?? 0 }}</td><td class="actions"><button @click="editTour(tour)">Sửa / sắp xếp</button><button @click="removeTour(tour)">Xóa</button></td></tr>
    </tbody></table>
  </section>
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue';import apiClient from '../../api/client';
const tours=ref([]);const allScenes=ref([]);const selectedSceneIds=ref([]);const editingTourId=ref(null);const error=ref('');const form=reactive({name:'',theme:'',description:''});
function sceneName(id){const scene=allScenes.value.find((item)=>Number(item.id)===Number(id));return scene?`${scene.space?.name||''} — ${scene.name}`:`Điểm nhìn #${id}`;}
function move(index,delta){const next=index+delta;if(next<0||next>=selectedSceneIds.value.length)return;const list=[...selectedSceneIds.value];[list[index],list[next]]=[list[next],list[index]];selectedSceneIds.value=list;}
async function loadTours(){const response=await apiClient.get('/tours');tours.value=response.data;}
async function loadAllScenes(){const response=await apiClient.get('/scenes');allScenes.value=response.data;}
async function editTour(tour){error.value='';try{const response=await apiClient.get(`/tours/${tour.id}`);const full=response.data;editingTourId.value=full.id;form.name=full.name||'';form.theme=full.theme||'';form.description=full.description||'';selectedSceneIds.value=(full.steps||[]).map((step)=>step.scene_id);}catch(err){error.value=err?.response?.data?.message||'Không thể tải lộ trình để sửa.';}}
function reset(){editingTourId.value=null;form.name='';form.theme='';form.description='';selectedSceneIds.value=[];error.value='';}
async function submitTour(){error.value='';const payload={name:form.name,theme:form.theme||null,description:form.description||null,scene_ids:selectedSceneIds.value.map(Number)};try{if(editingTourId.value)await apiClient.put(`/tours/${editingTourId.value}`,payload);else await apiClient.post('/tours',payload);reset();await loadTours();}catch(err){error.value=err?.response?.data?.message||'Không thể lưu lộ trình.';}}
async function removeTour(tour){if(!window.confirm(`Xóa lộ trình “${tour.name}”?`))return;try{await apiClient.delete(`/tours/${tour.id}`);await loadTours();}catch(err){error.value=err?.response?.data?.message||'Không thể xóa lộ trình.';}}
onMounted(async()=>{await Promise.all([loadTours(),loadAllScenes()]);});
</script>
<style scoped>.tour-form{display:grid;gap:10px;margin-bottom:18px}.tour-form input,.tour-form textarea{box-sizing:border-box;width:100%;padding:9px;border:1px solid var(--color-border);border-radius:8px}.scene-picker{display:flex;flex-direction:column;gap:6px;max-height:220px;overflow-y:auto;border:1px solid var(--color-border);border-radius:8px;padding:8px}.scene-option{display:flex;align-items:center;gap:8px}.chosen-scenes{display:grid;gap:4px}.chosen-scene{display:flex;align-items:center;gap:6px}.chosen-scene span{flex:1}.actions{display:flex;gap:8px;flex-wrap:wrap}.admin-table{width:100%;border-collapse:collapse}.admin-table th,.admin-table td{border-bottom:1px solid var(--color-border);padding:8px;text-align:left}.error{color:#b42318}</style>
