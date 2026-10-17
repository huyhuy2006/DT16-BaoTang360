<template>
  <div>
    <h2>Quản trị điểm nhìn, điểm nóng &amp; liên kết chuyển cảnh</h2>

    <div class="card">
      <label>Chọn không gian tham quan:</label>
      <select v-model.number="selectedSpaceId" @change="loadScenes">
        <option v-for="space in spaces" :key="space.id" :value="space.id">{{ space.name }}</option>
      </select>
    </div>

    <!-- ---------- Thêm điểm nhìn mới ---------- -->
    <form class="card" @submit.prevent="submitScene">
      <h3>{{ editingSceneId ? "Cập nhật điểm nhìn" : "Thêm điểm nhìn mới" }}</h3>
      <input v-model="sceneForm.name" placeholder="Tên điểm nhìn (vd. Sảnh chính)" required />
      <input type="file" accept="image/*" @change="onPanoramaFile" />
      <p class="muted">Khi sửa điểm nhìn, chỉ chọn tệp mới nếu muốn thay panorama hiện tại.</p>
      <!-- Dữ liệu panorama demo có sẵn; dùng trường tải lên để thêm cảnh đã được cấp quyền. -->
      <div class="row-3">
        <input v-model.number="sceneForm.initial_pitch" type="number" placeholder="Pitch ban đầu (độ)" />
        <input v-model.number="sceneForm.initial_yaw" type="number" placeholder="Yaw ban đầu (độ)" />
        <input v-model.number="sceneForm.initial_hfov" type="number" placeholder="Góc nhìn (hfov)" />
      </div>
      <div class="form-actions"><button class="btn-primary" type="submit">{{ editingSceneId ? "Lưu thay đổi" : "Thêm điểm nhìn" }}</button><button v-if="editingSceneId" type="button" @click="resetSceneForm">Hủy sửa</button></div>
    </form>

    <!-- ---------- Danh sách điểm nhìn ---------- -->
    <table class="admin-table">
      <thead><tr><th>Tên điểm nhìn</th><th>Số hotspot</th><th>Hành động</th></tr></thead>
      <tbody>
        <tr
          v-for="scene in scenes"
          :key="scene.id"
          :class="{ active: scene.id === selectedSceneId }"
          @click="selectScene(scene)"
        >
          <td>{{ scene.name }}</td>
          <td>{{ scene.hotspots?.length || 0 }}</td>
          <td class="actions"><button @click.stop="editScene(scene)">Sửa</button><button @click.stop="removeScene(scene)">Xoá</button></td>
        </tr>
      </tbody>
    </table>

    <!-- ---------- Quản trị hotspot của điểm nhìn đang chọn ---------- -->
    <section v-if="selectedScene" class="card">
      <h3>Điểm nóng thông tin của "{{ selectedScene.name }}"</h3>

      <form class="row-4" @submit.prevent="submitHotspot">
        <input v-model.number="hotspotForm.pitch" type="number" placeholder="Pitch" required />
        <input v-model.number="hotspotForm.yaw" type="number" placeholder="Yaw" required />
        <input v-model="hotspotForm.title" placeholder="Tiêu đề hotspot" required />
        <select v-model.number="hotspotForm.artifact_id">
          <option :value="null">-- Không gắn hiện vật --</option>
          <option v-for="a in artifacts" :key="a.id" :value="a.id">{{ a.name }}</option>
        </select>
        <button class="btn-primary" type="submit">Thêm hotspot</button>
      </form>

      <ul class="plain-list">
        <li v-for="h in selectedScene.hotspots" :key="h.id">
          {{ h.title }} (pitch {{ h.pitch }}, yaw {{ h.yaw }})
          <button @click="removeHotspot(h)">Xoá</button>
        </li>
      </ul>

      <h3>Liên kết chuyển cảnh từ "{{ selectedScene.name }}"</h3>
      <form class="row-4" @submit.prevent="submitSceneLink">
        <select v-model.number="linkForm.to_scene_id" required>
          <option disabled value="">-- Chọn điểm nhìn đích --</option>
          <option v-for="s in scenes.filter(x => x.id !== selectedScene.id)" :key="s.id" :value="s.id">
            {{ s.name }}
          </option>
        </select>
        <input v-model.number="linkForm.pitch" type="number" placeholder="Pitch" required />
        <input v-model.number="linkForm.yaw" type="number" placeholder="Yaw" required />
        <input v-model="linkForm.label" placeholder="Nhãn (vd. Sang phòng gốm)" />
        <button class="btn-primary" type="submit">Thêm liên kết</button>
      </form>

      <ul class="plain-list">
        <li v-for="link in selectedScene.outgoing_links" :key="link.id">
          → {{ link.to_scene?.name || ('Điểm nhìn #' + link.to_scene_id) }} ({{ link.label }})
          <button @click="removeSceneLink(link)">Xoá</button>
        </li>
      </ul>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import apiClient from "../../api/client";

const spaces = ref([]);
const scenes = ref([]);
const artifacts = ref([]);
const selectedSpaceId = ref(null);
const selectedSceneId = ref(null);
const panoramaFile = ref(null);
const editingSceneId = ref(null);

const sceneForm = reactive({ name: "", initial_pitch: 0, initial_yaw: 0, initial_hfov: 100 });
const hotspotForm = reactive({ pitch: 0, yaw: 0, title: "", artifact_id: null });
const linkForm = reactive({ to_scene_id: "", pitch: 0, yaw: 0, label: "" });

const selectedScene = computed(() => scenes.value.find((s) => s.id === selectedSceneId.value) || null);

function onPanoramaFile(e) {
  panoramaFile.value = e.target.files[0];
}

async function loadSpaces() {
  const res = await apiClient.get("/spaces");
  spaces.value = res.data;
  if (spaces.value.length && !selectedSpaceId.value) {
    selectedSpaceId.value = spaces.value[0].id;
    await loadScenes();
  }
}

async function loadArtifacts() {
  const res = await apiClient.get("/artifacts");
  artifacts.value = res.data.data; // phân trang Laravel
}

async function loadScenes() {
  if (!selectedSpaceId.value) return;
  const res = await apiClient.get("/scenes", { params: { space_id: selectedSpaceId.value } });
  scenes.value = res.data;
  selectedSceneId.value = scenes.value[0]?.id || null;
}

function selectScene(scene) {
  selectedSceneId.value = scene.id;
}

async function refreshSelectedScene() {
  if (!selectedSceneId.value) return;
  const res = await apiClient.get("/scenes/" + selectedSceneId.value);
  const idx = scenes.value.findIndex((s) => s.id === selectedSceneId.value);
  if (idx !== -1) scenes.value[idx] = res.data;
}

function editScene(scene) {
  editingSceneId.value = scene.id;
  selectedSceneId.value = scene.id;
  sceneForm.name = scene.name || "";
  sceneForm.initial_pitch = Number(scene.initial_pitch ?? 0);
  sceneForm.initial_yaw = Number(scene.initial_yaw ?? 0);
  sceneForm.initial_hfov = Number(scene.initial_hfov ?? 100);
  panoramaFile.value = null;
}

function resetSceneForm() {
  editingSceneId.value = null;
  sceneForm.name = "";
  sceneForm.initial_pitch = 0;
  sceneForm.initial_yaw = 0;
  sceneForm.initial_hfov = 100;
  panoramaFile.value = null;
}

async function submitScene() {
  if (!selectedSpaceId.value) {
    alert("Hãy tạo hoặc chọn không gian trước khi thêm điểm nhìn.");
    return;
  }
  if (!editingSceneId.value && !panoramaFile.value) {
    alert("Khi tạo điểm nhìn mới, bạn cần chọn ảnh panorama.");
    return;
  }

  const fd = new FormData();
  fd.append("space_id", selectedSpaceId.value);
  fd.append("name", sceneForm.name);
  if (panoramaFile.value) fd.append("panorama_image", panoramaFile.value);
  fd.append("initial_pitch", sceneForm.initial_pitch);
  fd.append("initial_yaw", sceneForm.initial_yaw);
  fd.append("initial_hfov", sceneForm.initial_hfov);

  const url = editingSceneId.value ? `/scenes/${editingSceneId.value}` : "/scenes";
  if (editingSceneId.value) fd.append("_method", "PUT");
  await apiClient.post(url, fd, { headers: { "Content-Type": "multipart/form-data" } });
  const previousSelectedSpace = selectedSpaceId.value;
  resetSceneForm();
  await loadScenes();
  selectedSpaceId.value = previousSelectedSpace;
}

async function removeScene(scene) {
  if (!confirm("Xoá điểm nhìn \"" + scene.name + "\"?")) return;
  await apiClient.delete("/scenes/" + scene.id);
  await loadScenes();
}

async function submitHotspot() {
  await apiClient.post("/hotspots", { scene_id: selectedScene.value.id, ...hotspotForm });
  hotspotForm.pitch = 0;
  hotspotForm.yaw = 0;
  hotspotForm.title = "";
  hotspotForm.artifact_id = null;
  await refreshSelectedScene();
}

async function removeHotspot(hotspot) {
  if (!confirm("Xoá hotspot \"" + hotspot.title + "\"?")) return;
  await apiClient.delete("/hotspots/" + hotspot.id);
  await refreshSelectedScene();
}

async function submitSceneLink() {
  await apiClient.post("/scene-links", { from_scene_id: selectedScene.value.id, ...linkForm });
  linkForm.to_scene_id = "";
  linkForm.pitch = 0;
  linkForm.yaw = 0;
  linkForm.label = "";
  await refreshSelectedScene();
}

async function removeSceneLink(link) {
  if (!confirm("Xoá liên kết chuyển cảnh này?")) return;
  await apiClient.delete("/scene-links/" + link.id);
  await refreshSelectedScene();
}

onMounted(async () => {
  await loadSpaces();
  await loadArtifacts();
});
</script>

<style scoped>
.row-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.row-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; align-items: center; }
.form-actions,.actions{display:flex;gap:8px;flex-wrap:wrap}.plain-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 6px; }
tr.active { background: var(--color-bg); font-weight: bold; cursor: pointer; }
tr { cursor: pointer; }
@media (max-width: 640px) { .row-3, .row-4 { grid-template-columns: 1fr; } }
</style>
