<template>
  <div class="container" v-if="tour">
    <div class="tour-header">
      <div><h2>{{ tour.name }}</h2><p class="muted">{{ tour.description }}</p></div>
      <select v-model="mode"><option value="guided">Có dẫn dắt</option><option value="free">Tự do</option></select>
    </div>

    <PanoramaViewer v-if="currentStep?.scene" :panorama-url="storageUrl(currentStep.scene.panorama_image)" :panorama-config="panoramaConfig(currentStep.scene.panorama_image)" :hotspots="viewerHotspots" :initial-pitch="currentStep.scene.initial_pitch" :initial-yaw="currentStep.scene.initial_yaw" :initial-hfov="currentStep.scene.initial_hfov" @hotspot-click="onHotspotClick" @scene-loaded="onSceneLoaded" @play-narration="onPlayNarration" />

    <div class="guided-controls" v-if="mode === 'guided'">
      <div>Điểm {{ currentStepIndex + 1 }} / {{ tour.steps.length }}</div>
      <progress class="tour-progress" :value="currentStepIndex + 1" :max="tour.steps.length" aria-label="Tiến độ lộ trình"></progress>
      <p v-if="savedProgress?.completed_at" class="muted">Lộ trình đã được đánh dấu hoàn thành.</p>
      <div class="step-buttons"><button :disabled="currentStepIndex===0" @click="prevStep">← Trước</button><button :disabled="isLastStep" @click="nextStep">Tiếp →</button></div>
    </div>
    <p v-else class="muted">Chế độ tự do: sử dụng các điểm nóng chuyển cảnh để khám phá các điểm nhìn liên quan.</p>

    <HotspotInfoModal :hotspot="activeHotspot" @close="activeHotspot=null" @save-to-collection="saveToCollection" @play-narration="onPlayNarration" />
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import apiClient from '../api/client';
import { useVisitStore } from '../stores/visit';
import PanoramaViewer from '../components/PanoramaViewer.vue';
import HotspotInfoModal from '../components/HotspotInfoModal.vue';
import { buildPanoramaConfig } from '../lib/panorama';
import { useAuthStore } from '../stores/auth';

const props=defineProps({id:{type:[String,Number],required:true}});
const tour=ref(null); const currentStepIndex=ref(0); const mode=ref('guided'); const activeHotspot=ref(null); const visitStore=useVisitStore(); const auth=useAuthStore(); const savedProgress=ref(null);
const currentStep=computed(()=>tour.value?.steps[currentStepIndex.value]||null);
const isLastStep=computed(()=>!!tour.value&&currentStepIndex.value===tour.value.steps.length-1);
const viewerHotspots=computed(()=>{
 if(!currentStep.value?.scene) return [];
 const info=(currentStep.value.scene.hotspots||[]).map(h=>({...h,type:'info'}));
 const links=(currentStep.value.scene.outgoing_links||[]).map(l=>({id:'link-'+l.id,pitch:l.pitch,yaw:l.yaw,title:l.label||'Sang điểm nhìn khác',type:'scene',targetSceneId:l.to_scene_id}));
 return [...info,...links];
});
function panoramaConfig(path){return buildPanoramaConfig(import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8000/storage', path);}
function storageUrl(path){return path?.startsWith?.('http')?path:`${import.meta.env.VITE_STORAGE_BASE_URL||'http://localhost:8000/storage'}/${path||''}`;}
async function loadTour(){const res=await apiClient.get(`/tours/${props.id}`);tour.value=res.data;currentStepIndex.value=0;savedProgress.value=null;if(auth.isAuthenticated){try{const progress=await apiClient.get(`/tour-progress/${props.id}`);if(progress.data){savedProgress.value=progress.data;currentStepIndex.value=Math.min(Number(progress.data.current_step_order||0),Math.max(0,tour.value.steps.length-1));}}catch{/* Progress is optional; the tour remains usable if it cannot load. */}}await markProgress();await visitStore.log('start_tour',{});}
function onHotspotClick(h){if(h.type==='scene'){const i=tour.value.steps.findIndex(s=>s.scene_id===h.targetSceneId);if(i>=0)currentStepIndex.value=i;else visitStore.log('click_hotspot',{sceneId:currentStep.value.scene.id,hotspotId:null});}else{activeHotspot.value=h;visitStore.log('click_hotspot',{sceneId:currentStep.value.scene.id,hotspotId:h.id});}}
function onSceneLoaded(){if(currentStep.value?.scene)visitStore.log('view_scene',{sceneId:currentStep.value.scene.id});}
function onPlayNarration(){visitStore.log('play_narration',{sceneId:currentStep.value?.scene?.id,hotspotId:activeHotspot.value?.id});}
async function markProgress(){if(!auth.isAuthenticated||!tour.value||!currentStep.value)return;const visited=(tour.value.steps||[]).slice(0,currentStepIndex.value+1).map((step)=>step.scene_id).filter(Boolean);try{const response=await apiClient.put(`/tour-progress/${props.id}`,{current_step_order:currentStepIndex.value,visited_scene_ids:visited,completed:isLastStep.value});savedProgress.value=response.data;}catch{/* Keep navigation working if progress API is temporarily unavailable. */}}
function nextStep(){if(!isLastStep.value)currentStepIndex.value++;}
function prevStep(){if(currentStepIndex.value>0)currentStepIndex.value--;}
watch(currentStepIndex,()=>{void markProgress();});
async function saveToCollection(artifact){if(!artifact)return;await apiClient.post('/collection-items',{artifact_id:artifact.id});}
watch(()=>props.id,loadTour); watch(isLastStep,(value)=>{if(value&&currentStep.value)visitStore.log('finish_tour',{sceneId:currentStep.value.scene.id});}); onMounted(loadTour);
</script>
<style scoped>.tour-header{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}.guided-controls{margin-top:12px}.tour-progress{display:block;width:100%;height:10px;margin:8px 0}.step-buttons{display:flex;gap:10px;margin-top:8px}@media(max-width:768px){.tour-header{flex-direction:column}}</style>
