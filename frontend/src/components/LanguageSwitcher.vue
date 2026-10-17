<template>
  <select
    :value="locale"
    class="lang-switcher"
    :aria-label="locale === 'vi' ? 'Chọn ngôn ngữ' : 'Choose language'"
    @change="onChange($event.target.value)"
  >
    <option value="vi">🇻🇳 Tiếng Việt</option>
    <option value="en">🇬🇧 English</option>
  </select>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { setUiLocale } from "../i18n";

const { locale } = useI18n();

function onChange(value) {
  if (value === "vi" || value === "en") setUiLocale(value);
}
</script>

<style scoped>
.lang-switcher {
  min-height: 38px;
  max-width: 100%;
  padding: 7px 10px;
  border: 1px solid var(--border);
  border-radius: 10px;
  background: var(--surface);
  color: var(--text);
  font: inherit;
  cursor: pointer;
}
</style>