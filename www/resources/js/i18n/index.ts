import { createI18n } from 'vue-i18n'
import en from './en.json'
import hu from './hu.json'

export default createI18n({
  legacy: false,
  locale: localStorage.getItem('lang') || 'en',
  fallbackLocale: 'en',
  messages: { en, hu }
})