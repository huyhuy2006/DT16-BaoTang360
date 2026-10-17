import { computed, ref } from 'vue';

const current = ref(localStorage.getItem('theme') || 'light');
let initialized = false;

function apply(value) {
  current.value = value;
  document.documentElement.setAttribute('data-theme', value);
  localStorage.setItem('theme', value);
}

export function useTheme() {
  if (!initialized && typeof document !== 'undefined') {
    apply(current.value);
    initialized = true;
  }
  const toggleTheme = () => apply(current.value === 'light' ? 'dark' : 'light');
  return { theme: computed(() => current.value), setTheme: apply, toggleTheme };
}
