<template>
  <div ref="containerRef" class="panorama-container" aria-label="Trình chiếu bảo tàng 360 độ"></div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch, ref } from "vue";

const props = defineProps({
  panoramaUrl: { type: String, required: true },
  panoramaConfig: { type: Object, default: null },
  hotspots: { type: Array, default: () => [] },
  initialPitch: { type: Number, default: 0 },
  initialYaw: { type: Number, default: 0 },
  initialHfov: { type: Number, default: 100 },
});

const emit = defineEmits(["hotspot-click", "scene-loaded"]);
const containerRef = ref(null);
let viewerInstance = null;

function buildPannellumHotspots() {
  return props.hotspots.map((h) => ({
    id: "hotspot-" + h.id,
    pitch: h.pitch,
    yaw: h.yaw,
    type: "info",
    text: h.title,
    cssClass: h.type === "scene" ? "hotspot-nav" : "hotspot-info",
    clickHandlerFunc: () => emit("hotspot-click", h),
  }));
}

function renderViewer() {
  if (!containerRef.value || !window.pannellum) return;
  if (viewerInstance) {
    viewerInstance.destroy();
    viewerInstance = null;
  }

  const config = props.panoramaConfig || {
    type: "equirectangular",
    panorama: props.panoramaUrl,
  };

  viewerInstance = window.pannellum.viewer(containerRef.value, {
    ...config,
    autoLoad: true,
    pitch: props.initialPitch,
    yaw: props.initialYaw,
    hfov: props.initialHfov,
    hotSpots: buildPannellumHotspots(),
    compass: false,
    showZoomCtrl: true,
    showFullscreenCtrl: true,
    multiResMinHfov: false,
  });

  viewerInstance.on("load", () => emit("scene-loaded"));
}

onMounted(renderViewer);
watch(() => [props.panoramaUrl, props.panoramaConfig, props.hotspots], renderViewer, { deep: true });
onBeforeUnmount(() => viewerInstance?.destroy());
</script>

<style scoped>
.panorama-container {
  width: 100%;
  height: 70vh;
  min-height: 360px;
  border-radius: 10px;
  overflow: hidden;
  background: #111;
}
@media (max-width: 768px) { .panorama-container { height: 55vh; } }
</style>
