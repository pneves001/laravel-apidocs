<template>

    <div :id="anchor" class="resource" :class="classes">
        <div class="title">
            {{ endpoint.title }}
        </div>
        <div class="description desc">
            {{ endpoint.description }}
        </div>
        
        <div class="params-section">
            <parameters v-if="size(endpoint.params)" title="Route Parameters" :params="endpoint.params" />
            <parameters v-if="size(endpoint.query)" title="Query Parameters" :params="endpoint.query" />
            <parameters v-if="size(endpoint.body && endpoint.body.data)" title="Body Parameters" :params="endpoint.body.data" />
        </div>

        <url :endpoint="endpoint" />

        <div class="samples">
            <examples v-if="size(examples)" :items="examples" />

            <responses v-if="size(responses)" :items="responses" />
        </div>

        <playground :endpoint="endpoint" />
        
        <snippets :endpoint="endpoint" />
    </div>

</template>

<script>

import Examples from '@/resource/Examples'
import Responses from '@/resource/Responses'
import Playground from '@/resource/Playground'
import Url from '@/resource/Url'
import Parameters from '@/resource/Parameters'
import Snippets from '@/resource/Snippets'
import { size } from 'lodash'

export default {
    props: ['endpoint'],
    components: {
        Examples,
        Responses,
        Playground,
        Url,
        Parameters,
        Snippets
    },
    computed: {
        examples() {
            return this.endpoint.examples || []
        },
        responses() {
            return this.endpoint.returns || []
        },
        anchor() {
            return `${this.endpoint.id}`
        },
        classes() {
            return {
                'deprecated': this.endpoint.deprecated
            }
        }
    },
    methods: {
        size(value) {
            return size(value)
        }
    }
}

</script>
