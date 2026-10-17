import { defineStore } from "pinia";
import apiClient from "../api/client";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("auth_token") || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user && state.user.role === "admin",
    isEditor: (state) => state.user && (state.user.role === "admin" || state.user.role === "editor"),
  },

  actions: {
    async login(email, password) {
      const res = await apiClient.post("/login", { email, password });
      this.setSession(res.data.user, res.data.token);
    },

    async register(payload) {
      const res = await apiClient.post("/register", payload);
      this.setSession(res.data.user, res.data.token);
    },

    async fetchMe() {
      if (!this.token) return;
      try { const res = await apiClient.get("/me"); this.user = res.data; }
      catch { this.user = null; this.token = null; localStorage.removeItem("auth_token"); }
    },

    async logout() {
      try {
        await apiClient.post("/logout");
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem("auth_token");
      }
    },

    setSession(user, token) {
      this.user = user;
      this.token = token;
      localStorage.setItem("auth_token", token);
    },

    async forgotPassword(email) {
      await apiClient.post("/forgot-password", { email });
    },

    async resetPassword(payload) {
      await apiClient.post("/reset-password", payload);
    },

    async changePassword(payload) {
      await apiClient.post("/change-password", payload);
    },
  },
});
