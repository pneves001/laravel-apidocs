import Vue from "vue";
import Vuex from "vuex";
import { filter } from 'lodash'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        apidocs: window.apidocs,
        colormap: {
            get: 'teal',
            post: 'orange',
            patch: 'blue',
            put: 'pink',
            delete: 'red',
            options: 'gray',
        },
        currentUri: window.location.hash.replace('#', ''),
        try: {},
        token: '',
    },
    getters: {
        token (state) {
            return state.token
        },
        colormap (state) {
            return state.colormap
        },
        info (state) {
            return state.apidocs.info
        },
        logo (state) {
            return state.apidocs.logo
        },
        groups (state) {
            return state.apidocs.groups
        },
        currentUri (state) {
            return state.currentUri
        },
        endpoints (state) {
            return state.apidocs.endpoints
        },
        webhooks (state) {
            return state.apidocs.webhooks || []
        },
        groupEndpoints: (state) => (name) => {
            return filter(state.apidocs.endpoints, (item) => { return item.group == name })
        }
    },
    mutations: {

        setToken (state, payload) {
            state.token = payload
        },

        currentUri (state, payload) {
            state.currentUri = payload
        }

        // addTry (state, key) {
        //     Vue.set(state.try, key, {
        //         param: {},
        //         query: {},
        //         body: {},
        //         headers: {},
        //     })
        // }

        // setTry (state, payload) {
        //     state.try[payload.key] = value
        //     Vue.set(state.try, key, {
        //         param: {},
        //         query: {},
        //         body: {},
        //         headers: {},
        //     })
        // }

    },
    actions: {}
});
