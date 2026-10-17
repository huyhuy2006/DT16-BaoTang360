<template>
  <div class="container">
    <h2 v-if="space">{{ space.name }} — Tham quan tự do</h2>

    <PanoramaViewer
      v-if="currentScene"
      :panorama-url="storageUrl(currentScene.panorama_image)" :panorama-config="panoramaConfig(currentScene.panorama_image)"
      :hotspots="viewerHotspots"
      :initial-pitch="currentScene.initial_pitch"
      :initial-yaw="currentScene.initial_yaw"
      :initial-hfov="currentScene.initial_hfov"
      @hotspot-click="onHotspotClick"
      @scene-loaded="onSceneLoaded"
    />

    <HotspotInfoModal
      :hotspot="activeHotspot"
      @close="activeHotspot = null"
      @save-to-collection="saveToCollection"
      @play-narration="onPlayNarration"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import apiClient from "../api/client";
import { useVisitStore } from "../stores/visit";
import PanoramaViewer from "../components/PanoramaViewer.vue";
import HotspotInfoModal from "../components/HotspotInfoModal.vue";
import { buildPanoramaConfig } from "../lib/panorama";

const props = defineProps({ spaceId: { type: [String, Number], required: true } });

const space = ref(null);
const currentScene = ref(null);
const activeHotspot = ref(null);
const visitStore = useVisitStore();

function panoramaConfig(path) { return buildPanoramaConfig(import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8000/storage', path); }

function storageUrl(path) { return path?.startsWith?.('http') ? path : `${import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8000/storage'}/${path || ''}`; }

const viewerHotspots = computed(() => {
  if (!currentScene.value) return [];

  const infoHotspots = (currentScene.value.hotspots || []).map((h) => ({ ...h, type: "info" }));
  const navHotspots = (currentScene.value.outgoing_links || []).map((l) => ({
    id: "link-" + l.id,
    pitch: l.pitch,
    yaw: l.yaw,
    title: l.label || "Sang không gian khác",
    type: "scene",
    targetSceneId: l.to_scene_id,
  }));

  return [...infoHotspots, ...navHotspots];
});

async function loadSpace() {
  const res = await apiClient.get("/spaces/" + props.spaceId);
  space.value = res.data;
  currentScene.value = space.value.scenes[0] || null;
}

function onHotspotClick(hotspot) {
  if (hotspot.type === "scene" && hotspot.targetSceneId) {
    goToScene(hotspot.targetSceneId);
  } else {
    activeHotspot.value = hotspot;
    visitStore.log("click_hotspot", { sceneId: currentScene.value.id, hotspotId: hotspot.id });
  }
}

function goToScene(sceneId) {
  const target = space.value.scenes.find((s) => s.id === sceneId);
  if (target) currentScene.value = target;
}

function onSceneLoaded() {
  visitStore.log("view_scene", { sceneId: currentScene.value.id });
}

function onPlayNarration(narration) {
  visitStore.log("play_narration", { sceneId: currentScene.value.id, hotspotId: activeHotspot.value?.id });
}

async function saveToCollection(artifact) {
  await apiClient.post("/collection-items", { artifact_id: artifact.id });
  alert("Đã lưu \"" + artifact.name + "\" vào bộ sưu tập của bạn.");
}

onMounted(loadSpace);
</script>
