import { defineStore } from "pinia";
import apiClient from "../api/client";

function getSessionId() {
  let id = localStorage.getItem("visit_session_id");
  if (!id) {
    id = "s_" + Date.now() + "_" + Math.random().toString(16).slice(2);
    localStorage.setItem("visit_session_id", id);
  }
  return id;
}

export const useVisitStore = defineStore("visit", {
  actions: {
    async log(action, payload) {
      try {
        await apiClient.post("/visit-logs", {
          session_id: getSessionId(),
          action,
          scene_id: payload && payload.sceneId ? payload.sceneId : null,
          hotspot_id: payload && payload.hotspotId ? payload.hotspotId : null,
          duration_seconds: payload?.durationSeconds ?? null,
        });
      } catch (e) {
        // Ghi log không được làm gián đoạn trải nghiệm người dùng
        console.warn("Khong the ghi log tham quan", e);
      }
    },
  },
});
