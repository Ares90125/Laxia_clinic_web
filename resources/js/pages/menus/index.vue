<template>
  <div v-if="is_master_loaded" class="main-in">
    <div class="main-content">
      <div class="staff-header">
        <p><a href="#" :class="{'active': query.status == 1}" @click="handleStatusChange(1)">{{ $t('掲載中') }}</a>
          <a href="#" :class="{'active': query.status == 0}" @click="handleStatusChange(0)">{{ $t('掲載停止') }}</a>
        </p>
      </div>
      <div class="staff-sub-header">
          <select class="menu-sort my-1" @change="handleCategoryChange">
            <option value="-1">{{ $t('部位でソート') }}</option>
            <option v-for="(name, id) in specialities" :key="id" :value="id">{{ name }}</option>
          </select>
          <button class="btn btn-primary my-1" @click="handleNewMenu"><img src="/img/plus.svg" class="img_plus"> {{ $t('新規メニューを追加') }}</button>
      </div>
      <div class="staff-content">
        <div class="menu-list col-12 px-0">
          <div v-for="(item, index) in menus" :key="index" class="menu-one col-md-6 col-12">
            <div class="menu-one-in" @click="handleShowMenu(item.id)">
              <div class="menu-img">
                <img v-if="item.images.length" :src="'/storage/'+item.images[0].path || '/img/menu-img.png'">
                <img v-else :src="'/img/menu-img.png'">
              </div>
              <div class="menu-info">
                <!-- <template v-if="index <= 5"></template> -->
                <p v-if="item.category" class="menu-cat">{{ item.category.name }}</p>
                <p class="menu-ttl">{{ item.name }}</p>
                <p class="menu-price">{{ item.price | currency }}({{ $t('税込') }})</p>
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
      id="menu-modal"
      :title="modalInfo.title"
      @cancel="handleModalClose"
      >
      <div v-if="form" class="create-menu-content">
        <div class="form-group">
          <small>{{ $t('掲載ステータス') }}</small>
          <div class="row mx-0">
            <!-- <toggle-button
              v-model="form.menus.status"
              :sync="true"
              :labels="{checked: '掲載', unchecked: '停止'}"
              :color="{checked: '#5CA3F6'}"
              :width="90"
              :height="30"
              :font-size="12" /> -->
              <Toggle :defaultState="form.menus.status == 0 ? true:false" @change="triggerEvent"/>
          </div>
          
        </div>
        <div class="form-group row">
          <div class="col-md-8">
            <small>{{ $t('メニュー名') }}</small>
            <input type="text" v-model="form.menus.name" :class="{'is-invalid' : errors && errors['menus.name'] }" placeholder="例：二重切開">
            <div v-if="errors && errors['menus.name']" class="error invalid-feedback">{{ errors['menus.name'][0] }}</div>
          </div>
          <div class="col-md-4">
            <small>{{ $t('料金') }}</small>
            <input type="number" v-model="form.menus.price" :class="{'is-invalid' : errors && errors['menus.price'] }" placeholder="例：250000円">
            <div v-if="errors && errors['menus.price']" class="error invalid-feedback">{{ errors['menus.price'][0] }}</div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
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
            <!-- <select v-model="form.menus.category_id" :class="{'is-invalid' : errors && errors['menus.category_id'] }">
              <option></option>
              <optgroup v-for="(parent, id) in categories" :key="id" :label="parent.name">
                <option v-for="(cate, j) in parent.all_children" :key="j" :value="cate.id">{{ cate.name }}</option>
              </optgroup>
            </select>
            <div v-if="errors && errors['menus.category_id']" class="error invalid-feedback">{{ errors['menus.category_id'][0] }}</div> -->
          </div>
        </div>
        <div class="form-group row companu-content--edit">
          <div class="col-md-12">
            <small>{{ $t('メニュー画像') }}</small>
            <file-upload
              ref="multiFilesUploadComponent"
              uploadUrl="/api/clinic/menus/photoupload"
              :maxFiles="10"
              name="menu-images"
              @file-upload-success="handleMultiFileSaved"
              @file-removed="hanleMultiFileRemove"
              @file-added="handleMultiFileAdded"
              @queue-complete="handleMultiFilesQueueComplete"
            />
            <div v-if="form.menuPhotos.length" class="company-profile-img-list">
              <div v-for="(img, index) in form.menuPhotos" class="company-image--edit" :key="index">
                <span class="remove-btn" @click="handleRemoveFile(index)"></span>
                <div class="over-hidden">
                  <img :src="'/storage/'+img" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="create-menu-desc">
          <small>{{ $t('メニューの説明') }}</small>
          <textarea rows="5" v-model="form.menus.description" :class="{'is-invalid' : errors && errors['menus.description'] }" placeholder="例：この施術は目頭を切る施術になります。"></textarea>
          <div v-if="errors && errors['menus.description']" class="error invalid-feedback">{{ errors['menus.description'][0] }}</div>
        </div>
        <div class="create-menu-risk">
          <small>{{ $t('副作用・リスク') }}</small>
          <textarea rows="5" v-model="form.menus.risk" :class="{'is-invalid' : errors && errors['menus.risk'] }" placeholder="例：施術後一週間ほど腫れる場合があります。"></textarea>
          <div v-if="errors && errors['menus.risk']" class="error invalid-feedback">{{ errors['menus.risk'][0] }}</div>
        </div>
        <div class="create-menu-gurantee">
          <small>{{ $t('施術の保証') }}</small>
          <textarea rows="5" v-model="form.menus.guarantee" :class="{'is-invalid' : errors && errors['menus.guarantee'] }" placeholder="例：何日間の保証があります。"></textarea>
          <div v-if="errors && errors['menus.guarantee']" class="error invalid-feedback">{{ errors['menus.guarantee'][0] }}</div>
        </div>
        <div class="form-group row">
          <div class="col-6">
              <small>{{ $t('施術時間') }}</small>
              <select v-model="form.menus.treat_time">
                <option></option>
                <option v-for="(item, key) in treat_time" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
          <div class="col-6">
              <small>{{ $t('抜糸') }}</small>
              <select v-model="form.menus.basshi">
                <option></option>
                <option v-for="(item, key) in basshi" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-6">
              <small>{{ $t('施術後の通院') }}</small>
              <select v-model="form.menus.hospital_visit">
                <option></option>
                <option v-for="(item, key) in hospital_visit" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
          <div class="col-6">
              <small>{{ $t('腫れ') }}</small>
              <select v-model="form.menus.hare">
                <option></option>
                <option v-for="(item, key) in hare" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-6">
              <small>{{ $t('痛み') }}</small>
              <select v-model="form.menus.pain">
                <option></option>
                <option v-for="(item, key) in pain" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
          <div class="col-6">
              <small>{{ $t('内出血') }}</small>
              <select v-model="form.menus.bleeding">
                <option></option>
                <option v-for="(item, key) in bleeding" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-6">
              <small>{{ $t('入院の必要性') }}</small>
              <select v-model="form.menus.hospital_need">
                <option></option>
                <option v-for="(item, key) in hospital_need" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
          <div class="col-6">
              <small>{{ $t('麻酔') }}</small>
              <select v-model="form.menus.masui">
                <option></option>
                <option v-for="(item, key) in masui" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-6">
              <small>{{ $t('メイク/洗顔') }}</small>
              <select v-model="form.menus.makeup">
                <option></option>
                <option v-for="(item, key) in makeup" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
          <div class="col-6">
              <small>{{ $t('シャワー/洗髪/入浴') }}</small>
              <select v-model="form.menus.shower">
                <option></option>
                <option v-for="(item, key) in shower" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-6">
              <small>{{ $t('施術部のマッサージ') }}</small>
              <select v-model="form.menus.massage">
                <option></option>
                <option v-for="(item, key) in massage" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
          <div class="col-6">
              <small>{{ $t('激しいスポーツ') }}</small>
              <select v-model="form.menus.sport_impossible">
                <option></option>
                <option v-for="(item, key) in sport_impossible" :key="key" :value="key">{{ item }}</option>
              </select>
          </div>
        </div>       
      </div>
      <template v-slot:footer>
        <button type="button" class="btn btn-primary btn-modal-footer" @click="handleUpdateMenu">{{ modalInfo.confirmBtnTitle }}</button>
      </template>
    </form-modal>

    <form-modal
      ref="viewModal"
      id="menu-view-modal"
      :title="modalInfo.title"
      >
      <div v-if="form" class="create-menu-content view-modal-content">
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('掲載ステータス') }}</small>
            <p v-if="form.menus.status">{{ $t('掲載中') }}</p>
            <p v-else>{{ $t('停止') }}</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-8">
            <small>{{ $t('メニュー名') }}</small>
            <p>{{form.menus.name}}埋没法ダブル</p>
          </div>
          <div class="col-4">
            <small>{{ $t('料金') }}</small>
            <p>{{form.menus.price | currency}}</p>
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
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('メニュー画像') }}</small>
            <div class="companu-content--edit">
                <div v-if="form.menuPhotos.length" class="company-profile-img-list">
                  <div v-for="(img, index) in form.menuPhotos" class="company-image--edit" :key="index">
                    <div class="over-hidden">
                      <img :src="'/storage/'+img" />
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col">
            <small>{{ $t('メニューの説明') }}</small>
            <p>{{form.menus.description}}</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('副作用・リスク') }}</small>
            <p>{{form.menus.risk}}</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('施術の保証') }}</small>
            <p>{{form.menus.guarantee}}</p>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('施術時間') }}</small>
            <template v-for="(item, key) in treat_time" :value="key">
              <p v-if="form.menus.treat_time == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
          <div class="col">
            <small>{{ $t('抜糸') }}</small>
            <template v-for="(item, key) in basshi" :value="key">
              <p v-if="form.menus.basshi == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('施術後の通院') }}</small>
            <template v-for="(item, key) in hospital_visit" :value="key">
              <p v-if="form.menus.hospital_visit == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
          <div class="col">
            <small>{{ $t('腫れ') }}</small>
            <template v-for="(item, key) in hare" :value="key">
              <p v-if="form.menus.hare == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('痛み') }}</small>
            <template v-for="(item, key) in pain" :value="key">
              <p v-if="form.menus.pain == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
          <div class="col">
            <small>{{ $t('内出血') }}</small>
            <template v-for="(item, key) in bleeding" :value="key">
              <p v-if="form.menus.bleeding == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('入院の必要性') }}</small>
            <template v-for="(item, key) in hospital_need" :value="key">
              <p v-if="form.menus.hospital_need == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
          <div class="col">
            <small>{{ $t('麻酔') }}</small>
            <template v-for="(item, key) in masui" :value="key">
              <p v-if="form.menus.masui == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('メイク/洗顔') }}</small>
            <template v-for="(item, key) in makeup" :value="key">
              <p v-if="form.menus.makeup == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
          <div class="col">
            <small>{{ $t('シャワー/洗髪/入浴') }}</small>
            <template v-for="(item, key) in shower" :value="key">
              <p v-if="form.menus.shower == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <small>{{ $t('施術部のマッサージ') }}</small>
            <template v-for="(item, key) in massage" :value="key">
              <p v-if="form.menus.massage == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
          <div class="col">
            <small>{{ $t('激しいスポーツ') }}</small>
            <template v-for="(item, key) in sport_impossible" :value="key">
              <p v-if="form.menus.sport_impossible == key" :key="key">
                {{item}}
              </p>
            </template>
          </div>
        </div>
        <div class="view-modal-footer">
          <button type="button" class="btn btn-danger btn-modal-footer" @click="handleDeleteMenu">{{ modalInfo.delBtnTitle }}</button>
          <button type="button" class="btn btn-primary btn-modal-footer ml-4"  @click="handleShowEditMenu">{{ modalInfo.confirmBtnTitle }}</button>
        </div>

      </div>
    </form-modal>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  data() {
    return {
      menus: [],
      form: undefined,
      errors: undefined,
      tmp: {
        menus: {
          name: '',
          speciality_id: '',
          price: '',
          description: '',
          risk: '',
          guarantee: '',
          treat_time: '',
          masui: '',
          hospital_visit: '',
          hare: '',
          pain: '',
          bleeding: '',
          hospital_need: '',
          basshi: '',
          makeup: '',
          shower: '',
          massage: '',
          sport_impossible: '',
          status: true,
          photo: '',
        },
        fileChanged: false,
        menuPhotos: [],
      },
      query: {
        per_page: 10,
        page: 1,
        status: 1,
        speciality_id: ''
      },
      modalInfo: {
        title: '',
        confirmBtnTitle: '',
      },
      pageInfo: undefined,
      selected_categories: [],
    }
  },

  computed: {
    ...mapGetters({
      token: 'auth/token',
      is_master_loaded: 'state/is_master_loaded',
      specialities: 'data/specialities',
      categories: 'data/categories',
      masui: 'constant/masui',
      bleeding: 'constant/bleeding',
      hospital_need: 'constant/hospital_need',
      hospital_visit: 'constant/hospital_visit',
      makeup: 'constant/makeup',
      massage: 'constant/massage',
      pain: 'constant/pain',
      shower: 'constant/shower',
      sport_impossible: 'constant/sport_impossible',
      basshi: 'constant/basshi',
      hare: 'constant/hare',
      treat_time: 'constant/treat_time',
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
    menu_data(){
      return this.form.menuPhotos;
    }
  },

  mounted() {
    this.getData()
  },

  methods: {
   
    getData() {
      this.$store.dispatch('state/setIsLoading')
      const qs = this.$utils.getQueryString(this.query)
      axios.get(`/api/clinic/menus?${qs}`)
        .then(res => {
          this.menus = res.data.menus.data;
          this.query = {
            ...this.query,
            per_page: res.data.menus.per_page
          }
          this.pageInfo = {
            last_page: res.data.menus.last_page,
          }
          this.$store.dispatch('state/removeIsLoading')
        })
        .catch(error => {
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleShowMenu(id) {
      let selected = this.menus.find(el => el.id == id)
      this.form = {
        fileChanged: false,
        menus: {
          id: selected.id,
          name: selected.name,
          category_id: selected.category_id,
          price: selected.price,
          description: selected.description,
          risk: selected.risk,
          guarantee: selected.guarantee,
          treat_time: selected.treat_time,
          masui: selected.masui,
          hospital_visit: selected.hospital_visit,
          hare: selected.hare,
          pain: selected.pain,
          bleeding: selected.bleeding,
          hospital_need: selected.hospital_need,
          basshi: selected.basshi,
          makeup: selected.makeup,
          shower: selected.shower,
          massage: selected.massage,
          sport_impossible: selected.sport_impossible,
          status: selected.status == 1,
          photo: selected.photo,
        },
        menuPhotos: 
          selected.images.map(el => el.path)
        ,
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
      // this.modalInfo = {
      //   title: 'メニューを編集',
      //   confirmBtnTitle: 'メニューの編集を完了'
      // }
      // this.$refs.modal.show();
      this.errors = undefined
      this.modalInfo = {
        title: 'メニューの詳細',
        confirmBtnTitle: 'メニューを編集',
        delBtnTitle: 'メニューを削除'
      }
      this.$refs.viewModal.show();
    },
    handleShowEditMenu(){
      this.modalInfo = {
        title: 'メニューを編集',
        confirmBtnTitle: 'メニューの編集を完了'
      }
      this.$refs.viewModal.hide();
      this.$refs.modal.show();
    },

    handleNewMenu() {
      this.form = {
        ...this.tmp,
        menus: { ...this.tmp.menus }
      }
      this.modalInfo = {
        title: '新規メニューを追加',
        confirmBtnTitle: '新規メニューを追加'
      }
      this.form.menuPhotos = [];
      this.selected_categories = [];
      this.$refs.modal.show();
    },   

    handleUpdateMenu() { //pstar
      let flg = false;
      if (this.form.fileChanged) {
        flg = true
        this.$refs.multiFilesUploadComponent.processQueue();
      } else {
        this.handleSaveMenu();
        // this.modalInfo = {
        //   title: 'メニューの詳細',
        //   confirmBtnTitle: 'メニューを編集',
        //   delBtnTitle: 'メニューを削除'
        // }
        // this.$refs.modal.hide();
        // this.$refs.viewModal.show();
      }
    },

    handlePaginate(pageNum) {
      this.query = {
        ...this.query,
        page: pageNum,
      }
      this.getData()
    },

    handleStatusChange(status) {
      this.query = {
        ...this.query,
        page: 1,
        status: status
      }
      this.getData()
    },

    handleSaveMenu() {
      this.$store.dispatch('state/setIsLoading')   
      let url = '/api/clinic/menus';
      if (this.form.menus.id) {
        url += `/${this.form.menus.id}`
      }
      this.form = {
        ...this.form,
        categories: this.selected_categories.map(el => el.id)
      }
      axios.post(url, this.form)
        .then(res => {
          let menu = res.data.menu
          this.$store.dispatch('state/removeIsLoading')
          // if (menu.status == 1) {
            this.$store.dispatch('data/addMenu', { menu: menu })
          // }
          this.errors = undefined
          this.$refs.modal.hide()
          this.$swal({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            title: '登録しました。',
            icon: 'success',
          })
          this.getData()
        })
        .catch(error => {
          this.$swal({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            title: '登録できません。',
            icon: 'error',
          })
          this.errors = { ...error.response.data.errors }          
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleDeleteMenu(){
      this.$refs.viewModal.hide();
      let url = '/api/clinic/menus';
      if (this.form.menus.id) {
        url += `/${this.form.menus.id}`
      }
      axios.delete(url)
        .then(res => {
          this.getData()
          this.$swal({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            title: '削除。',
            icon: 'success',
          })
        })
        .catch(error => {
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleModalClose() {
      this.$refs.multiFilesUploadComponent.removeAllFiles()
    },

    // handleFileSaved(fileUrl) {
    //   this.selected_photos.push(fileUrl); 
    //   this.form.menus.photo = fileUrl
    //   this.form.fileChanged = false
    //   this.handleSaveMenu()
    // },

    // hanleFileRemove() {
    //   this.form.fileChanged = false;
    //   this.form.menus.photo = '';
    // },

    // handleFileAdded(flg) {
    //   this.form.fileChanged = flg;
    // },

    handleMultiFileSaved(fileUrl) {
      this.form.menuPhotos.push(fileUrl)
    },

    hanleMultiFileRemove(id) {
      console.log("remove", id);
      let length = this.$refs.multiFilesUploadComponent.getQueuedFiles();
      if (!length) {
        this.form.fileChanged = false;
      }
    },

    handleMultiFileAdded(flg) {
      this.form.fileChanged = flg;
    },

    handleMultiFilesQueueComplete() {
      this.form.fileChanged = false
      // if (!this.form.avatarFileChanged) {
        this.handleSaveMenu()
      // }
    },

    handleRemoveFile(index) {      
      this.form.menuPhotos.splice(index, 1)
    },

    handleCategoryChange(e) {
      e.preventDefault();
      this.query.category_id = e.target.value
      this.getData()      
    },
    triggerEvent(value){      
      if(value)
        this.form.menus.status = 0;
      else
        this.form.menus.status = 1;
    }
  }
}
</script>

<style scoped>
.img_plus{
  vertical-align: initial;
  margin-right: 5px;
}
.form-group input, .form-group select{
  min-height: 40px;
  padding: 3px 12px;
  width: 100%;
}
input::placeholder {
  color: rgba(19, 19, 64, 0.3);
  font-size: 14px;
  font-weight: 500;
}
div.create-menu-content{
  padding: 0 4rem;
}
.btn-modal-footer{
  padding: 8px 24px !important;
  font-weight: 500;
}
* >>> .vue-dropzone>.dz-preview .dz-image{
  width: 170px;
}

.view-modal-footer{
  margin-top: 4rem;
  display: flex;
  justify-content: center;
}
.company-profile-img-list{
  margin: 20px 0px;
}
.company-profile-img-list > div{
      padding: 10px 5px;
}
</style>
