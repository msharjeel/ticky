import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        user: false,
        permissions: {},
        settings: false,

        leftTopLogo: '/images/ticky_logo1.jpg',
        // leftTopLogo: '/images/tech_direct_logo1.png',
    },
    mutations: {
        updateLeftTopLogo(state, data){
            // console.log('updateLeftTopLogo', data, state);

            if(state.user != false && state.user.department != null && state.user.department.logo != 'gravatar'){
                state.leftTopLogo = state.user.department.logo;
            }
            else if(state.settings != false && state.settings.icon != undefined){
                state.leftTopLogo = state.settings.icon;
            }
        },
        setSettings(state, data) {
            state.settings = data;

            this.commit("updateLeftTopLogo", {'from':'setSettings'});
        },
        login(state, response) {
            state.user = response.user;
            this.commit("updateLeftTopLogo", {'from':'login'});

            state.permissions = response.user.role.permissions;
            localStorage.setItem('token', response.token);
            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.token;

        },
        logout(state) {
            axios.post('api/auth/logout').then(function () {
                state.user = false;
                this.commit("updateLeftTopLogo", {'from':'logout'});
            });
            delete window.axios.defaults.headers.common.Authorization;
            localStorage.removeItem('token');

        },
        setUser(state) {
            if (localStorage.getItem('token')) {
                const self = this;
                axios.get('api/auth/user').then(function (response) {
                    state.user = response.data;
                    self.commit("updateLeftTopLogo", {'from':'setUser'});
                    state.permissions = response.data.role.permissions;
                });
            }
        },
        updateUser(state, response) {
            state.user = response;
            this.commit("updateLeftTopLogo", {'from':'updateUser'});
            state.permissions = response.role.permissions;
        },
    }
});

export default store;

