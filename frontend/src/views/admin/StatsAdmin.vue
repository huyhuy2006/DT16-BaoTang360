<template>
  <div>
    <h2>Thống kê hành vi tham quan</h2>

    <div v-if="summary" class="stats-grid">
      <div class="card stat"><strong>{{ summary.total_visits }}</strong><span>Lượt xem điểm nhìn</span></div>
      <div class="card stat"><strong>{{ summary.total_hotspot_clicks }}</strong><span>Lượt bấm điểm nóng</span></div>
      <div class="card stat"><strong>{{ summary.total_narration_plays }}</strong><span>Lượt phát thuyết minh</span></div>
      <div class="card stat"><strong>{{ summary.unique_sessions }}</strong><span>Phiên tham quan</span></div>
    </div>

    <!-- Biểu đồ 1: cơ cấu hành vi tham quan -->
    <h3>Biểu đồ cơ cấu hành vi</h3>
    <svg v-if="summary" class="chart-bar" :viewBox="`0 0 320 ${actionBars.length * 34 + 10}`" role="img" aria-label="Biểu đồ cột cơ cấu hành vi tham quan">
      <g v-for="(bar, i) in actionBars" :key="bar.label" :transform="`translate(0, ${i * 34})`">
        <text x="0" y="14" class="chart-label">{{ bar.label }}</text>
        <rect x="90" y="2" :width="bar.width" height="18" class="chart-rect" />
        <text :x="90 + bar.width + 6" y="15" class="chart-value">{{ bar.value }}</text>
      </g>
    </svg>

    <!-- Biểu đồ 2: điểm nhìn được xem nhiều nhất -->
    <h3>Biểu đồ điểm nhìn được xem nhiều nhất</h3>
    <svg v-if="summary" class="chart-bar" :viewBox="`0 0 320 ${sceneBars.length * 34 + 10}`" role="img" aria-label="Biểu đồ cột điểm nhìn được xem nhiều nhất">
      <g v-for="(bar, i) in sceneBars" :key="bar.label + i" :transform="`translate(0, ${i * 34})`">
        <text x="0" y="14" class="chart-label">{{ bar.label }}</text>
        <rect x="90" y="2" :width="bar.width" height="18" class="chart-rect chart-rect-alt" />
        <text :x="90 + bar.width + 6" y="15" class="chart-value">{{ bar.value }}</text>
      </g>
    </svg>

    <h3>Điểm nhìn được xem nhiều nhất</h3>
    <table class="admin-table">
      <thead><tr><th>Điểm nhìn</th><th>Lượt xem</th></tr></thead>
      <tbody>
        <tr v-for="row in summary?.top_scenes" :key="row.scene_id">
          <td>{{ row.scene?.name }}</td>
          <td>{{ row.total }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import apiClient from "../../api/client";

const summary = ref(null);
const MAX_BAR_WIDTH = 180;

function toBars(items) {
  const max = Math.max(1, ...items.map((i) => i.value));
  return items.map((i) => ({ ...i, width: Math.round((i.value / max) * MAX_BAR_WIDTH) }));
}

const actionBars = computed(() => {
  if (!summary.value) return [];
  return toBars([
    { label: "Xem điểm nhìn", value: summary.value.total_visits || 0 },
    { label: "Bấm điểm nóng", value: summary.value.total_hotspot_clicks || 0 },
    { label: "Phát thuyết minh", value: summary.value.total_narration_plays || 0 },
    { label: "Phiên tham quan", value: summary.value.unique_sessions || 0 },
  ]);
});

const sceneBars = computed(() => {
  if (!summary.value?.top_scenes) return [];
  return toBars(
    summary.value.top_scenes.map((row) => ({
      label: row.scene?.name || `#${row.scene_id}`,
      value: row.total,
    })),
  );
});

onMounted(async () => {
  const res = await apiClient.get("/visit-logs/summary");
  summary.value = res.data;
});
</script>

<style scoped>
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px; }
.stat { text-align: center; }
.stat strong { display: block; font-size: 28px; color: var(--color-primary); }
.chart-bar { width: 100%; max-width: 420px; display: block; margin-bottom: 20px; }
.chart-label { font-size: 11px; fill: var(--color-text); }
.chart-value { font-size: 11px; fill: var(--color-text); }
.chart-rect { fill: var(--color-primary); rx: 3; }
.chart-rect-alt { fill: #f59e0b; }
</style>
