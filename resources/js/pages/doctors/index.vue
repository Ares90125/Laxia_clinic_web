<template>
  <div v-if="is_master_loaded" class="main-in">
    <div class="row new-btn-header">
      <button class="btn3 add-stuff-btn mr-3" @click="handleInviteDoctor" ><img src="/img/plus.svg"> {{ $t('ドクターを招待') }}</button>
      <button class="btn3 add-stuff-btn" @click="handleNewStuffForm"><img src="/img/plus.svg"> {{ $t('新規スタッフを追加') }}</button>
    </div>
    <div class="main-content main-dev-content">
      <div class="staff-header staff-doctor-header mb-2">
        <p>
          <!-- <select class="staff-sort">
            <option>{{ $t('職種でソート') }}</option>
            <option v-for="(name, id) in jobs" :key="id" :value="id">{{ name }}</option>
          </select> -->
          <a href="#" :class="{'active': query.status == 0}" @click="handleStatusChange(0)">{{ $t('すべて') }}</a>
          <a href="#" :class="{'active': query.status == 1}" @click="handleStatusChange(1)" >{{ $t('ドクター') }} ({{doctor_cnt}})</a> 
          <a href="#" :class="{'active': query.status == 2}" @click="handleStatusChange(2)" >{{ $t('スタッフ') }} ({{staff_cnt}})</a> 
        </p>
      </div>
      <div class="search-content" >
        <div class="search-panel">
          <span><i class="bi bi-search"></i></span>
          <input type="text" placeholder="メンバーの名前を検索" @keyup='add_todo_keyup' />
        </div>
      </div>
      <div v-if="doctors.length && (query.status == 0 || query.status == 1)" class="staff-list" style="margin-top: 4rem !important;">
        <div v-for="(item, index) in doctors" :key="index" class="staff-one" @click="handleShowDoctor(item.doctor_id)">
          <div v-if="item.photo" class="photo-item">
            <p class="staff-img">
              <img :src="'/storage/' + item.photo" />
              </p>
          </div>
          <div v-else class="photo-svg-item">
              <svg  width="145" height="145" viewBox="0 0 145 145" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="72.5" cy="72.5" r="72.5" fill="#F5F5F5"/>
              <path opacity="0.3" d="M99.1381 78.2378C97.3863 74.8907 95.3706 71.8751 93.3869 69.7364C95.8451 66.04 97.2864 61.6093 97.2864 56.8293C97.2829 43.9258 86.8115 33.464 73.8963 33.4604C60.981 33.464 50.5096 43.9258 50.5096 56.8293C50.5096 61.6022 51.951 66.04 54.4092 69.7364C52.422 71.8751 50.4097 74.8943 48.658 78.2378C46.8991 81.6276 45.4399 85.2598 44.8869 88.6782C44.7049 89.8153 44.6193 90.9238 44.6193 91.9968C44.605 95.5684 45.6361 98.7943 47.4449 101.396C50.1564 105.324 54.345 107.805 58.9688 109.352C63.6104 110.892 68.7908 111.534 73.8963 111.537C80.7071 111.52 87.6428 110.422 93.262 107.474C96.0663 105.995 98.553 104.013 100.351 101.396C102.153 98.7943 103.184 95.572 103.177 91.9968C103.177 90.9203 103.091 89.8153 102.906 88.6782C102.356 85.2598 100.897 81.6312 99.1381 78.2378ZM61.905 44.849C64.984 41.7729 69.2011 39.8837 73.8963 39.8837C78.5879 39.8837 82.8121 41.7729 85.8875 44.849C88.9593 47.9252 90.8573 52.142 90.8573 56.8293C90.8538 61.5167 88.9629 65.7299 85.8875 68.8097C82.8085 71.8787 78.5879 73.7714 73.8927 73.7714C69.2011 73.7679 64.9804 71.8787 61.9015 68.8097C58.8261 65.7335 56.9316 61.5202 56.9316 56.8293C56.9352 52.142 58.8296 47.9252 61.905 44.849ZM95.0495 97.7713C93.4511 100.113 90.5719 102.006 86.7865 103.264C83.019 104.522 78.4558 105.121 73.8927 105.114C67.8168 105.132 61.7267 104.034 57.531 101.796C55.4296 100.691 53.817 99.3325 52.7431 97.7641C51.6727 96.1886 51.0555 94.3921 51.0448 92.0003C51.0448 91.2874 51.1055 90.5104 51.2339 89.6798C51.5871 87.3843 52.8108 84.1549 54.3557 81.2106C55.7364 78.5514 57.3883 76.1062 58.7155 74.5842C62.8005 78.0774 68.0987 80.1982 73.8963 80.1982C79.6974 80.1982 84.992 78.0774 89.0806 74.5877C90.4042 76.1098 92.0597 78.5514 93.4404 81.2141C94.9852 84.1584 96.209 87.3879 96.5586 89.6834C96.6906 90.5139 96.7513 91.2839 96.7513 92.0039C96.737 94.3921 96.1234 96.1886 95.0495 97.7713Z" fill="#666E6E"/>
              </svg>
          </div>
          <div class="job-item">
            <p class="staff-hira">{{ item.hira_name }} {{ item.kata_name }}</p>
            <p class="staff-name">{{ item.job && item.job_name }}{{ $t('ドクター') }}</p>
          </div>
        </div>
      </div>

      <div v-if="stuff_datas.length && (query.status == 0 || query.status == 2)" class="staff-list" :style="query.status == 2 ? 'margin-top: 4rem !important;' : 'margin-top: 25px !important;'">
        <div v-for="(item, index) in stuff_datas" :key="index" class="staff-one" @click="handleShowStuff(item.id)">
          <div v-if="item.photo" class="photo-item">
            <p class="staff-img">
              <img :src="'/storage/' + item.photo" />
              </p>
          </div>
          <div v-else class="photo-svg-item">
              <svg  width="145" height="145" viewBox="0 0 145 145" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="72.5" cy="72.5" r="72.5" fill="#F5F5F5"/>
              <path opacity="0.3" d="M99.1381 78.2378C97.3863 74.8907 95.3706 71.8751 93.3869 69.7364C95.8451 66.04 97.2864 61.6093 97.2864 56.8293C97.2829 43.9258 86.8115 33.464 73.8963 33.4604C60.981 33.464 50.5096 43.9258 50.5096 56.8293C50.5096 61.6022 51.951 66.04 54.4092 69.7364C52.422 71.8751 50.4097 74.8943 48.658 78.2378C46.8991 81.6276 45.4399 85.2598 44.8869 88.6782C44.7049 89.8153 44.6193 90.9238 44.6193 91.9968C44.605 95.5684 45.6361 98.7943 47.4449 101.396C50.1564 105.324 54.345 107.805 58.9688 109.352C63.6104 110.892 68.7908 111.534 73.8963 111.537C80.7071 111.52 87.6428 110.422 93.262 107.474C96.0663 105.995 98.553 104.013 100.351 101.396C102.153 98.7943 103.184 95.572 103.177 91.9968C103.177 90.9203 103.091 89.8153 102.906 88.6782C102.356 85.2598 100.897 81.6312 99.1381 78.2378ZM61.905 44.849C64.984 41.7729 69.2011 39.8837 73.8963 39.8837C78.5879 39.8837 82.8121 41.7729 85.8875 44.849C88.9593 47.9252 90.8573 52.142 90.8573 56.8293C90.8538 61.5167 88.9629 65.7299 85.8875 68.8097C82.8085 71.8787 78.5879 73.7714 73.8927 73.7714C69.2011 73.7679 64.9804 71.8787 61.9015 68.8097C58.8261 65.7335 56.9316 61.5202 56.9316 56.8293C56.9352 52.142 58.8296 47.9252 61.905 44.849ZM95.0495 97.7713C93.4511 100.113 90.5719 102.006 86.7865 103.264C83.019 104.522 78.4558 105.121 73.8927 105.114C67.8168 105.132 61.7267 104.034 57.531 101.796C55.4296 100.691 53.817 99.3325 52.7431 97.7641C51.6727 96.1886 51.0555 94.3921 51.0448 92.0003C51.0448 91.2874 51.1055 90.5104 51.2339 89.6798C51.5871 87.3843 52.8108 84.1549 54.3557 81.2106C55.7364 78.5514 57.3883 76.1062 58.7155 74.5842C62.8005 78.0774 68.0987 80.1982 73.8963 80.1982C79.6974 80.1982 84.992 78.0774 89.0806 74.5877C90.4042 76.1098 92.0597 78.5514 93.4404 81.2141C94.9852 84.1584 96.209 87.3879 96.5586 89.6834C96.6906 90.5139 96.7513 91.2839 96.7513 92.0039C96.737 94.3921 96.1234 96.1886 95.0495 97.7713Z" fill="#666E6E"/>
              </svg>
          </div>
          <div class="job-item">
            <p class="staff-hira">{{ item.name }} {{ item.kana }}</p>
            <p class="staff-name">{{ item.job && item.job_name }}</p>
          </div>
        </div>
      </div>
      <!-- <pagination
        v-if="pageInfo"
        :page="query.page"
        :page-count="pageInfo.last_page"
        :click-handler="handlePaginate" /> -->
    </div>
    <form-modal
      ref="modal"
      id="stuff-modal"
      :title="modalInfo.title"
      @cancel="handleModalClose"
      >
      <div v-if="isEditing && form">
        <span class="label-title px-5">写真</span>
        <div class="row d-flex justify-content-center">          
          <div class="col-md-3 col-5">
            <div>
              <file-upload
                v-if="form.stuffs.photo"
                ref="fileUploadComponent"
                uploadUrl="/api/clinic/stuffs/photoupload"
                :photo="'/storage/'+form.stuffs.photo"
                :avatar="true"
                @file-upload-success="handleFileSaved"
                @file-removed="hanleFileRemove"
                @file-added="handleFileAdded"
              />
              <file-upload
                v-else
                ref="fileUploadComponent"
                uploadUrl="/api/clinic/stuffs/photoupload"
                :avatar="true"
                @file-upload-success="handleFileSaved"
                @file-removed="hanleFileRemove"
                @file-added="handleFileAdded"
              />
            </div>
          </div>
        </div>
        <div class="row my-5 px-5">
          <div class="col-6">
            <span class="label-title">名前(漢字)</span>
            <input type="text" class="form-control" v-model="form.stuffs.name" placeholder="例：田中太郎"/>
          </div>
          <div class="col-6">
            <span class="label-title">名前(カタカナ)</span>
            <input type="text" class="form-control" v-model="form.stuffs.kana" placeholder="例：タナカタロウ"/>
          </div>
        </div>

        <div class="row mt-5">
          <div class="col-12 d-flex justify-content-center">
            <button type="button" class="btn btn-primary" @click="handleUpdateStuff">{{ modalInfo.confirmBtnTitle }}</button>
          </div>
        </div>
      </div>
      <!-- <div v-if="isCreateProfile">
        <div class="row d-flex justify-content-center my-5">
          <div class="col-3">
            <div>
              <img :src="form.photo" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 d-flex justify-content-center">
            <p>タナカアオイ</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12 d-flex justify-content-center">
            <p>田中葵</p>
          </div>
        </div>
        <div class="row my-5 px-5">
          <div class="col-6">
            <button type="button" class="btn btn-danger" @click="handleUpdateStuff">スタッフ情報を削除</button>
          </div>
          <div class="col-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" @click="handleUpdateStuff">プロフィールを修正</button>
          </div>
        </div>
      </div> -->
    </form-modal>
    <form-modal
      ref="stuffViewModal"
      id="stuff-view-modal"
      :title="modalInfo.title"
      >
      <div v-if="isStuffDetail && form">
        <div class="row d-flex justify-content-center">
            <img class="md-doctor-avatar-img" v-if="form.stuffs.photo" :src="'/storage/' + form.stuffs.photo" />
            <img class="md-doctor-avatar-img" v-else :src="'/img/menu-img.png'">
        </div>
        <div class="row d-flex flex-column align-items-center mt-5">
          <p class="detail-title">{{form.stuffs.kana}}</p>
          <p class="detail-content">{{form.stuffs.name}}</p>
        </div>

        <div class="row mt-5">
          <div class="col-12 d-flex justify-content-around">
            <button type="button" class="btn btn-danger" @click="handleDeleteConfirmDoctor(0)">{{ modalInfo.delBtnTitle }}</button>
            <button type="button" class="btn btn-primary" @click="handleShowEditStuff">{{ modalInfo.confirmBtnTitle }}</button>
          </div>
        </div>
      </div>
    </form-modal>

    <form-modal
      ref="inviteDoctor"
      id="invite-doctor-modal"
      :title="modalInfo.title"
      @cancel="handleModalClose"
    >
      <div v-if="isInviteDoctor" class="main-modal">
        <div class="">
            <div class="row  my-3">
              <div class="col-12">
                <span class="font-500">{{ $t('招待したいドクターのIDを入力してください') }}</span>
              </div>
            </div>
            <div class="row">
              <div class="col-9">
                <input class="form-control gray-ph-input" type="text" v-model="doctorId" placeholder="IDを入力してください"/>
              </div>
              <div class="col-3">
                <button type="button" class="bootstrap-btn btn btn-secondary btn-secondary-2" @click="handleFindDoctor">検索</button>
              </div>
            </div>
            <div v-if="failSearchResult" class="row my-1">
              <div class="col-12">
                <p class="find-doctor-warning-msg">このIDは存在しないかID名が間違っています<br>
                  正しいID名を入力してください</p>
              </div>
            </div>

            <div v-if="isEnableDoctor" class="row mt-5">
              <div class="col-9">
                <div class="d-flex align-items-center">
                  <img class="sm-doctor-avatar-img" :src="doctor.photo || '/img/menu-img.png'" />
                  <p class="ml-2">{{doctor.hira_name}}{{ doctor.kata_name }}</p>
                </div>
              </div>
              <div class="col-3 d-flex align-items-center">
                <button type="button" class="bootstrap-btn btn btn-primary btn-primary-2" @click="handleAddDoctor(doctor.doctor_id)">招待</button>
              </div>
            </div>
        </div>
      </div>
    </form-modal>

    <form-modal
      ref="doctorDetailModal"
      id="doctor-detail-modal"
      :title="modalInfo.title"
      @cancel="handleModalClose"
    >
      <div v-if="isDoctorDetail" class="main-modal">
        <div class="row mb-5">
          <div class="col-md-3 col-6">
            <img class="md-doctor-avatar-img" :src="doctorItem.photo || '/img/menu-img.png'" />
          </div>
          <div class="col-md-8 col-6 d-flex align-items-center">
            <div>
              <p class="detail-title">{{doctorItem.hira_name}}</p>
              <p class="detail-content">{{doctorItem.kata_name}}</p>
            </div>
          </div>
        </div>
        <div class="row my-4">
          <div class="col-5">
            <p class="detail-title">{{ $t('ID名') }}</p>
            <p class="detail-content">{{doctorItem.name}}</p>
          </div>
          <div class="col-3">
            <p class="detail-title">{{ $t('役職') }}</p>
            <p class="detail-content">{{doctorItem.job_name}}</p>
          </div>
          <div class="col-3">
            <p class="detail-title">{{ $t('役職歴') }}</p>
            <p class="detail-content">{{doctorItem.experience_year}}</p>
          </div>
        </div>
        <div class="row my-4">
          <div class="col-12">
            <p class="detail-title">{{ $t('得意分野') }}</p>
            <p class="detail-content"><span>{{doctorItem.spec0}}</span> <span>{{doctorItem.spec1}}</span> <span>{{doctorItem.spec2}}</span></p>
          </div>
        </div>
        <div class="row my-4">
          <div class="col-12">
            <p class="detail-title">{{ $t('経歴') }}</p>
            <p class="detail-content">{{doctorItem.profile}}</p>
          </div>
        </div>
        <div class="row my-4">
          <div class="col-12">
            <p class="detail-title">{{ $t('資格・実績') }}</p>
            <p class="detail-content">{{doctorItem.career}}</p>
          </div>
        </div>
        <div class="row my-4">
          <div class="col-12 d-flex justify-content-center">
            <button type="button" class="btn btn-danger" @click="handleDeleteConfirmDoctor(1)">{{ $t('ドクター情報を削除') }}</button>
          </div>
        </div>
      </div>
    </form-modal>

    <form-modal
      ref="delConfirmModal"
      id="del-confirm-modal"
      :title="delModalInfo.title"
      >
        <div class="main-modal">
          <div class="auth--wrapper">
            <div class="auth-form">
              <h2 class="auth-title mb-4 mt-2" >
                <template v-if="cancel_status == 0">
                {{ $t('スタッフ情報を削除しますか？') }}
                </template>
                <template v-else>
                {{ $t('ドクター情報を削除しますか？') }}
                </template>
              </h2>
              <span class="auth-title-tip">
                <template v-if="cancel_status == 0">
                  スタッフ情報を削除してよろしいでしょうか？<br>
                  削除すると復元することはできません。
                </template>
                <template v-else>
                  ドクター情報を削除してよろしいでしょうか？<br>
                  削除すると復元することはできません。
                </template>
              </span>
            </div>
            <div class="row mt-4">
              <div class="col-12 d-flex justify-content-around">
                <button type="button" class="btn btn-cancel" @click="cancelModal()">{{ delModalInfo.confirmBtnTitle}}</button>
                <!-- <button type="button" class="btn btn-danger" @click="handleDeleteDoctor(doctorItem.doctor_id)">{{ modalInfo.delBtnTitle }}</button> -->
                <button type="button" class="btn btn-danger" @click="handleDelete">{{ delModalInfo.delBtnTitle }}</button>
              </div>
            </div>
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
      doctors: [],
      stuff_datas: [],
      isInviteDoctor: false,
      findDoctor: false,
      successFindDoctor: false,
      form: undefined,
      doctorItem: undefined,
      stuffItem: undefined,
      errors: undefined,
      doctorId:'',
      doctor: undefined,
      isEnableDoctor: false,
      failSearchResult: false,
      isCreateProfile: false,
      isEditing: false,
      completeDoctor: undefined,
      isDoctorDetail: false,
      isStuffDetail: false,
      tmp: {
        fileChanged: false,
        stuffs: {
          name: '',
          kana: '',
          duty: 'aaa',
          job_id: '2',
          experience_year: '0',
          career: 'aaa',
          profile: 'aaa',
          photo: '',
        },
        specialities: {
          spec0: '',
          spec1: '',
          spec2: '',
        }
      },
      query: {
        per_page: 12,
        page: 1,
        status: 0,
      },
      modalInfo: {
        title: '',
        confirmBtnTitle: '',
      },
      delModalInfo: {
        title: '',
        confirmBtnTitle: '',
      },
      pageInfo: undefined,
      cancel_status: 0,
      doctor_cnt: 0,
      staff_cnt: 0,
    }
  },

  computed: {
    ...mapGetters({
      is_master_loaded: 'state/is_master_loaded',
      jobs: 'data/jobs',
      stuffs: 'data/stuffs',
      specialities: 'data/specialities',
    }),
  },

  mounted() {
    this.getData()
  },

  methods: {
    add_todo_keyup(e){
      if (e.key == 'Enter') {
        this.query = {
          ...this.query,
          q: e.target.value,
        }
        this.getData();
      }
    },
    getData() {
      this.$store.dispatch('state/setIsLoading')
      const qs = this.$utils.getQueryString(this.query)
      // axios.get(`/api/clinic/doctors?${qs}`)
      //   .then(res => {
      //     this.doctors = res.data.data;
      //     this.doctor_cnt = res.data.cnt_mh;
      //     this.staff_cnt = res.data.cnt_mg;
      //     // this.pageInfo = {
      //     //   last_page: res.data.stuffs.last_page,
      //     // }
      //     this.$store.dispatch('state/removeIsLoading')
      //   })
      //   .catch(error => {
      //     this.$store.dispatch('state/removeIsLoading')
      //   })

      Promise.all([
        axios.get(`/api/clinic/doctors?${qs}`),
        axios.get(`/api/clinic/stuffs/get?${qs}`)
      ]).then(([res1, res2]) => {
          this.doctors = res1.data.data;
          this.doctor_cnt = res1.data.cnt_mh;
          this.stuff_datas = res2.data.stuffs;
          this.staff_cnt = res2.data.cnt_mg;
          this.$store.dispatch('state/removeIsLoading')
      }).catch(error => {
        this.$store.dispatch('state/removeIsLoading')
      })
    },

    handleInviteDoctor() {
      this.modalInfo = {
        title: 'ドクターを招待',
        confirmBtnTitle: '検索'
      }
      this.isInviteDoctor = true
      this.$refs.inviteDoctor.show();
    },

    handleFindDoctor(){
      let url = '/api/clinic/search/doctor'
      axios.post(url, { q: this.doctorId })
        .then(res => {
          this.doctor = {...res.data.data}
          if(this.doctor.name === undefined){
            this.failSearchResult = true;
            this.isEnableDoctor = false
          }else{
            this.isEnableDoctor = true;
            this.failSearchResult = false;
          }
        })
        .catch(error => {
          this.failSearchResult = true;
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleAddDoctor(doctorId){
      let url = '/api/clinic/doctors'
      axios.post(url, { doctor_id: doctorId })
        .then(res => {
          this.getData();
        })
        .catch(error => {
          this.$store.dispatch('state/removeIsLoading')
        })
      this.$refs.inviteDoctor.hide();
    },

    handleShowDoctor(id) {
      let selected = this.doctors.find(el => el.doctor_id == id);
      this.doctorItem = {
         ...selected
      }
      this.isDoctorDetail = true
      this.modalInfo = {
        title: 'ドクターの詳細',
        confirmBtnTitle: 'プロフィールを編集'
      }
      this.$refs.doctorDetailModal.show()
    },

    handleShowStuff(id) {
      let selected = this.stuff_datas.find(el => el.id == id);      
      this.form = {
        fileChanged: false,
        stuffs: {
          id: selected.id,
          name: selected.name,
          kana: selected.kana,
          duty: selected.duty,
          job_id: selected.job_id,
          experience_year: selected.experience_year,
          career: selected.career,
          profile: selected.profile,
          photo: selected.photo,
        }
      }
      this.isStuffDetail = true
      this.modalInfo = {
        title: 'スタッフの詳細',
        confirmBtnTitle: 'プロフィールを修正',
        delBtnTitle: 'スタッフ情報を削除'
      }
      this.$refs.stuffViewModal.show()
    },

    handleShowEditStuff(){
      this.modalInfo = {
        title: 'スタッフ情報を変更',
        confirmBtnTitle: '編集を保存'
      }
      this.isEditing = true
      this.$refs.stuffViewModal.hide();
      this.$refs.modal.show();
    },

    handleUpdateDoctor(){
      this.$refs.modal.hide()
      // this.$refs.stuffViewModal.show()
      // this.modalInfo = {
      //   title: 'スタッフの詳細',
      //   confirmBtnTitle: 'プロフィールを修正',
      //   delBtnTitle: 'スタッフ情報を削除'
      // }
      // this.handleShowDoctor()
    },

    handleDeleteConfirmDoctor(p_status){
      this.cancel_status = p_status;
      this.$refs.delConfirmModal.show()      
      this.delModalInfo = {
        confirmBtnTitle: 'キャンセル',
        delBtnTitle: '削除する'
      }
    },

    cancelModal(){
      this.$refs.delConfirmModal.hide()
      // this.$refs.stuffViewModal.hide()      
    },

    handleDelete(){
      if(this.cancel_status == 0)
        this.handleDeleteStuff(this.form.stuffs.id);
      else
        this.handleDeleteDoctor(this.doctorItem.doctor_id);

      this.$refs.delConfirmModal.hide()
    },

    handleDeleteStuff(stuffId){
      this.$refs.delConfirmModal.hide()
      let url = '/api/clinic/stuffs/' + stuffId
      axios.delete(url)
        .then(res => {
          this.$refs.stuffViewModal.hide()
          this.getData();
          this.form.stuffs.photo = '';
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

    handleDeleteDoctor(doctorId){
      this.$refs.delConfirmModal.hide()
      let url = '/api/clinic/doctors/' + doctorId
      axios.delete(url)
        .then(res => {
          this.doctors = this.doctors.filter(function (ele){
            return ele.doctor_id !== doctorId;
          })
          this.doctorDetailModal = false;
          this.$refs.doctorDetailModal.hide()

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

    handleStatusChange(status) {
      this.query = {
        ...this.query,
        page: 1,
        status: status
      }
      // this.getData()
    },

    handlePaginate(pageNum) {
      this.query = {
        ...this.query,
        page: pageNum,
      }
      this.getData()
    },

    handleNewStuffForm() {
      this.form = {
        ...this.tmp,
        stuffs: { ...this.tmp.stuffs },
        specialities: { ...this.tmp.specialities }
      }
      this.modalInfo = {
        title: '新規スタッフを追加',
        confirmBtnTitle: 'プロフィールを作成'
      }
      this.isEditing = true
      // this.isCreateProfile = true
      this.$refs.stuffViewModal.hide();
      this.$refs.modal.show();
    },

    handleUpdateStuff() {
      // if (!this.isEditing) {
      //   this.isEditing = true
      //   return
      // }
      if (this.form.fileChanged) {
        this.$refs.fileUploadComponent.processQueue();
      } else {
        this.handleSaveStuff();
      }
      // this.handleSaveStuff();
    },

    handleSaveStuff() {
      let url = '/api/clinic/stuffs';
      if (this.form.stuffs.id) {
        url += `/${this.form.stuffs.id}`
      }
      this.form = {
        ...this.form,
        specialities: { ...this.tmp.specialities }
      }
      axios.post(url, this.form)
        .then(res => {
          let stuff = res.data.stuff
          this.$store.dispatch('state/removeIsLoading')
          this.$store.dispatch('data/addStuff', {stuff: stuff})
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
          this.query = {
            ...this.query,
            page: 1
          }
          this.getData()
        })
        .catch(error => {
          this.errors = { ...error.response.data.errors }
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleModalClose() {
      this.$refs.fileUploadComponent.removeAllFiles()
    },

    handleFileSaved(fileUrl) {
      this.form.stuffs.photo = fileUrl
      this.form.fileChanged = false
      this.handleSaveStuff()
    },

    hanleFileRemove() {
      this.form.fileChanged = false;
      this.form.stuffs.photo = '';
    },

    handleFileAdded(flg) {
      this.form.fileChanged = flg;
    },
  }
}
</script>

<style scoped>
  .find-doctor-warning-msg{
    color: red;
  }
  .sm-doctor-avatar-img{
    border-radius: 50%;
    object-fit: cover;
    width: 50px;
    height: 50px;
  }
  .md-doctor-avatar-img{
    border-radius: 50%;
    object-fit: cover;
    width: 100px;
    height: 100px;
  }
  .new-btn-header{
    display: flex;
    float: right;
    padding-right: 45px;
  }
  * >>> #del-confirm-modal .form-modal-header{
    display: none !important;
    
  }
  * >>> #del-confirm-modal.form-modal-wrapper{
    top: 0px;
    left: 25vw;
    width: 50vw;
    align-items: center;
  }
  #del-confirm-modal .main-modal{
    padding: 0;
  }
  .auth--wrapper .auth-form{
    padding: 0;
  }
  .main-modal .auth--wrapper{
    height: auto;
  }
  .vue-dropzone:hover {
    background-color: #fff !important; 
  }  
</style>
