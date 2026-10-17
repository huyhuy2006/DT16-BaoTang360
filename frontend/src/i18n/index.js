import { createI18n } from "vue-i18n";
import vi from "./locales/vi";
import en from "./locales/en";

const savedLocale = localStorage.getItem("ui_locale") || "vi";

const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: savedLocale,
  fallbackLocale: "vi",
  messages: { vi, en },
});

export function setUiLocale(locale) {
  i18n.global.locale.value = locale;
  localStorage.setItem("ui_locale", locale);
  document.documentElement.setAttribute("lang", locale);
}

export default i18n;
