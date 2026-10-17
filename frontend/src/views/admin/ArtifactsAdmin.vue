<template>
  <div>
    <h2>Quản trị hiện vật &amp; thuyết minh đa ngữ</h2>

    <!-- ---------- Thêm hiện vật ---------- -->
    <form class="card" @submit.prevent="submitArtifact">
      <h3>{{ editingArtifactId ? "Cập nhật hiện vật" : "Thêm hiện vật mới" }}</h3>
      <input v-model="artifactForm.name" placeholder="Tên hiện vật" required />
      <textarea v-model="artifactForm.description" placeholder="Mô tả chi tiết"></textarea>
      <div class="row-2">
        <input v-model="artifactForm.period" placeholder="Niên đại (vd. Thế kỷ XV)" />
        <input v-model="artifactForm.material" placeholder="Chất liệu (vd. Gốm men lam)" />
      </div>
      <input type="file" accept="image/*" @change="onArtifactImage" />
      <input v-model.trim="artifactForm.video_url" type="url" placeholder="URL video giới thiệu (nếu có)" />
      <!-- Ảnh demo có sẵn trong storage; dùng trường tải ảnh để thay bằng tư liệu đã có quyền sử dụng. -->
      <div class="form-actions"><button class="btn-primary" type="submit">{{ editingArtifactId ? "Lưu thay đổi" : "Thêm hiện vật" }}</button><button v-if="editingArtifactId" type="button" @click="resetArtifactForm">Hủy sửa</button></div>
    </form>

    <!-- ---------- Danh sách hiện vật ---------- -->
    <table class="admin-table">
      <thead><tr><th>Tên hiện vật</th><th>Niên đại</th><th>Hành động</th></tr></thead>
      <tbody>
        <tr
          v-for="a in artifacts"
          :key="a.id"
          :class="{ active: a.id === selectedArtifactId }"
          @click="selectArtifact(a)"
        >
          <td>{{ a.name }}</td>
          <td>{{ a.period }}</td>
          <td class="actions"><button @click.stop="editArtifact(a)">Sửa</button><button @click.stop="removeArtifact(a)">Xoá</button></td>
        </tr>
      </tbody>
    </table>

    <!-- ---------- Thuyết minh đa ngữ của hiện vật đang chọn ---------- -->
    <section v-if="selectedArtifact" class="card">
      <h3>Thuyết minh đa ngữ cho "{{ selectedArtifact.name }}"</h3>

      <form class="row-3" @submit.prevent="submitNarration">
        <select v-model.number="narrationForm.language_id" required>
          <option disabled value="">-- Chọn ngôn ngữ --</option>
          <option v-for="lang in languages" :key="lang.id" :value="lang.id">{{ lang.name }}</option>
        </select>
        <input type="file" accept="audio/*" @change="onAudioFile" required />
        <!-- Tải tệp audio demo hoặc tệp có quyền sử dụng theo ngôn ngữ đã chọn. -->
        <button class="btn-primary" type="submit">Tải lên thuyết minh</button>
      </form>
      <textarea v-model="narrationForm.transcript" placeholder="Bản ghi lời thuyết minh khớp với tệp âm thanh"></textarea>

      <ul class="plain-list">
        <li v-for="n in selectedArtifact.narrations" :key="n.id">
          {{ n.language.name }} —
          <audio controls :src="storageUrl(n.audio_path)"></audio>
          <button @click="removeNarration(n)">Xoá</button>
        </li>
      </ul>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import apiClient from "../../api/client";

const artifacts = ref([]);
const languages = ref([]);
const selectedArtifactId = ref(null);
const artifactImage = ref(null);
const audioFile = ref(null);

const artifactForm = reactive({ name: "", description: "", period: "", material: "", video_url: "" });
const editingArtifactId = ref(null);
const narrationForm = reactive({ language_id: "", transcript: "" });

const selectedArtifact = computed(() => artifacts.value.find((a) => a.id === selectedArtifactId.value) || null);

function storageUrl(path) {
  return import.meta.env.VITE_STORAGE_BASE_URL + "/" + path;
}

function onArtifactImage(e) {
  artifactImage.value = e.target.files[0];
}
function onAudioFile(e) {
  audioFile.value = e.target.files[0];
}

async function loadArtifacts() {
  const res = await apiClient.get("/artifacts");
  artifacts.value = res.data.data;
}

async function loadLanguages() {
  const res = await apiClient.get("/languages");
  languages.value = res.data;
}

function selectArtifact(a) {
  selectedArtifactId.value = a.id;
}
function editArtifact(a) {
  editingArtifactId.value = a.id;
  artifactForm.name = a.name || ""; artifactForm.description = a.description || "";
  artifactForm.period = a.period || ""; artifactForm.material = a.material || ""; artifactForm.video_url = a.video_url || "";
}
function resetArtifactForm() {
  editingArtifactId.value = null; artifactForm.name = ""; artifactForm.description = "";
  artifactForm.period = ""; artifactForm.material = ""; artifactForm.video_url = ""; artifactImage.value = null;
}

async function refreshSelectedArtifact() {
  if (!selectedArtifactId.value) return;
  const res = await apiClient.get("/artifacts/" + selectedArtifactId.value);
  const idx = artifacts.value.findIndex((a) => a.id === selectedArtifactId.value);
  if (idx !== -1) artifacts.value[idx] = { ...artifacts.value[idx], narrations: res.data.narrations };
}

async function submitArtifact() {
  const fd = new FormData();
  Object.entries(artifactForm).forEach(([key, val]) => fd.append(key, val || ""));
  if (artifactImage.value) fd.append("image", artifactImage.value);
  const url = editingArtifactId.value ? `/artifacts/${editingArtifactId.value}` : "/artifacts";
  if (editingArtifactId.value) fd.append("_method", "PUT");
  await apiClient.post(url, fd, { headers: { "Content-Type": "multipart/form-data" } });
  resetArtifactForm();
  await loadArtifacts();
}

async function removeArtifact(a) {
  if (!confirm("Xoá hiện vật \"" + a.name + "\"?")) return;
  await apiClient.delete("/artifacts/" + a.id);
  await loadArtifacts();
}

async function submitNarration() {
  const fd = new FormData();
  fd.append("artifact_id", selectedArtifact.value.id);
  fd.append("language_id", narrationForm.language_id);
  fd.append("audio", audioFile.value);
  fd.append("transcript", narrationForm.transcript || "");

  await apiClient.post("/narrations", fd, { headers: { "Content-Type": "multipart/form-data" } });
  narrationForm.language_id = "";
  narrationForm.transcript = "";
  audioFile.value = null;
  await refreshSelectedArtifact();
}

async function removeNarration(n) {
  if (!confirm("Xoá bản thuyết minh này?")) return;
  await apiClient.delete("/narrations/" + n.id);
  await refreshSelectedArtifact();
}

onMounted(async () => {
  await loadArtifacts();
  await loadLanguages();
});
</script>

<style scoped>
.form-actions,.actions{display:flex;gap:8px;flex-wrap:wrap}.row-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
.row-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; align-items: center; }
.plain-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 8px; }
tr.active { background: var(--color-bg); font-weight: bold; }
tr { cursor: pointer; }
@media (max-width: 640px) { .row-2, .row-3 { grid-template-columns: 1fr; } }
</style>
