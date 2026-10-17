<template>
  <div v-if="hotspot" class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal-content card">
      <button class="close-btn" @click="$emit('close')">✕</button>

      <h3>{{ hotspot.title }}</h3>

      <img
        v-if="hotspot.artifact && hotspot.artifact.image"
        :src="storageUrl(hotspot.artifact.image)"
        loading="lazy"
        decoding="async"
        alt="Ảnh hiện vật"
        class="artifact-image"
      />
      <!-- Nếu chưa có ảnh, modal vẫn hoạt động và hiển thị nội dung văn bản. -->

      <p v-if="hotspot.artifact">{{ hotspot.artifact.description }}</p>
      <p v-if="hotspot.artifact && hotspot.artifact.period"><strong>Niên đại:</strong> {{ hotspot.artifact.period }}</p>
      <video v-if="hotspot.artifact?.video_url" controls preload="metadata" class="artifact-video" :src="hotspot.artifact.video_url">Trình duyệt không hỗ trợ video HTML5.</video>

      <div v-if="narrations.length" class="narration-box">
        <label>Ngôn ngữ thuyết minh:</label>
        <select v-model="selectedLanguageId">
          <option v-for="n in narrations" :key="n.id" :value="n.language.id">
            {{ n.language.name }}
          </option>
        </select>

        <audio
          v-if="currentNarration"
          controls
          :src="storageUrl(currentNarration.audio_path)"
          @play="$emit('play-narration', currentNarration)"
        ></audio>
        <!-- Nếu chưa có audio, phần thuyết minh sẽ không hiển thị trình phát. -->
      </div>

      <button
        v-if="isAuthenticated && hotspot.artifact"
        class="btn-primary"
        @click="$emit('save-to-collection', hotspot.artifact)"
      >
        ⭐ Lưu vào bộ sưu tập
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { storeToRefs } from "pinia";
import apiClient from "../api/client";
import { useAuthStore } from "../stores/auth";

const props = defineProps({ hotspot: { type: Object, default: null } });
defineEmits(["close", "save-to-collection", "play-narration"]);

const { isAuthenticated } = storeToRefs(useAuthStore());
const narrations = ref([]);
const selectedLanguageId = ref(null);

const currentNarration = computed(() =>
  narrations.value.find((n) => n.language.id === selectedLanguageId.value)
);

function storageUrl(path) {
  return import.meta.env.VITE_STORAGE_BASE_URL + "/" + path;
}

async function loadNarrations() {
  narrations.value = [];
  if (!props.hotspot) return;

  const responses = [];
  if (props.hotspot.id && !String(props.hotspot.id).startsWith("link-")) {
    const hotspotRes = await apiClient.get("/narrations", { params: { hotspot_id: props.hotspot.id } });
    responses.push(...hotspotRes.data);
  }
  const artifactId = props.hotspot.artifact?.id;
  if (artifactId) {
    const artifactRes = await apiClient.get("/narrations", { params: { artifact_id: artifactId } });
    responses.push(...artifactRes.data);
  }
  const seen = new Set();
  narrations.value = responses.filter((item) => {
    if (seen.has(item.id)) return false; seen.add(item.id); return true;
  });
  if (narrations.value.length) selectedLanguageId.value = narrations.value[0].language.id;
}

watch(() => props.hotspot, loadNarrations, { immediate: true });
</script>

<style scoped>
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.55);
  display: flex; align-items: center; justify-content: center; z-index: 50;
}
.modal-content { width: min(520px, 92vw); max-height: 85vh; overflow-y: auto; position: relative; }
.close-btn { position: absolute; top: 10px; right: 10px; border: none; background: none; font-size: 18px; cursor: pointer; }
.artifact-image { width: 100%; border-radius: 8px; margin: 10px 0; }
.artifact-video { width:100%; max-height:320px; margin:10px 0; border-radius:8px; background:#000; }
.narration-box { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }
</style>
