<template>
  <div class="foldable-component">
    <div class="foldable-content">
      <div :key="'in-memberships'" class="option">
        <input
          :id="'in-memberships'"
          v-model="selectedInMemberships"
          type="checkbox"
          :value="selectedInMemberships"
        />
        <label :for="'in-memberships'">
          <span>
            {{ t('In Membership') }}
          </span>
        </label>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InMembershipsFilter',
  props: {
    value: {
      type: Number,
      required: true
    },
    id: {
      type: String,
      required: true
    },
    facets: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedInMemberships: this.value
    }
  },
  watch: {
    value() {
      this.selectedInMemberships = this.value
    },
    selectedInMemberships() {
      this.$emit('input', this.selectedInMemberships)
    }
  },
  methods: {
    facetCount(value) {
      let facet = this.facets.find(x => x.id === value)
      return facet ? facet.count : 0
    }
  }
}
</script>
