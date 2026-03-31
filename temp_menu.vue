<template>

    <div class="aside">
        <div class="header-info pb-6">
            <div v-if="logo" class="logo-container mb-4">
                <img :src="logo" class="max-w-full h-auto">
            </div>
            <div class="title text-xl font-bold">
                {{ info.title }}
            </div>
            <div class="version text-xs opacity-50">
                v{{ info.version }}
            </div>
        </div>

        <div class="pb-4">
            <input v-model="token" type="text" class="w-full block bg-purple-white shadow rounded border-0 p-1 px-3 outline-none mb-2" placeholder="Bearer Token">
            <input v-model="queryString" type="search" class="w-full block bg-purple-white shadow rounded border-0 p-1 px-3 outline-none" placeholder="Search">
        </div>

        <scrollactive active-class="active" :offset="80">
            <menu-group v-for="(group, slug) in groupped" :key="slug" :name="slug" :items="group"></menu-group>
        </scrollactive>

    </div>

</template>

<script>

import MenuGroup from '@/aside/menu/Group'
import { groupBy, filter } from 'lodash'

export default {
    props: [],
    data() {
        return {
            queryString: ''
        }
    },
    components: {
        MenuGroup
    },
    computed: {
        token: {
            get () {
                return this.$store.getters['token']
            },
            set (value) {
                this.$store.commit('setToken', value)
            }
        },
        info() {
            return this.$store.getters['info']
        },
        logo() {
            return this.$store.getters['logo']
        },
        endpoints() {
...
        },
        query() {
            return this.queryString.toLowerCase()
        },
        filtered() {
            return filter(this.endpoints, (endpoint) => {
                return endpoint.title.toLowerCase().includes(this.query)
            })
        },
        groupped() {
            return groupBy(this.filtered, 'group')
        },
    }
}

</script>
