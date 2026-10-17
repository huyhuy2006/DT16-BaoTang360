<template>
  <div class="container">
    <AppHeader />
    <main>
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "./stores/auth";
import AppHeader from "./components/AppHeader.vue";

const auth = useAuthStore();
onMounted(() => {
  if (auth.token) auth.fetchMe();
});
</script>

<style>
.container.narrow {
  max-width: 420px;
}
</style>
