<template>
  <button type="button" class="favorite-button" :disabled="busy" :aria-pressed="saved" @click.stop.prevent="toggle">
    {{ busy ? 'Đang lưu…' : saved ? '♥ Đã yêu thích' : '♡ Yêu thích' }}
  </button>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import apiClient from '../api/client';

const props = defineProps({ targetType: { type: String, required: true }, targetId: { type: [String, Number], required: true } });
const auth = useAuthStore(); const saved = ref(false); const busy = ref(false);
async function refresh() {
  saved.value = false;
  if (!auth.token) return;
  try {
    const response = await apiClient.get('/favorites');
    saved.value = response.data.some((item) => item.target_type === props.targetType && String(item.target_id) === String(props.targetId));
  } catch { saved.value = false; }
}
async function toggle() {
  if (!auth.token) { window.alert('Bạn cần đăng nhập để lưu mục yêu thích.'); return; }
  busy.value = true;
  try {
    if (saved.value) await apiClient.delete(`/favorites/${props.targetType}/${props.targetId}`);
    else await apiClient.post('/favorites', { target_type: props.targetType, target_id: Number(props.targetId) });
    saved.value = !saved.value;
  } catch (error) { window.alert(error?.response?.data?.message || 'Không thể cập nhật mục yêu thích.'); }
  finally { busy.value = false; }
}
onMounted(refresh);
watch(() => [props.targetType, props.targetId, auth.token], refresh);
</script>

<style scoped>.favorite-button{border:1px solid var(--color-border);border-radius:8px;padding:7px 10px;background:var(--color-surface);color:inherit;cursor:pointer}.favorite-button:disabled{opacity:.6;cursor:wait}</style>
