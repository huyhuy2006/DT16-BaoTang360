<template>
  <section class="tour-page">
    <div class="tour-hero">
      <div class="tour-hero-copy">
        <span class="tour-eyebrow">DT-16 · VIRTUAL MUSEUM</span>
        <h1>{{ pageTitle }}</h1>
        <p class="tour-intro">{{ pageSubtitle }}</p>
      </div>
      <div class="tour-hero-icon" aria-hidden="true">🏛️</div>
    </div>

    <section v-if="recommendations.length" class="recommendations">
      <div class="recommendation-heading">
        <span class="recommendation-icon" aria-hidden="true">✦</span>
        <div>
          <h2>{{ recommendationTitle }}</h2>
          <p>{{ recommendationSubtitle }}</p>
        </div>
      </div>
      <div class="topic-list">
        <span v-for="topic in recommendations" :key="topic" class="topic-chip">
          {{ topic }}
        </span>
      </div>
      <small class="engine-label">
        {{ recommendationEngine === 'python' ? 'Python recommendation service' : 'Fallback recommendations' }}
      </small>
    </section>

    <div class="section-heading">
      <div>
        <h2>{{ toursHeading }}</h2>
        <p>{{ tours.length }} {{ countLabel }}</p>
      </div>
      <span class="section-mark" aria-hidden="true">✦</span>
    </div>

    <p v-if="error" class="tour-error" role="alert">{{ error }}</p>

    <div v-if="loading" class="tour-grid">
      <div v-for="n in 3" :key="n" class="tour-skeleton" aria-hidden="true">
        <div class="skeleton-image"></div>
        <div class="skeleton-line"></div>
        <div class="skeleton-line short"></div>
      </div>
    </div>

    <div v-else-if="tours.length" class="tour-grid">
      <article v-for="(tour, index) in tours" :key="tour.id" class="tour-card">
        <RouterLink
          :to="{ name: 'tour-player', params: { id: tour.id } }"
          class="tour-link"
        >
          <div class="tour-cover">
            <img
              v-if="tour.cover_image"
              :src="storageUrl(tour.cover_image)"
              :alt="tour.name"
              loading="lazy"
              decoding="async"
              @error="onImageError"
            />
            <div v-if="!tour.cover_image" class="cover-placeholder" aria-hidden="true">
              <span>🏺</span>
            </div>
            <span class="tour-number">{{ String(index + 1).padStart(2, '0') }}</span>
            <span v-if="tour.theme" class="tour-theme">{{ tour.theme }}</span>
          </div>

          <div class="tour-card-content">
            <h3>{{ tour.name }}</h3>
            <p class="tour-description">{{ tour.description }}</p>
            <div class="tour-meta">
              <span aria-hidden="true">⌖</span>
              <span>{{ tour.steps_count }} {{ stopsLabel }}</span>
            </div>
            <span class="tour-open">{{ openTourLabel }} <span aria-hidden="true">↗</span></span>
          </div>
        </RouterLink>

        <div class="tour-favorite">
          <FavoriteButton target-type="tour" :target-id="tour.id" />
        </div>
      </article>
    </div>

    <div v-else-if="!error && !loading" class="tour-empty">
      <span aria-hidden="true">🏛️</span>
      <h2>{{ emptyTitle }}</h2>
      <p>{{ emptySubtitle }}</p>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import apiClient from '../api/client'
import FavoriteButton from '../components/FavoriteButton.vue'

const { locale } = useI18n()
const tours = ref([])
const recommendations = ref([])
const recommendationEngine = ref('fallback')
const error = ref('')
const loading = ref(true)

const isEnglish = computed(() => locale.value === 'en')
const pageTitle = computed(() => isEnglish.value ? 'Journeys through heritage' : 'Hành trình khám phá di sản')
const pageSubtitle = computed(() => isEnglish.value
  ? 'Explore curated museum routes, discover remarkable objects, and experience stories across time.'
  : 'Khám phá những lộ trình được tuyển chọn, tìm hiểu hiện vật và trải nghiệm những câu chuyện di sản.')
const recommendationTitle = computed(() => isEnglish.value ? 'Recommended themes' : 'Chủ đề gợi ý')
const recommendationSubtitle = computed(() => isEnglish.value ? 'Ideas for your next museum journey' : 'Gợi ý cho chuyến tham quan tiếp theo')
const toursHeading = computed(() => isEnglish.value ? 'Explore all journeys' : 'Khám phá các lộ trình')
const countLabel = computed(() => isEnglish.value ? 'journeys to discover' : 'lộ trình đang chờ bạn khám phá')
const stopsLabel = computed(() => isEnglish.value ? 'stops' : 'điểm dừng')
const openTourLabel = computed(() => isEnglish.value ? 'Explore journey' : 'Khám phá lộ trình')
const emptyTitle = computed(() => isEnglish.value ? 'No journeys yet' : 'Chưa có lộ trình tham quan')
const emptySubtitle = computed(() => isEnglish.value ? 'Please check back later.' : 'Vui lòng quay lại sau.')
const onImageError = (event) => {
  event.target.style.display = 'none'
}

function storageUrl(path) {
  if (!path) return ''
  if (/^https?:\/\//i.test(path)) return path
  const base = import.meta.env.VITE_STORAGE_BASE_URL || 'http://localhost:8080/storage'
  return `${base.replace(/\/$/, '')}/${path.replace(/^\//, '')}`
}

onMounted(async () => {
  try {
    const results = await Promise.allSettled([
      apiClient.get('/tours'),
      apiClient.get('/recommendations')
    ])

    const tourResponse = results[0]
    const recommendationResponse = results[1]

    if (tourResponse.status === 'fulfilled') {
      const payload = tourResponse.value.data
      tours.value = Array.isArray(payload) ? payload : (payload.data || [])
    } else {
      error.value = isEnglish.value
        ? 'Unable to load journeys. Please try again.'
        : 'Không thể tải danh sách tour. Vui lòng thử lại.'
    }

    if (recommendationResponse.status === 'fulfilled') {
      recommendations.value = recommendationResponse.value.data.recommendations || []
      recommendationEngine.value = recommendationResponse.value.data.engine || 'fallback'
    }
  } catch {
    error.value = isEnglish.value
      ? 'An unexpected error occurred while loading journeys.'
      : 'Đã xảy ra lỗi khi tải danh sách tour.'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.tour-page {
  width: 100%;
  max-width: 1440px;
  margin: 0 auto;
  padding: 18px 0 56px;
  color: var(--text, #182338);
}
.tour-hero {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 28px;
  min-height: 220px;
  overflow: hidden;
  padding: clamp(28px, 5vw, 60px);
  border-radius: 24px;
  background: linear-gradient(115deg, #142b42 0%, #244d5b 56%, #98784c 140%);
  color: #fff;
  box-shadow: 0 18px 48px rgb(21 42 60 / 15%);
}
.tour-hero::after {
  position: absolute;
  right: -70px;
  bottom: -190px;
  width: 420px;
  height: 420px;
  border: 1px solid rgb(255 255 255 / 18%);
  border-radius: 50%;
  content: "";
  box-shadow: 0 0 0 36px rgb(255 255 255 / 4%), 0 0 0 72px rgb(255 255 255 / 4%);
  pointer-events: none;
}
.tour-hero-copy { position: relative; z-index: 1; max-width: 760px; }
.tour-eyebrow {
  display: inline-block;
  margin-bottom: 16px;
  color: #e8c99a;
  font-size: .75rem;
  font-weight: 800;
  letter-spacing: .16em;
}
.tour-hero h1 {
  margin: 0;
  color: #fff;
  font-size: clamp(2rem, 4vw, 3.4rem);
  line-height: 1.12;
  letter-spacing: -.035em;
}
.tour-intro {
  max-width: 680px;
  margin: 18px 0 0;
  color: rgb(255 255 255 / 83%);
  font-size: clamp(.98rem, 1.4vw, 1.1rem);
  line-height: 1.8;
}
.tour-hero-icon {
  position: relative;
  z-index: 1;
  display: grid;
  flex: 0 0 112px;
  width: 112px;
  height: 112px;
  place-items: center;
  border: 1px solid rgb(255 255 255 / 30%);
  border-radius: 28px;
  background: rgb(255 255 255 / 10%);
  font-size: 3.5rem;
  backdrop-filter: blur(8px);
}
.recommendations {
  display: grid;
  gap: 18px;
  margin-top: 24px;
  padding: 24px;
  border: 1px solid var(--border, #e0e5ec);
  border-radius: 18px;
  background: var(--surface, #fff);
}
.recommendation-heading { display: flex; align-items: center; gap: 14px; }
.recommendation-icon {
  display: grid;
  flex: 0 0 44px;
  width: 44px;
  height: 44px;
  place-items: center;
  border-radius: 14px;
  background: #f5ead8;
  color: #8c6331;
  font-size: 1.4rem;
}
.recommendation-heading h2 { margin: 0; font-size: 1.05rem; }
.recommendation-heading p { margin: 5px 0 0; color: var(--muted, #667085); font-size: .9rem; }
.topic-list { display: flex; flex-wrap: wrap; gap: 10px; }
.topic-chip {
  padding: 8px 14px;
  border: 1px solid var(--border, #e0e5ec);
  border-radius: 999px;
  background: var(--bg, #f6f8fb);
  font-size: .88rem;
  font-weight: 600;
}
.engine-label { color: var(--muted, #667085); }
.section-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin: 36px 0 20px;
}
.section-heading h2 { margin: 0; font-size: clamp(1.4rem, 2vw, 1.8rem); letter-spacing: -.025em; }
.section-heading p { margin: 7px 0 0; color: var(--muted, #667085); font-size: .92rem; }
.section-mark { color: #ad8956; font-size: 1.7rem; }
.tour-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 22px;
}
.tour-card {
  display: flex;
  min-width: 0;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid var(--border, #e0e5ec);
  border-radius: 18px;
  background: var(--surface, #fff);
  box-shadow: 0 8px 24px rgb(21 42 60 / 5%);
  transition: transform .2s ease, box-shadow .2s ease;
}
.tour-card:hover { transform: translateY(-4px); box-shadow: 0 18px 38px rgb(21 42 60 / 12%); }
.tour-link { display: block; color: inherit; text-decoration: none; }
.tour-cover {
  position: relative;
  overflow: hidden;
  aspect-ratio: 16 / 9;
  background: linear-gradient(135deg, #203448, #a88a5e);
}
.tour-cover img { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
.tour-card:hover .tour-cover img { transform: scale(1.04); }
.cover-placeholder {
  display: grid;
  width: 100%;
  height: 100%;
  place-items: center;
  background: radial-gradient(circle at 50% 40%, #536d78, #1c3145);
  color: #f4d8a7;
  font-size: 3.4rem;
}
.tour-number {
  position: absolute;
  top: 14px;
  left: 14px;
  display: grid;
  width: 40px;
  height: 40px;
  place-items: center;
  border: 1px solid rgb(255 255 255 / 45%);
  border-radius: 12px;
  background: rgb(18 36 52 / 72%);
  color: #fff;
  font-size: .8rem;
  font-weight: 800;
  backdrop-filter: blur(6px);
}
.tour-theme {
  position: absolute;
  right: 14px;
  bottom: 14px;
  max-width: calc(100% - 28px);
  padding: 7px 12px;
  overflow: hidden;
  border: 1px solid rgb(255 255 255 / 35%);
  border-radius: 999px;
  background: rgb(18 36 52 / 78%);
  color: #fff;
  font-size: .78rem;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.tour-card-content { display: flex; flex: 1; flex-direction: column; padding: 22px 22px 14px; }
.tour-card-content h3 { margin: 0; font-size: 1.2rem; line-height: 1.4; }
.tour-description {
  display: -webkit-box;
  min-height: 3.2em;
  margin: 12px 0 18px;
  overflow: hidden;
  color: var(--muted, #667085);
  font-size: .94rem;
  line-height: 1.65;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}
.tour-meta { display: flex; align-items: center; gap: 8px; color: var(--muted, #667085); font-size: .86rem; }
.tour-meta span:first-child { color: #a27b48; font-size: 1.1rem; }
.tour-open { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--border, #e0e5ec); color: var(--primary, #275b69); font-size: .9rem; font-weight: 750; }
.tour-favorite { padding: 0 22px 20px; }
.tour-error { padding: 14px 18px; border: 1px solid #f2c5c5; border-radius: 12px; background: #fff2f2; color: #a12626; }
.tour-empty { padding: 64px 24px; border: 1px dashed var(--border, #e0e5ec); border-radius: 18px; text-align: center; }
.tour-empty > span { font-size: 3rem; }
.tour-empty h2 { margin-bottom: 8px; }
.tour-empty p { color: var(--muted, #667085); }
.tour-skeleton { overflow: hidden; padding: 12px; border: 1px solid var(--border, #e0e5ec); border-radius: 18px; background: var(--surface, #fff); }
.skeleton-image, .skeleton-line { border-radius: 8px; background: var(--bg, #edf0f4); animation: shimmer 1.4s infinite alternate; }
.skeleton-image { aspect-ratio: 16 / 9; }
.skeleton-line { height: 16px; margin-top: 18px; }
.skeleton-line.short { width: 58%; margin-top: 10px; }
@keyframes shimmer { from { opacity: .55; } to { opacity: 1; } }
@media (min-width: 1200px) { .tour-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (min-width: 1550px) { .tour-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media (max-width: 900px) {
  .tour-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
  .tour-hero-icon { flex-basis: 84px; width: 84px; height: 84px; font-size: 2.6rem; }
}
@media (max-width: 600px) {
  .tour-page { padding-top: 8px; }
  .tour-hero { min-height: auto; padding: 28px 22px; border-radius: 18px; }
  .tour-hero-icon { display: none; }
  .tour-eyebrow { font-size: .66rem; }
  .tour-grid { grid-template-columns: minmax(0, 1fr); }
  .tour-card-content { padding: 18px 18px 12px; }
  .tour-favorite { padding: 0 18px 18px; }
  .recommendations { padding: 18px; }
  .section-heading { margin-top: 28px; }
}
@media (prefers-reduced-motion: reduce) {
  .tour-card, .tour-cover img, .skeleton-image, .skeleton-line { transition: none; animation: none; }
}
</style>
