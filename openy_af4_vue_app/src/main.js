import { createApp, configureCompat } from 'vue'
// W3: compat MODE 2 — Vue 2 APIs available at runtime (filter/mixin/use).
// Remove configureCompat call when @vue/compat is dropped (after W5).
configureCompat({ MODE: 2 })

import BootstrapVue from 'bootstrap-vue'
import App from '@/App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faFilter,
  faCalendar,
  faMoneyBill,
  faClock,
  faChevronDown,
  faChevronUp,
  faBookmark,
  faPlusCircle,
  faMinusCircle,
  faSortAmountDown
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add([
  faFilter,
  faCalendar,
  faMoneyBill,
  faClock,
  faChevronDown,
  faChevronUp,
  faBookmark,
  faPlusCircle,
  faMinusCircle,
  faSortAmountDown
])

// Listen to custom event to track events in Google Analytics.
document.addEventListener('openy_activity_finder_event', e => {
  const { action, label, value, category } = e.detail

  if (window.gtag) {
    window.gtag('event', action, {
      event_category: category,
      event_label: label,
      value: value
    })
  } else if (window.ga) {
    window.ga('send', 'event', category, action, label, value)
  }
})

const app = createApp({
  components: { 'activity-finder': App }
})

// TODO(W4): remove when BootstrapVue replaced with hand-rolled components
app.use(BootstrapVue)

app.component('font-awesome-icon', FontAwesomeIcon)

// TODO(W5-P0): remove when filter pipes replaced with function calls in templates
app.filter('capitalize', function(str) {
  if (!str) return ''
  str = str.toString()
  return str[0].toUpperCase() + str.slice(1)
})
app.filter('t', function(value, args, options = { context: 'Activity Finder' }) {
  return window.Drupal.t(value, args, options)
})
app.filter('formatPlural', function(
  value,
  singular,
  plural,
  args,
  options = { context: 'Activity Finder' }
) {
  if (!value) return ''
  return window.Drupal.formatPlural(value, singular, plural, args, options)
})

// TODO(W5-P1): replace mixin with composable / global properties
app.mixin({
  computed: {
    isIosMobile() {
      return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream
    }
  },
  methods: {
    trackEvent(action, label, value = 0, category = 'Activity Finder') {
      const event = new CustomEvent('openy_activity_finder_event', {
        detail: { action, label, value, category }
      })
      document.dispatchEvent(event)
    },
    t(value, args, options = { context: 'Activity Finder' }) {
      return window.Drupal.t(value, args, options)
    },
    formatPlural(value, singular, plural, args, options = { context: 'Activity Finder' }) {
      return window.Drupal.formatPlural(value, singular, plural, args, options)
    },
    getCookie(cname) {
      const name = cname + '='
      const decodedCookie = decodeURIComponent(document.cookie)
      const ca = decodedCookie.split(';')
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i]
        while (c[0] === ' ') c = c.slice(1)
        if (c.startsWith(name)) return c.slice(name.length, c.length)
      }
      return ''
    }
  }
})

app.mount('#activity-finder')
