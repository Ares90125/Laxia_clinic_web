<template>
  <div v-if="is_master_loaded" class="main-in">
    <div class="main-content case-content">
      <div class="staff-header case-header">
          <select class="menu-sort my-1" @change="handleCategoryChange">
            <option value="-1">{{ $t('部位でソート') }}</option>
            <option v-for="(name, id) in specialities" :key="id" :value="id">{{ name }}</option>
          </select>
          <!-- <select class="staff-sort" @change="handleStuffChange">
            <option value="-1">{{ $t('担当者でソート') }}</option>
            <option v-for="(name, id) in stuffs" :key="id" :value="id">{{ name }}</option>
          </select> -->
          <button class="btn btn-primary my-1" @click="handleNewCase"><img src="/img/plus.svg" class="img_plus"> {{ $t('新規症例を追加') }}</button>
      </div>
      <div class="staff-content case-sub-content">
        <div class="case-list">
          <div v-for="(item, index) in cases" :key="index" class="case-one">
            <div class="case-one-in" @click="handleShowCase(item.id)">
              <div class="case-img">
                <p class="before">
                  <img v-if="item.before_photo" :src="'/storage/'+item.before_photo">
                  <img v-else :src="'/img/menu-img.png'">
                </p>
                <p class="after">
                  <img v-if="item.after_photo" :src="'/storage/'+item.after_photo">
                  <img v-else :src="'/img/menu-img.png'">
                </p>
              </div>
              <div class="case-info">
                 <template v-for="(item, key) in item.categories" :value="key">
                  <p class="case-cat" v-if="key <= 3" :key="key">
                    {{ item.name }}
                  </p>
                </template>
                <p class="case-ttl">{{item.name}}</p>
                <p class="case-name"><!--<i class="fas fa-clipboard-list"></i>--><img src="/img/clip_book.svg" style="margin-top: -2px; margin-right:4px;">  <template v-if="item.menus.length">{{ item.menus[0].name}}</template></p>
              </div>
            </div>
          </div>
        </div>
        <pagination
        v-if="pageInfo"
        :page="query.page"
        :page-count="pageInfo.last_page"
        :click-handler="handlePaginate" />
      </div>      
    </div>

    <form-modal
      ref="modal"
      id="case-modal"
      :title="modalInfo.title"
      @cancel="handleModalClose"
      >
      <div v-if="form">
        <div class="create-case-content">
          <div class="form-group row mt-0">
            <div class="col-12">
              <small>{{ $t('タイトル') }}</small>
              <input type="text" v-model="form.cases.name" placeholder="例：奥二重の方の二重切開" :class="{'is-invalid' : errors && errors['cases.name'] }" />
              <div v-if="errors && errors['cases.name']" class="error invalid-feedback">{{ errors['cases.name'][0] }}</div>
            </div>
          </div>
          <div class="form-group row justify-content-center">
            <div class="col-4">
              <small class="text-center">{{ $t('Before画像') }}</small>
              <file-upload
                v-if="form.cases.before_photo"
                ref="beforeFileUploadComponent"
                uploadUrl="/api/clinic/profile/photoupload"
                name="before_photo"
                :photo="'/storage/'+form.cases.before_photo"
                @file-upload-success="handleFileSaved"
                @file-removed="hanleFileRemove"
                @file-added="handleFileAdded"
              />
              <file-upload
                v-else
                ref="beforeFileUploadComponent"
                uploadUrl="/api/clinic/profile/photoupload"
                name="before_photo"
                :photo="form.cases.before_photo"
                @file-upload-success="handleFileSaved"
                @file-removed="hanleFileRemove"
                @file-added="handleFileAdded"
              />
            </div>
            <div class="col-4">
              <small class="text-center">{{ $t('After画像') }}</small>
              <file-upload
                v-if="form.cases.after_photo"
                ref="afterFileUploadComponent"
                uploadUrl="/api/clinic/cases/photoupload"
                name="after_photo"
                :photo="'/storage/'+form.cases.after_photo"
                @file-upload-success="handleFileSaved"
                @file-removed="hanleFileRemove"
                @file-added="handleFileAdded"
              />
              <file-upload
                v-else
                ref="afterFileUploadComponent"
                uploadUrl="/api/clinic/cases/photoupload"
                name="after_photo"
                :photo="form.cases.after_photo"
                @file-upload-success="handleFileSaved"
                @file-removed="hanleFileRemove"
                @file-added="handleFileAdded"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <small>{{ $t('メニュー') }}</small>
              <select v-model="form.cases.menu_id" :class="{'is-invalid' : errors && errors['cases.menu_id'] }" @change="handleMenuChange($event)">
                <option></option>
                <option v-for="(name, id) in menus" :key="id" :value="id" >{{ name }}</option>
              </select>
              <div v-if="errors && errors['cases.menu_id']" class="error invalid-feedback">{{ errors['cases.menu_id'][0] }}</div>
            </div>
          </div>
          <div class="form-group" v-if="menu_detail_data.length">
            <div class="row">
              <div class="col-7">
                <small>{{ $t('メニュー名') }}</small>
              </div>
              <div class="col-3">
                <small>{{ $t('料金') }}</small>
              </div>
              <div class="col-2 text-center">
                <small>{{ $t('削除') }}</small>
              </div>
            </div>
            <!-- <template v-for="(item, id) in menu_detail_data" :value="id">
              <MenuDetail :m_menu_data="item" :m_idx="id" :key="id"/>
            </template> -->
            <MenuDetail />
          </div>
          <div class="form-group row">
            <div class="col-12">              
              <small>{{ $t('カテゴリー') }}</small>
                <multiselect
                  v-model="selected_categories"
                  :options="category_options"
                  :multiple="true"
                  group-values="children"
                  group-label="group_name"
                  :group-select="true"
                  track-by="name"
                  label="name"
                  selectLabel=""
                  selectGroupLabel=""
                  placeholder=""
                  selectedLabel="選択済み"
                  deselectLabel="削除"
                  deselectGroupLabel="削除"
                ></multiselect>
                <div v-if="errors && errors['categories']" class="error invalid-feedback d-block">{{ errors['categories'][0] }}</div>
            </div>
          </div>          
          
          <!-- <div class="form-group row">
            <div class="col-12">
                <small>{{ $t('担当者') }}</small>
                <select v-model="form.cases.stuff_id" :class="{'is-invalid' : errors && errors['cases.stuff_id'] }">
                  <option></option>
                  <option v-for="(name, id) in stuffs" :key="id" :value="id">{{ name }}</option>
                </select>
                <div v-if="errors && errors['cases.stuff_id']" class="error invalid-feedback">{{ errors['cases.stuff_id'][0] }}</div>
            </div>
          </div> -->
          <div class="form-group row">
            <div class="col-6">
                <small>{{ $t('年齢') }}</small>
                <select v-model="form.cases.patient_age" :class="{'is-invalid' : errors && errors['cases.patient_age'] }">
                  <option></option>
                  <option v-for="i in 7" :key="i" :value="i * 10">{{ i * 10 }}{{ $t('才') }}</option>
                </select>
                <div v-if="errors && errors['cases.patient_age']" class="error invalid-feedback">{{ errors['cases.patient_age'][0] }}</div>
            </div>
            <div class="col-6">
              <small>{{ $t('性別') }}</small>
              <select v-model="form.cases.patient_gender" :class="{'is-invalid' : errors && errors['cases.patient_gender'] }">
                <!-- <option></option> -->
                <option v-for="(name, id) in genders" :key="id" :value="id">{{ name }}</option>
              </select>
              <div v-if="errors && errors['cases.patient_gender']" class="error invalid-feedback">{{ errors['cases.patient_gender'][0] }}</div>
            </div>
          </div>

          <div class="create-case-text">
            <small>{{ $t('施術の解説') }}</small>
            <textarea v-model="form.cases.case_description" :class="{'is-invalid' : errors && errors['cases.case_description'] }" placeholder="例：この施術は目頭を切る施術になります。"></textarea>
            <div v-if="errors && errors['cases.case_description']" class="error invalid-feedback">{{ errors['cases.case_description'][0] }}</div>
          </div>
          <div class="create-case-text">
            <small>{{ $t('副作用・リスク') }}</small>
            <textarea v-model="form.cases.treat_risk" :class="{'is-invalid' : errors && errors['cases.treat_risk'] }" placeholder="例：施術後一週間ほど腫れる場合があります。"></textarea>
            <div v-if="errors && errors['cases.treat_risk']" class="error invalid-feedback">{{ errors['cases.treat_risk'][0] }}</div>
          </div>
          <div class="create-case-text">
            <small>{{ $t('担当ドクターからのコメント') }}</small>
            <textarea v-model="form.cases.doctor_opinion" placeholder="例：この施術は〇〇な方に向いているかと思います。"></textarea>
            <!-- <div v-if="errors && errors['cases.treat_risk']" class="error invalid-feedback">{{ errors['cases.treat_risk'][0] }}</div> -->
          </div>
        </div>        
      </div>
      <template v-slot:footer>
        <button type="button" class="btn btn-primary" @click="handleUpdateCase">{{ modalInfo.confirmBtnTitle }}</button>
      </template>
    </form-modal>

    <form-modal
      ref="viewModal"
      id="case-view-modal"
      :title="modalInfo.title"
      >
      <div v-if="form" class="create-menu-content view-modal-content">
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('タイトル') }}</small>
            <p>{{form.cases.name}} 埋没法ダブル</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('Before画像') }}</small>
            <div class="view-img-panel">
              <img v-if="form.cases.before_photo" :src="'/storage/'+form.cases.before_photo">
              <!-- <img src="/img/shopify_b.jpg">
              <img src="/img/shopify_b.jpg">
              <img src="/img/shopify_b.jpg"> -->
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('After画像') }}</small>
            <div class="view-img-panel">
              <img v-if="form.cases.after_photo" :src="'/storage/'+form.cases.after_photo">
              <!-- <img src="/img/shopify_b.jpg">
              <img src="/img/shopify_b.jpg">
              <img src="/img/shopify_b.jpg"> -->
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="row">
            <div class="col-9">
              <small>{{ $t('メニュー名') }}</small>
            </div>
            <div class="col-3">
              <small>{{ $t('料金') }}</small>
            </div>
          </div>
          <div class="row mb-2" v-for="(item, idx) in selected_menu_details" :key="idx">
            <div class="col-9">
              <p>{{item.name}}</p>
            </div>
            <div class="col-3">
              <p>{{item.price}}</p>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('カテゴリー') }}</small>
            <div class="view-cate-panel">
              <template v-for="(item, idx) in selected_categories" :value="id">
                <p :key="idx">
                  {{item.name}}
                </p>
              </template>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="row">
            <div class="col-6">
              <small>{{ $t('年齢') }}</small>
              <p>{{form.cases.patient_age}}{{ $t('才') }}</p>
            </div>
            <div class="col-6">
              <small>{{ $t('性別') }}</small>
              <template v-for="(name, id) in genders" :value="id">
                <p v-if="form.cases.patient_gender == id" :key="id">
                  {{name}}
                </p>
              </template>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('施術の解説') }}</small>
            <p>{{form.cases.case_description}}</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('副作用・リスク') }}</small>
            <p>{{form.cases.treat_risk}}</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('担当ドクターからのコメント') }}</small>
            <p>{{form.cases.doctor_opinion}}</p>
          </div>
        </div>
      </div>
      <div class="view-modal-footer">
        <button type="button" class="btn btn-primary" @click="handleShowEditMenu">{{ modalInfo.confirmBtnTitle }}</button>
      </div>
    </form-modal>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import Multiselect from 'vue-multiselect'

export default {
  middleware: 'auth',

  data() {
    return {
      cases: [],
      form: undefined,
      errors: undefined,
      tmp: {
        beforeFileChanged: false,
        afterFileChanged: false,
        cases: {
          menu_id: '',
          stuff_id: '',
          patient_age: '',
          name: '',
          patient_gender: '',
          case_description: '',
          doctor_opinion: '',
          treat_risk: '',
          before_photo: '',
          after_photo: '',
        },
        categoires: null,
      },
      query: {
        per_page: 12,
        page: 1,
        stuff_id: '',
        category_id: ''
      },
      modalInfo: {
        title: '',
        confirmBtnTitle: '',
      },
      pageInfo: undefined,
      fileUploadCount: 0,
      selected_categories: [],
      selected_menu_details: [],
    }
  },

  computed: {
    ...mapGetters({
      is_master_loaded: 'state/is_master_loaded',
      specialities: 'data/specialities',
      categories: 'data/categories',
      stuffs: 'data/stuffs',
      menus: 'data/menus',
      menu_details: 'data/menu_details',
      genders: 'constant/gender_types'
    }),
    category_options() {
      return this.categories.map(el => {
        return {
          group_name: el.name,
          children: el.all_children.map(item => {
            return {
              id: item.id,
              name: item.name,
            };
          }),
        };
      });
    },
    menu_detail_data() {
      return this.$store.state.data.selected_menus;
    },
  },

  mounted() {
    this.getData();
  },

  methods: {
    handleMenuChange(e){
      this.menu_details.forEach(item => {
        if(item.id == e.target.value){
          this.selected_menu_details.push({
            id: item.id,
            name: item.name,
            price: item.price
          })
        }
      })
      this.$store.dispatch('data/addMenuDetails', { data : this.selected_menu_details })
    },
    getData() {
      this.$store.dispatch('state/setIsLoading')
      const qs = this.$utils.getQueryString(this.query)
      axios.get(`/api/clinic/cases?${qs}`)
        .then(res => {
          this.cases = res.data.cases.data;
          this.pageInfo = {
            last_page: res.data.cases.last_page,
          }
          this.$store.dispatch('state/removeIsLoading')
        })
        .catch(error => {
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleShowCase(id) {
      let selected = this.cases.find(el => el.id == id);
      this.form = {
        ...this.tmp,
        cases: { ...selected },
      }
      this.selected_categories = []
      this.categories.forEach(item => {
        item.all_children.forEach(child => {
          if (selected.categories.map(item => item.id).includes(child.id)) {
            this.selected_categories.push({
              id: child.id,
              name: child.name
            })
          }
        })
      })

      this.selected_menu_details = [];
      this.menu_details.forEach(item => {
      if(selected.menus.map(item => item.id).includes(item.id)){
          this.selected_menu_details.push({
            id: item.id,
            name: item.name,
            price: item.price
          })
        }
      })
      this.$store.dispatch('data/addMenuDetails', { data : this.selected_menu_details })

      this.errors = undefined
      this.modalInfo = {
        title: '症例の詳細',
        confirmBtnTitle: '症例内容を編集する'
      }
      this.$refs.viewModal.show()
    },

    handleShowEditMenu(){
      this.modalInfo = {
        title: '症例を追加する',
        confirmBtnTitle: '症例を追加する'
      }
      this.$refs.viewModal.hide();
      this.$refs.modal.show();
    },

    handleNewCase() {
      this.form = {
        ...this.tmp,
        cases: { ...this.tmp.cases }
      }
      this.selected_categories = [];
      this.selected_menu_details = [];
      this.$store.dispatch('data/addMenuDetails', { data : this.selected_menu_details })
      this.modalInfo = {
        title: '症例を追加する',
        confirmBtnTitle: '症例を追加する'
      }
      this.$refs.modal.show();
    },

    handleUpdateCase() {
      this.$store.dispatch('state/setIsLoading')
      let flg = false;
      if (this.form.beforeFileChanged) {
        flg = true
        this.$refs.beforeFileUploadComponent.processQueue();
      }
      if (this.form.afterFileChanged) {
        flg = true
        this.$refs.afterFileUploadComponent.processQueue();
      }
      if (!flg) {
        this.handleSaveCase();
      }
    },

    handleUpdateCase1(){
      this.$refs.beforeFileUploadComponent.processQueue();
    },

    handleSaveCase() {
      let isCreate = true
      let url = '/api/clinic/cases';
      if (this.form.cases.id) {
        url += `/${this.form.cases.id}`
        isCreate = false
      }
      this.form = {
        ...this.form,
        categories: this.selected_categories.map(el => el.id),
        menus: this.menu_detail_data.map(el => el.id)
      }

      axios.post(url, this.form)
        .then(res => {
          this.$store.dispatch('state/removeIsLoading')
          this.errors = undefined
          this.fileUploadCount = 0
          this.$refs.modal.hide()
          this.$swal({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            title: '登録しました。',
            icon: 'success',
          })
          if (isCreate) {
            this.query = {
              ...this.query,
              page: 1,
            }
          }
          this.getData()
        })
        .catch(error => {
          this.errors = { ...error.response.data.errors }
          this.form.beforeFileChanged = false
          this.form.afterFileChanged = false
          this.fileUploadCount = 0
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

    handleModalClose() {
      this.$refs.beforeFileUploadComponent.removeAllFiles()
      this.$refs.afterFileUploadComponent.removeAllFiles()
    },

    handleFileSaved(filePath, name) {
      this.form = {
        ...this.form,
        cases: {
          ...this.form.cases,
          [name]: filePath,
          [name + '_thumb_url']: ''
        },
      }

      this.fileUploadCount++;
      if (this.form.beforeFileChanged && this.form.afterFileChanged) {
        if (this.fileUploadCount == 2) this.handleSaveCase();
      } else {
        this.handleSaveCase();
      }
    },

    hanleFileRemove(name) {
      let changeKey = ''
      if (name == 'before_photo') {
        changeKey = 'beforeFileChanged'
      } else {
        changeKey = 'afterFileChanged'
      }
      this.form[changeKey] = false;
      this.form.cases[name] = ''
    },

    handleFileAdded(flg, name) {
      let changeKey = ''
      if (name == 'before_photo') {
        changeKey = 'beforeFileChanged'
      } else {
        changeKey = 'afterFileChanged'
      }
      this.form[changeKey] = flg;
    },

    handleCategoryChange(e) {
      e.preventDefault();
      this.query.category_id = e.target.value
      this.getData()
    },

    handleStuffChange(e) {
      e.preventDefault();
      this.query.stuff_id = e.target.value
      this.getData()
    },
  }
}
</script>

<style scoped>
.case-content{
  padding: 0px 30px;
}
.case-header{
      border-bottom: 0px;
}
.form-group{
  margin-top: 1.5rem;
}
.form-group input, .form-group select{
  min-height: 40px;
  padding: 3px 12px;
  width: 100%;
}
.create-case-content{
  padding: 0 5rem;
}
div.create-menu-content{
  padding: 0 4rem;
}
.view-modal-footer{
  margin-top: 4rem;
  display: flex;
  justify-content: center;
}
</style>
