<template>
  <button
    type="button"
    class="btn-primary theme-switcher"
    @click="toggleTheme"
  >
    {{ theme === "light" ? $t("theme.dark") : $t("theme.light") }}
  </button>
</template>

<script setup>
import { useTheme } from "../composables/useTheme";

const { theme, toggleTheme } = useTheme();
</script>

<style scoped>
.theme-switcher {
  min-height: 38px;
  white-space: nowrap;
}
</style>
