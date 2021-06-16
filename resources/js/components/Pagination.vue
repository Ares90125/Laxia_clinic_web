<template>
  <div v-if="currentPage" class="pagination-wrapper">
    <paginate
      v-model="currentPage"
      :page-count="pageCount"
      :page-range="pageRange"
      :margin-pages="marginPages"
      :click-handler="clickHandler"
      :prev-text="`<i class='bi bi-chevron-left'></i> 前へ`"
      :next-text="`次へ <i class='bi bi-chevron-right'></i>`"
      :container-class="'pagination'"
      :page-class="'page-item'"
      :page-link-class="'page-link'" >
    </paginate>
  </div>
</template>

<script>
import Paginate from 'vuejs-paginate'

export default {
  name: 'Pagination',

  components: {
    Paginate
  },
  
  props: {
    page: {
      type: Number,
      default: 1
    },
    pageCount: {
      type: Number,
      default: 1
    },
    pageRange: {
      type: Number,
      default: 5
    },
    marginPages: {
      type: Number,
      default: 1
    },
    clickHandler: {
      type: Function,
      default: function () {
        return ''
      }
    }
  },
  
  data() {
    return {
      currentPage: this.page
    }
  },

  watch: { 
    page: function(newVal, oldVal) {
      this.currentPage = newVal
    }
  }
}
</script>
