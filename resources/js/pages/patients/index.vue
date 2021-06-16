<template>
  <div v-if="is_master_loaded" class="main-in">
    <div class="main-content">
      <table class="task-list">
        <thead>
          <tr>
            <th>{{ $t('カルテ番号') }}</th>
            <th>{{ $t('氏名') }}</th>
            <th>{{ $t('性別') }}</th>
            <th>{{ $t('年齢') }}</th>
            <th>{{ $t('生年月日') }}</th>
            <th>{{ $t('電話番号') }}</th>
            <th>{{ $t('詳細') }}</th>
          </tr>
        </thead>
        <tbody v-if="patients.length">
          <tr v-for="item in patients">
            <td>{{ item.id }}</td>
            <td>
                <small>{{ item.kana }}</small>
                <br>
                {{ item.name }}
            </td>
            <td>{{ gender_types[item.gender] }}</td>
            <td>{{ item.age }}</td>
            <td>{{ item.birthday | formatDate }}</td>
            <td>{{ item.phone_number }}</td>
            <td>
              <router-link :to="{ name: 'patient', params: { id: item.id }}" class="btn-primary normal-btn">詳細</router-link>
            </td>
          </tr>
        </tbody>
      </table>
      <pagination
        v-if="pageInfo"
        :page="query.page"
        :page-count="pageInfo.last_page"
        :click-handler="handlePaginate" /> 
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  data() {
    return {
      patients: [],
      query: {
        per_page: 20,
        page: 1,
      },
      pageInfo: undefined,
    }
  },
  computed: mapGetters({
    is_master_loaded: 'state/is_master_loaded',
    gender_types: 'constant/gender_types',
    stuffs: 'data/stuffs',
    menus: 'data/menus',
    rsv_contents: 'data/rsv_contents',
  }),
  mounted() {
    this.searchInfo = this.$route.params.search;
    if(this.searchInfo){
      
      this.query = {
        ...this.query,
        q: this.searchInfo,
      }
    }
    this.getData();
  },
  watch: {
    $route() {      
      this.searchInfo = this.$route.params.search;
      if(this.searchInfo){
        this.query = {
          ...this.query,
          q: this.searchInfo,
        }
      }
      this.getData();
    }
  },
  methods: {
    getData() {
      this.$store.dispatch('state/setIsLoading')
      const qs = this.$utils.getQueryString(this.query)
      axios.get(`/api/clinic/patients?${qs}`)
        .then(res => {
          this.patients = res.data.patients.data;
          this.pageInfo = {
            last_page: res.data.patients.last_page,
          }
          this.$store.dispatch('state/removeIsLoading')
        })
        .catch(error => {
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handlePaginate(pageNum) {
      this.query = {
        ...this.query,
        page: pageNum,
      }
      this.getData()
    },
  }
}
</script>

<style scoped>
</style>
