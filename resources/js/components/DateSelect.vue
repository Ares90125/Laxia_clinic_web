<template>
  <div class="date-select--wrapper">
    <select class="form-control" name="DOBYear">
    </select>
    <select class="form-control" name="DOBMonth">
    </select>
    <select class="form-control" name="DOBDay">
    </select>
  </div>
</template>

<script>
export default {
  name: 'DateSelect',

  props: {
    name: {
      type: String,
      default: 'date'
    },
    value: {
      type: Boolean,
      default: false
    },
  },

  data: () => ({
  }),

  computed: {
    
  },

  methods: {
  }
}
</script>
