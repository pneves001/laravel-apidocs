<template>

    <div>
        <slot />
        <codemirror v-model="codeFormatted" :options="options"  @input="onInput" />
    </div>

</template>

<script>

import { debounce } from 'lodash'

export default {
    props: {
        code: {
            type: [Array, Object, String],
            default: ''
        },
        options: [Object],
        raw: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            codeFormatted: ''
        }
    },
    watch: {
        code: {
            handler(value) {
                this.formatCode(value)
            },
            immediate: true
        }
    },
    methods: {
        formatCode(value) {
            this.codeFormatted = this.raw ? value : JSON.stringify(value, null, 4)
        },
        onInput(value) {
            this.$emit('update', value)
        }
    }
}

</script>
