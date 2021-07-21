<template>
  <div class="dropdown searchable-dropdown">
    <input 
        v-if="Object.keys(selectedItem).length === 0" 
        ref="dropdowninput" 
        v-model.trim="inputValue" 
        class="dropdown-input"
        type="text"
        placeholder="名前・電話番号・予約IDで検索"
        @focus="history_flag = true"
        @blur="lossfocus"
        @keyup="searchData" />

    <span class="remove-btn" v-show="inputValue" @click="removeHandle">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.5 1.5L1.5 10.5" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1.5 1.5L10.5 10.5" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>

    <div v-show="inputValue && apiLoaded" class="dropdown-list">
      <div 
        @click="selectItem(item)"
        v-show="itemVisible(item)"
        v-for="item in itemList"
        :key="item.name"
        class="dropdown-item">
        {{ item.name }}
      </div>
      <div class="dropdown-item empty-item" v-if="itemList.length === 0">一致する結果は見つかりませんでした。再度検索してください。</div>
    </div>

    <div v-show="!inputValue && history_flag && historyList.length > 0" class="dropdown-list history-list">
        <div 
            @click="selectHistoryItem(item)"
            v-for="(item, index) in historyList"
            :key="index"
            class="dropdown-item history-item">
            {{ item }}
            <span class="history-remove" @click="removeHistoryItem(index, $event)">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.5 1.5L1.5 10.5" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1.5 1.5L10.5 10.5" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
    name: "HeaderSearchBox",
    data () {
        return {
            selectedItem: {},
            inputValue: '',
            itemList: [],
            apiLoaded: false,
            history_flag: false,
            historyList: [],
        }
    },
    mounted () {
        
    },
    methods: {
        selectItem(theItem) {
            this.inputValue = theItem.name
            this.apiLoaded = false;
            this.$refs.dropdowninput.focus();
        },
        selectHistoryItem(item) {
            this.inputValue = item;
            this.$refs.dropdowninput.focus();
        },
        itemVisible(item) {
            return true;
        },
        searchData(e) {
            if (e.key == 'Enter') {
                this.$router.push({ name: 'patients.search', params: {search: this.inputValue } }).catch(()=>{});
                
                this.historyList.unshift(this.inputValue);
                if(this.historyList.length > 5) this.historyList.pop();

                this.apiLoaded = false;
                this.history_flag = false;
            } else {
                this.getList();
            }
        },
        getList() {
            axios.get(`/api/clinic/patients?per_page=5&page=1&q=` + this.inputValue)
                .then(res => {
                    this.itemList = res.data.patients.data;
                    this.apiLoaded = true;
                })
                .catch(error => {
                
                });
        },
        removeHandle() {
            this.inputValue = '';
        },
        removeHistoryItem(index, e) {
            e.stopPropagation();
            
            this.historyList.splice(index, 1);
        },
        lossfocus() {
            let parent = this;

            setTimeout(function() {
                parent.history_flag = false;
                parent.apiLoaded = false;
            }, 500);
        }
    }
}
</script>