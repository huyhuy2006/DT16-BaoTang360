<template><button class="btn-primary" @click="toggleTheme">{{ theme === 'light' ? '🌙 Chủ đề tối' : '☀ Chủ đề sáng' }}</button></template>
<script setup>import { useTheme } from '../composables/useTheme'; const {theme,toggleTheme}=useTheme();</script>
