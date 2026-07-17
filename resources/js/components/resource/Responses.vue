<template>

    <div class="tabs">
        <div class="header">
            <vue-custom-scrollbar class="scroll-area" :settings="settings">
                <div
                    v-for="(item, index) in flattened"
                    :key="index"
                    class="tab"
                    :class="{'current': index == current}"
                    @click="select(index)" >
                    {{ item.code }} <span v-if="item.description" class="text-xs ml-1 opacity-50">({{ item.description }})</span>
                </div>
            </vue-custom-scrollbar>

        </div>
        <div class="content">
            <code-editor
                v-for="(item, index) in flattened"
                v-if="current == index"
                :key="index"
                :code="item.response"
                :options="{ readOnly: true }">
                {{ item.description }}
            </code-editor>
        </div>
    </div>

</template>

<style>
    .tabs {
        padding-left: 1rem;
    }
</style>

<script>

import CodeEditor from '@/CodeEditor'
import vueCustomScrollbar from 'vue-custom-scrollbar'
import { flatMap, map } from 'lodash'

export default {
    props: ['items'],
    data() {
        return {
            current: 0,
            settings: {
                suppressScrollY: true,
                suppressScrollX: false,
                wheelPropagation: true
            }
        }
    },
    components: {
        CodeEditor,
        vueCustomScrollbar
    },
    computed: {
        flattened() {
            return flatMap(this.items, (responses, code) => {
                // Backward compatibility: If the response is not an array, make it one.
                if (!Array.isArray(responses)) {
                    responses = [responses]
                }
                return map(responses, (response) => {
                    return {
                        code,
                        ...response
                    }
                })
            })
        }
    },
    mounted() {
        this.current = 0
    },
    methods: {
        select(index) {
            this.current = index
        }
    }
}

</script>
