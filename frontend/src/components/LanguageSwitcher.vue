<template>
  <select :value="locale" @change="onChange($event.target.value)" class="lang-switcher">
    <option value="vi">🇻🇳 Tiếng Việt</option>
    <option value="en">🇬🇧 English</option>
  </select>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { setUiLocale } from "../i18n";

const { locale } = useI18n();

function onChange(value) {
  setUiLocale(value);
}
</script>

<style scoped>
.lang-switcher { padding: 6px 10px; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-surface); color: var(--color-text); }
</style>
