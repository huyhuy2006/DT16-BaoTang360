<template>
  <section class="container">
    <h1>Đánh giá và phản hồi</h1>
    <p>Chọn tour hoặc hiện vật đã tham quan, chấm từ 1 đến 5 sao và gửi nhận xét. Đánh giá mới sẽ chờ quản trị viên kiểm duyệt trước khi hiển thị công khai.</p>
    <form v-if="isAuthenticated" class="card feedback-form" @submit.prevent="submit">
      <label>Đối tượng
        <select v-model="form.targetKey" required>
          <option disabled value="">-- Chọn tour hoặc hiện vật --</option>
          <optgroup label="Tour">
            <option v-for="tour in tours" :key="`tour-${tour.id}`" :value="`tour:${tour.id}`">Tour — {{ tour.name }}</option>
          </optgroup>
          <optgroup label="Hiện vật">
            <option v-for="artifact in artifacts" :key="`artifact-${artifact.id}`" :value="`artifact:${artifact.id}`">Hiện vật — {{ artifact.name }}</option>
          </optgroup>
        </select>
      </label>
      <label>Điểm đánh giá
        <select v-model.number="form.rating" required>
          <option :value="5">5 — Rất tốt</option><option :value="4">4 — Tốt</option><option :value="3">3 — Bình thường</option><option :value="2">2 — Chưa tốt</option><option :value="1">1 — Không hài lòng</option>
        </select>
      </label>
      <label>Nhận xét<textarea v-model.trim="form.comment" minlength="5" maxlength="2000" rows="4" required placeholder="Nội dung phản hồi (5–2000 ký tự)"></textarea></label>
      <p v-if="message" class="success" role="status">{{ message }}</p>
      <p v-if="error" class="error" role="alert">{{ error }}</p>
      <button class="btn-primary" type="submit" :disabled="busy">Gửi đánh giá</button>
    </form>
    <p v-else>Đăng nhập để gửi đánh giá. Bạn vẫn có thể xem những đánh giá đã được duyệt ở bên dưới.</p>

    <h2>Đánh giá đã được duyệt</h2>
    <div v-if="reviews.length" class="reviews">
      <article v-for="review in reviews" :key="review.id" class="card">
        <strong>{{ review.user?.name || 'Người tham quan' }}</strong>
        <span aria-label="Số sao">{{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5-review.rating) }}</span>
        <p>{{ review.comment }}</p>
        <small class="muted">{{ review.target_type === 'tour' ? 'Tour' : 'Hiện vật' }} #{{ review.target_id }}</small>
      </article>
    </div>
    <p v-else>Chưa có đánh giá nào được duyệt.</p>
  </section>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { storeToRefs } from 'pinia';
import apiClient from '../api/client';
import { useAuthStore } from '../stores/auth';
const auth = useAuthStore(); const { isAuthenticated } = storeToRefs(auth);
const tours=ref([]); const artifacts=ref([]); const reviews=ref([]); const busy=ref(false); const error=ref(''); const message=ref('');
const form=reactive({targetKey:'',rating:5,comment:''});
function messageFor(err){const errors=err?.response?.data?.errors;return errors?Object.values(errors).flat().join(' '):(err?.response?.data?.message||'Không thể xử lý yêu cầu.');}
async function load(){const results=await Promise.allSettled([apiClient.get('/tours'),apiClient.get('/artifacts'),apiClient.get('/feedback')]);if(results[0].status==='fulfilled')tours.value=results[0].value.data;if(results[1].status==='fulfilled')artifacts.value=results[1].value.data.data||results[1].value.data;if(results[2].status==='fulfilled')reviews.value=results[2].value.data.data||results[2].value.data;}
async function submit(){busy.value=true;error.value='';message.value='';try{const [target_type,targetId]=form.targetKey.split(':');await apiClient.post('/feedback',{target_type,target_id:Number(targetId),rating:form.rating,comment:form.comment});form.comment='';message.value='Đã gửi. Đánh giá sẽ hiển thị sau khi được duyệt.';}catch(err){error.value=messageFor(err);}finally{busy.value=false;}}
onMounted(load);
</script>

<style scoped>.feedback-form{display:grid;gap:12px;max-width:680px}.feedback-form label{display:grid;gap:6px}.feedback-form input,.feedback-form select,.feedback-form textarea{padding:10px;border:1px solid var(--color-border);border-radius:8px;max-width:100%;box-sizing:border-box}.reviews{display:grid;gap:12px}.success{color:#187443}.error{color:#b42318}</style>
