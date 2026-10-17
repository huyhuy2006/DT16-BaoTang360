<template>
  <div class="container">
    <h2>Sổ lưu bút</h2>

    <form class="card" @submit.prevent="submitEntry">
      <input v-model="form.display_name" placeholder="Tên của bạn" required />
      <textarea v-model="form.message" placeholder="Cảm nhận của bạn về chuyến tham quan..." required></textarea>
      <button class="btn-primary" type="submit">Gửi lưu bút</button>
    </form>

    <div class="entries">
      <div v-for="entry in entries" :key="entry.id" class="card entry">
        <strong>{{ entry.display_name }}</strong>
        <p>{{ entry.message }}</p>
        <small>{{ new Date(entry.created_at).toLocaleString("vi-VN") }}</small>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import apiClient from "../api/client";

const entries = ref([]);
const form = reactive({ display_name: "", message: "" });

async function loadEntries() {
  const res = await apiClient.get("/guestbook");
  entries.value = res.data.data; // phân trang Laravel: dữ liệu nằm trong .data
}

async function submitEntry() {
  await apiClient.post("/guestbook", { ...form });
  form.display_name = "";
  form.message = "";
  await loadEntries();
}

onMounted(loadEntries);
</script>

<style scoped>
form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.entries { display: flex; flex-direction: column; gap: 10px; }
</style>
