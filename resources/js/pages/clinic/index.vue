<template>
  <div class="main-in">
    <div class="main-content">
      <div v-if="!isEditing && clinic" class="company-content edit-close">
        <div v-if="clinic.photo" class="company-img">
          <img :src="clinic.photo" />
        </div>
        <div class="company-name">
          <span>{{ $t('クリニック名') }}</span>
          <p>{{ clinic.name | emptyValue }}</p>
        </div>
        <div class="company-info">
          <div>
            <span>{{ $t('住所') }}</span>
            <p>{{ address | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('ネット予約') }}</span>
            <p>{{ clinic.net_reservation | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('最寄駅') }}</span>
            <p>{{ clinic.nearest_station | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('公式サイト') }}</span>
            <p>{{ clinic.site | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('アクセス') }}</span>
            <p>{{ clinic.access | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('電話番号') }}</span>
            <p>{{ clinic.phone_number | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('営業時間') }}</span>
            <p>{{ clinic.working_time | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('クレジットカード') }}</span>
            <p>{{ clinic.credit_card | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('休業日') }}</span>
            <p>{{ clinic.holidays | emptyValue }}</p>
          </div>
          <div>
            <span>{{ $t('駐車場') }}</span>
            <p>{{ clinic.parking | emptyValue }}</p>
          </div>
        </div>
        <div class="company-profile-imgs">
          <p class="payment-ttl">{{ $t('会社プロフィール画像') }}</p>
          <div class="company-profile-img-list">
            <div v-for="(attachment, index) in clinic.images" :key="index">
              <img :src="attachment.path" />
            </div>
          </div>
        </div>
        <div class="company-about">
          <p class="payment-ttl">{{ $t('会社紹介') }}</p>
          <div class="company-ttl">
            <span>{{ $t('タイトル') }}</span>
            <p>{{ clinic.company_title }}</p>
          </div>
          <div class="company-txt">
            <span>{{ $t('会社紹介文') }}</span>
            <p>{{ clinic.company_profile }}</p>
          </div>
        </div>
        <div class="btn-wrapper"><button class="btn btn-primary " @click="handleEditProfile">{{ $t('プロフィールを編集') }}</button></div>
      </div>
      <div v-if="isEditing && form" class="company-content companu-content--edit">
        <div class="company-img">
          <file-upload
            ref="fileUploadComponent"
            uploadUrl="/api/clinic/photoupload"
            :photo="form.clinic.photo"
            @file-upload-success="handleFileSaved"
            @file-removed="hanleFileRemove"
            @file-added="handleFileAdded"
          />
        </div>
        <div class="company-name">
          <span>{{ $t('クリニック名') }}</span>
          <input type="text" v-model="form.clinic.name" :class="{'is-invalid' : errors && errors['clinic.name'] }">
          <div v-if="errors && errors['clinic.name']" class="error invalid-feedback">{{ errors['clinic.name'][0] }}</div>
        </div>
        <div class="company-info">
          <div>
            <span>{{ $t('住所') }}</span>
            <div class="clinic-info--address-block">
              <select v-model="form.clinic.pref_id" @change="changePref">
                <option></option>
                <option v-for="(name, id) in prefs" :key="id" :value="id">{{ name }}</option>
              </select>
              <span>></span>
              <select v-model="form.clinic.addr01" @change="changeCity">
                <option></option>
                <option v-for="(city, id) in cities" :key="id" :value="city.name">{{ city.name }}</option>
              </select>
              <span>></span>
              <select v-model="form.clinic.addr02">
                <option></option>
                <option v-for="(town, id) in towns" :key="id" :value="town.name">{{ town.name }}</option>
              </select>
            </div>
            <div><input type="text" v-model="form.clinic.department" :class="{'is-invalid' : errors && errors['clinic.department'] }"></div>
            <div v-if="errors && errors['clinic.department']" class="error invalid-feedback">{{ errors['clinic.department'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('ネット予約') }}</span>
            <input type="text" v-model="form.clinic.net_reservation" :class="{'is-invalid' : errors && errors['clinic.net_reservation'] }">
            <div v-if="errors && errors['clinic.net_reservation']" class="error invalid-feedback">{{ errors['clinic.net_reservation'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('最寄駅') }}</span>
            <input type="text" v-model="form.clinic.nearest_station" :class="{'is-invalid' : errors && errors['clinic.nearest_station'] }">
            <div v-if="errors && errors['clinic.nearest_station']" class="error invalid-feedback">{{ errors['clinic.nearest_station'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('公式サイト') }}</span>
            <input type="text" v-model="form.clinic.site" :class="{'is-invalid' : errors && errors['clinic.name01'] }">
            <div v-if="errors && errors['clinic.name02']" class="error invalid-feedback">{{ errors['clinic.name02'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('アクセス') }}</span>
            <input type="text" v-model="form.clinic.access" :class="{'is-invalid' : errors && errors['clinic.access'] }">
            <div v-if="errors && errors['clinic.access']" class="error invalid-feedback">{{ errors['clinic.access'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('電話番号') }}</span>
            <input type="text" v-model="form.clinic.phone_number" :class="{'is-invalid' : errors && errors['clinic.phone_number'] }">
            <div v-if="errors && errors['clinic.phone_number']" class="error invalid-feedback">{{ errors['clinic.phone_number'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('営業時間') }}</span>
            <input type="text" v-model="form.clinic.working_time" :class="{'is-invalid' : errors && errors['clinic.working_time'] }">
            <div v-if="errors && errors['clinic.working_time']" class="error invalid-feedback">{{ errors['clinic.working_time'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('クレジットカード') }}</span>
            <input type="text" v-model="form.clinic.credit_card" :class="{'is-invalid' : errors && errors['clinic.credit_card'] }">
            <div v-if="errors && errors['clinic.credit_card']" class="error invalid-feedback">{{ errors['clinic.credit_card'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('休業日') }}</span>
            <input type="text" v-model="form.clinic.holidays" :class="{'is-invalid' : errors && errors['clinic.holidays'] }">
            <div v-if="errors && errors['clinic.holidays']" class="error invalid-feedback">{{ errors['clinic.holidays'][0] }}</div>
          </div>
          <div>
            <span>{{ $t('駐車場') }}</span>
            <input type="text" v-model="form.clinic.parking" :class="{'is-invalid' : errors && errors['clinic.name01'] }">
            <div v-if="errors && errors['clinic.name02']" class="error invalid-feedback">{{ errors['clinic.name02'][0] }}</div>
          </div>
        </div>
        <div class="company-profile-imgs">
          <p class="payment-ttl">会社プロフィール画像</p>
          <div v-if="form.companyPhotos.length" class="company-profile-img-list">
            <div v-for="(img, index) in form.companyPhotos" class="company-image--edit" :key="index">
              <span class="remove-btn" @click="handleRemoveFile(index)"></span>
              <div class="over-hidden">
                <img :src="img" />
              </div>
            </div>
          </div>
          <file-upload
            ref="multiFilesUploadComponent"
            uploadUrl="/api/clinic/company-photo-upload"
            :maxFiles="10"
            name="company-images"
            @file-upload-success="handleMultiFileSaved"
            @file-removed="hanleMultiFileRemove"
            @file-added="handleMultiFileAdded"
            @queue-complete="handleMultiFilesQueueComplete"
          />
        </div>
        <div class="company-about">
          <p class="payment-ttl">会社紹介</p>
          <div class="company-ttl">
            <span>タイトル</span>
            <input type="text" v-model="form.clinic.company_title" :class="{'is-invalid' : errors && errors['clinic.company_title'] }">
            <div v-if="errors && errors['clinic.company_title']" class="error invalid-feedback">{{ errors['clinic.company_title'][0] }}</div>
          </div>
          <div class="company-txt">
            <span>会社紹介文</span>
            <textarea v-model="form.clinic.company_profile" :class="{'is-invalid' : errors && errors['clinic.company_profile'] }"></textarea>
            <div v-if="errors && errors['clinic.company_profile']" class="error invalid-feedback">{{ errors['clinic.company_profile'][0] }}</div>
          </div>
        </div>
        <div class="btn-wrapper">
          <button class="btn btn-primary" @click="isEditing = false">キャンセル</button>
          <button class="btn btn-primary" @click="handleUpdate">プロフィールを保存</button>
        </div>
      </div>
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
      clinic: undefined,
      form: undefined,
      isEditing: false,
      errors: undefined,
      tmp: {
        avatarFileChanged: false,
        companyPhotoFileChanged: false,
        clinic: undefined,
        companyPhotos: []
      },
      cities: [],
      towns: [],
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      prefs: 'data/prefs',
    }),
    address: function() {
      const pref = this.clinic.pref ? this.clinic.pref.name : ''
      return `${pref}${this.clinic.addr01}${this.clinic.addr02}${this.clinic.department}`;
    },
  },

  mounted() {
    this.getData()
  },

  filters: {
    emptyValue(value) {
      if (value) return value;

      return '未入力';
    }
  },

  methods: {
    getData() {
      this.$store.dispatch('state/setIsLoading')
      axios.get(`/api/clinic/getInfo`)
        .then(res => {
          this.$store.dispatch('state/removeIsLoading')
          this.clinic = res.data.clinic
        })
        .catch(error => {
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleEditProfile() {
      this.form = {
        ...this.tmp,
        clinic: { ...this.clinic },
        companyPhotos: [
          ...this.clinic.images.map(el => el.path)
        ],
      }
      this.isEditing = true;
    },

    handleUpdate() {
      this.$store.dispatch('state/setIsLoading')
      let uploadFlg = false
      if (this.form.avatarFileChanged) {
        uploadFlg = true
        this.$refs.fileUploadComponent.processQueue();
      }
      if (this.form.companyPhotoFileChanged) {
        uploadFlg = true
        this.$refs.multiFilesUploadComponent.processQueue();
      }

      if (!uploadFlg) {
        this.handleSaveClinic()
      }
    },

    handleSaveClinic() {
      this.$store.dispatch('state/setIsLoading')
      axios.put(`/api/clinic/${this.user.id}`, this.form)
        .then(res => {
          this.$swal({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            title: '変更しました。',
            icon: 'success',
          })
          this.clinic = res.data.clinic
          this.isEditing = false
          this.$store.dispatch('state/removeIsLoading')
        })
        .catch(error => {
          this.errors = { ...error.response.data.errors }
          this.$store.dispatch('state/removeIsLoading')
        })
    },

    handleFileSaved(fileUrl) {
      this.form.clinic.photo = fileUrl
      this.form.avatarFileChanged = false

      if (!this.form.companyPhotoFileChanged) {
        this.handleSaveClinic()
      }
    },

    hanleFileRemove() {
      this.form.avatarFileChanged = false;
      this.form.clinic.photo = '';
    },

    handleFileAdded(flg) {
      this.form.avatarFileChanged = flg;
    },

    handleMultiFileSaved(fileUrl) {
      this.form.companyPhotos.push(fileUrl)
    },

    hanleMultiFileRemove() {
      let length = this.$refs.multiFilesUploadComponent.getQueuedFiles();
      console.log(length)
      if (!length) {
        this.form.companyPhotoFileChanged = false;
      }
    },

    handleMultiFileAdded(flg) {
      console.log('success')
      this.form.companyPhotoFileChanged = flg;
    },

    handleMultiFilesQueueComplete() {
      console.log('Multi Files Uplaod Complete!');
      this.form.companyPhotoFileChanged = false
      if (!this.form.avatarFileChanged) {
        this.handleSaveClinic()
      }
    },

    handleRemoveFile(index) {
      this.form.companyPhotos.splice(index, 1)
      // this.form.companyPhotoThumbs.splice(index, 1)
    },

    changePref(e) {
      this.cities = [];
      this.towns = [];
      axios.get(`/api/clinic/pref/${this.form.clinic.pref_id}/cities`)
        .then((res) => {
          this.cities = res.data
        });
    },

    changeCity(e) {
      this.towns = [];
      const city_id = this.cities.find(el => el.name == e.target.value).id
      axios.get(`/api/clinic/city/${city_id}/towns`)
        .then((res) => {
          this.towns = res.data
        });
    },
  }
}
</script>

<style scoped>
</style>
