<template>

    <div class="content-view">

        <div class="hero">
            <div class="desc">
                <h2>{{ info.title }}</h2>
                <p>{{ info.description }}</p>

                <small>
                    ver: <strong>{{ info.version }}</strong>
                </small>
                <div></div>
                <small>
                    host: <strong>{{ host }}</strong>
                </small>
            </div>
        </div>

        <resource v-for="(endpoint, index) in endpoints" :key="'endpoint-' + index" :endpoint="endpoint"></resource>

        <div v-if="size(webhooks)" class="section-divider">
            <h2>Webhooks</h2>
            <p>The following callbacks can be sent from our server to yours.</p>
        </div>

        <resource v-for="(webhook, index) in webhooks" :key="'webhook-' + index" :endpoint="webhook"></resource>
    </div>

</template>

<script>

import Resource from '@/Resource'
import { size } from 'lodash'

export default {
    components: {
        Resource
    },
    data() {
        return {
            info: window.info,
            host: window.root
        }
    },
    computed: {
        endpoints() {
            return this.$store.getters['endpoints']
        },
        webhooks() {
            return this.$store.getters['webhooks']
        }
    },
    methods: {
        size(value) {
            return size(value)
        }
    }
}

</script>
