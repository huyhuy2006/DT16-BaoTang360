import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

const routes = [
  { path: "/", name: "home", component: () => import("../views/HomeView.vue") },
  { path: "/tours", name: "tours", component: () => import("../views/TourListView.vue") },
  { path: "/hien-vat", name: "artifacts", component: () => import("../views/ArtifactCatalogView.vue") },
  { path: "/tours/:id", name: "tour-player", component: () => import("../views/TourPlayerView.vue"), props: true },
  { path: "/tham-quan-tu-do/:spaceId", name: "free-explore", component: () => import("../views/FreeExploreView.vue"), props: true },
  { path: "/so-luu-but", name: "guestbook", component: () => import("../views/GuestbookView.vue") },
  { path: "/bo-suu-tap", name: "collection", component: () => import("../views/CollectionView.vue"), meta: { requiresAuth: true } },
  { path: "/tai-khoan", name: "profile", component: () => import("../views/ProfileView.vue"), meta: { requiresAuth: true } },
  { path: "/danh-gia", name: "feedback", component: () => import("../views/FeedbackView.vue") },
  { path: "/dang-nhap", name: "login", component: () => import("../views/LoginView.vue") },
  { path: "/dang-ky", name: "register", component: () => import("../views/RegisterView.vue") },
  { path: "/quen-mat-khau", name: "forgot-password", component: () => import("../views/ForgotPasswordView.vue") },
  { path: "/dat-lai-mat-khau", name: "reset-password", component: () => import("../views/ResetPasswordView.vue") },
  {
    path: "/quan-tri", component: () => import("../views/admin/AdminLayout.vue"), meta: { requiresEditor: true },
    children: [
      { path: "khong-gian", name: "admin-spaces", component: () => import("../views/admin/SpacesAdmin.vue") },
      { path: "diem-nhin", name: "admin-scenes", component: () => import("../views/admin/ScenesAdmin.vue") },
      { path: "hien-vat", name: "admin-artifacts", component: () => import("../views/admin/ArtifactsAdmin.vue") },
      { path: "lo-trinh", name: "admin-tours", component: () => import("../views/admin/ToursAdmin.vue") },
      { path: "thong-ke", name: "admin-stats", component: () => import("../views/admin/StatsAdmin.vue"), meta: { requiresAdmin: true } },
      { path: "nguoi-dung", name: "admin-users", component: () => import("../views/admin/UsersAdmin.vue"), meta: { requiresAdmin: true } },
      { path: "phan-hoi", name: "admin-feedback", component: () => import("../views/admin/FeedbackAdmin.vue"), meta: { requiresAdmin: true } },
    ],
  },
];

const router = createRouter({ history: createWebHistory(), routes });

router.beforeEach(async (to) => {
  const auth = useAuthStore();
  // Reload the API-backed user before evaluating role-based routes after a refresh.
  if (auth.token && !auth.user) await auth.fetchMe();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: "login" };
  }
  if (to.meta.requiresEditor && !auth.isEditor) {
    return { name: "home" };
  }
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: "home" };
  }
  return true;
});

export default router;
