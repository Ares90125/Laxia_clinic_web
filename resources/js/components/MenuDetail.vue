<template>
<div>
    <div class="row mb-2"  v-for="(item, id) in menu_detail_data" :value="id" :key="id">
        <div class="col-7">
            <p>{{item.name}}</p>
        </div>
        <div class="col-3">
            <p>{{item.price}}</p>
        </div>
        <div class="col-2 text-center">
            <p><i class="bi bi-x-circle-fill" @click="removeMenu(id)"></i></p>
        </div>
    </div>
</div>
</template>
<style scoped>
    p{
        /* font-size: 18px; */
        color: #131340;
        font-weight: 500;
        padding-top: 6px;
    }
    i {
        cursor: pointer;
    }
</style>
<script>
export default {
    name: "MenuDetail",
    // props: ['m_menu_data', 'm_idx'],
    data: () => ({
      m_name: '',
      m_price: '',
      m_index: 0,
    }),
    computed: {
        menu_detail_data() {
            return this.$store.state.data.selected_menus;
        },
    },
    mounted() {
	},
    methods: {
        removeMenu (idx) {
            this.menu_detail_data.splice(idx, 1)
            // this.$store.dispatch('data/addMenuDetails', { data : this.menu_detail_data })
        },
    }
}
</script>
