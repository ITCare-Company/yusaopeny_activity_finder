<template>
  <Foldable
    :label="t('Duration(s)')"
    :collapse-id="id + '-toggle'"
    :counter="filtersCount"
    class="duration-filter-component"
  >
    <div v-for="duration in durations" :key="id + '-duration-' + duration.value" class="option">
      <input
        :id="id + '-duration-' + duration.value"
        v-model="selectedDurations"
        type="checkbox"
        :value="duration.value"
      />
      <label :for="id + '-duration-' + duration.value">{{ duration.label }}</label>
    </div>
  </Foldable>
</template>

<script>
import Foldable from '@/components/Foldable.vue'

export default {
  name: 'DurationsFilter',
  components: {
    Foldable
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    id: {
      type: String,
      required: true
    },
    durations: {
      type: Array,
      required: true
    },
    facets: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedDurations: this.value
    }
  },
  computed: {
    filtersCount() {
      return this.selectedDurations.length
    }
  },
  watch: {
    value() {
      this.selectedDurations = this.value
    },
    selectedDurations() {
      this.$emit('input', this.selectedDurations)
    }
  },
  methods: {
    facetCount(value) {
      let facet = this.facets.find(x => x.filter === value)
      return facet ? facet.count : 0
    }
  }
}
</script>
