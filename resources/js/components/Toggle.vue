<template>
    <label :for="id + '_button'" :class="{'active': isActive}" class="toggle__button">
        <!-- <span v-if="isActive" class="toggle__label">{{ enableText }}</span>
        <span v-if="! isActive" class="toggle__label">{{ disabledText }}</span> -->

        <input type="checkbox" :disabled="disabled" :id="id + '_button'" v-model="checkedValue">
        <span class="toggle__switch">
            <div class="d-flex">
                <p class="toggle__text__before" :style="!isActive ? 'color: #fff;': 'color: #5F6377'">{{ enableText }}</p>
                <p class="toggle__text__after" :style="isActive ? 'color: #fff;': 'color: #5F6377'">{{ disabledText }}</p>
            </div>
        </span>
    </label>
</template>

<script>
export default {
    name: "Toggle",
    props: {
        disabled: {
            type: Boolean,
            default: false
        },

        labelEnableText: {
            type: String,
            default: '掲載'
        },
        
        labelDisableText: {
            type: String,
            default: '停止'
        },

        id: {
            type: String,
            default: 'primary'
        }, 

        defaultState: {
            type: Boolean,
            default: true
        }
    },

    data() {    
        return {
            currentState: this.defaultState
        }
    },

    watch: {
        defaultState: function defaultState() {
            this.currentState = Boolean(this.defaultState)
        }
    },

    computed: {
        // currentState() {
        //     return this.defaultState;
        // },

        isActive() {
            return this.currentState;
        },

        enableText() {
            return this.labelEnableText;
        },

        disabledText() {
            return this.labelDisableText;
        },

        checkedValue: {
            get() {
                return this.currentState;
            },

            set(newValue) {
                this.currentState = newValue;
                this.$emit('change', newValue);
            }
        }
    }
}
</script>

<style scoped>
.toggle__button {
    vertical-align: middle;
    user-select: none;
    cursor: pointer;
}
.toggle__button input[type="checkbox"] {
    opacity: 0;
    position: absolute;
    width: 1px;
    height: 1px;
}
.toggle__button .toggle__switch {
    display:inline-block;
    height:40px;
    border-radius:27px;
    width:200px;
    background: #F3F3F3;
    box-shadow: inset 0 0 1px #F3F3F3;
    position:relative;
    transition: all .25s;
}

.toggle__button .toggle__switch::after, 
.toggle__button .toggle__switch::before {
    content: "";
    position: absolute;
    display: block;
    height: 40px;
    width: 100px;
    border-radius: 27px;
    left: 0;
    top: 0;
    transform: translateX(0);
    transition: all .25s cubic-bezier(.5, -.6, .5, 1.6);
}

.toggle__button .toggle__switch::after {
    background: #5CA3F6;
    box-shadow: 0 0 1px #5CA3F6;
}
.toggle__button .toggle__switch::before {
    background: #5CA3F6;
    box-shadow: 0 0 0 3px #5CA3F6;
    opacity:0;
}

.active .toggle__switch {
    background: #F3F3F3;
    box-shadow: inset 0 0 1px #F3F3F3;
}
.toggle__text__before{
    position: relative;
    top: 10px;
    text-align: center;
    z-index: 10;
    width: 50%;
    font-size: 16px;
    font-weight: 500;
}
.toggle__text__after{
    position: relative;
    top: 10px;
    z-index: 10;
    width: 50%;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
}

.active .toggle__switch::after,
.active .toggle__switch::before{
    transform:translateX(40px - 18px);
}
.active .toggle__switch::after p{
    color:#fff;
}

.active .toggle__switch::after {
    left: 100px;
    background: #5CA3F6;
    box-shadow: 0 0 1px #5CA3F6;
}
</style>