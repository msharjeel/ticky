import '@/plugins/lodash';
import '@/plugins/moment';
import '@/plugins/ladda';
import '@/plugins/axios';
import "@/components";

import Vue from "vue";
import Vuex from 'vuex';
import Meta from 'vue-meta';
import SvgVue from 'svg-vue';
import Notifications from 'vue-notification';
import {VueReCaptcha} from "vue-recaptcha-v3";
import VueElementLoading from 'vue-element-loading';
import TextareaAutosize from 'vue-textarea-autosize';
import vueFilterPrettyBytes from 'vue-filter-pretty-bytes';

import store from '@/store';
import App from "@/views/app";
import i18n from "@/language";
import router from "@/views/router";

Vue.use(Vuex);
Vue.use(Meta);
Vue.use(SvgVue);
Vue.use(Notifications);
Vue.use(TextareaAutosize);
Vue.use(vueFilterPrettyBytes);
Vue.component('VueElementLoading', VueElementLoading);
if (window.app.recaptcha_enabled) {
    Vue.use(VueReCaptcha, {siteKey: window.app.recaptcha_public});
}

Vue.config.productionTip = false;

Vue.mixin({
    methods: {
        rlogs: function (...params)
        {
            // console.log('in app.js');
            console.log(...params);
        },
        userRole_FromMixin(roleName){
            let isUserRoleSame = false;
            if( store.state.user != undefined && 
                store.state.user.role != undefined && 
                store.state.user.role.name != undefined && 
                store.state.user.role.name == roleName
            ){
                isUserRoleSame = true;
            }
            return isUserRoleSame;
        },
        userHasPermission_FromMixin(permissionName){
            let hasPermission = false;
            if( store.state.user != undefined && 
                store.state.user.permissions != undefined && 
                store.state.user.permissions.indexOf(permissionName) > -1
            ){
                hasPermission = true;
            }
            return hasPermission;
        },
        fetchAllowedStatusList_FromMixin(statusList){
            let allowedStatusList = [];
            for(let x in statusList){
                let row = statusList[x];
                if(row.allowed_for_status_change === 1){
                    allowedStatusList.push( row );
                }
            }
            return allowedStatusList;
        },
    }
});

new Vue({
    i18n,
    store,
    router,
    render: h => h(App),
    mounted() {
        this.initI18n();
        this.$store.commit('setUser');
        this.$store.commit('setSettings', window.app);
    },
    methods: {
        initI18n() {
            this.$i18n.locale = document.documentElement.lang;
            this.loadTranslations();
        },
        loadTranslations() {
            let self = this;
            axios.get('api/lang/' + self.$i18n.locale).then(function (response) {
                self.$i18n.setLocaleMessage(self.$i18n.locale, response.data);
            });
        },
    }
}).$mount("#app");
