<template>
    <div class="snippets mt-4">
        <div class="flex border-b border-grey-light mb-2">
            <div 
                v-for="lang in languages" 
                :key="lang"
                class="px-4 py-2 cursor-pointer text-sm font-bold uppercase tracking-wider"
                :class="{'text-purple border-b-2 border-purple': current === lang, 'text-grey-dark': current !== lang}"
                @click="current = lang"
            >
                {{ lang }}
            </div>
        </div>
        <div class="relative group">
            <pre class="bg-grey-darkest text-white p-4 rounded text-xs overflow-x-auto"><code>{{ code }}</code></pre>
            <button @click="copy" class="absolute top-0 right-0 mt-2 mr-2 bg-purple text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Copy</button>
        </div>
    </div>
</template>

<style scoped>
.snippets { margin-top: 1rem; }
.flex { display: flex; }
.border-b { border-bottom-width: 1px; }
.border-grey-light { border-color: #dae1e7; }
.mb-2 { margin-bottom: 0.5rem; }
.cursor-pointer { cursor: pointer; }
.text-sm { font-size: 0.875rem; }
.font-bold { font-weight: 700; }
.uppercase { text-transform: uppercase; }
.tracking-wider { letter-spacing: 0.05em; }
.text-purple { color: #9561e2; }
.border-b-2 { border-bottom-width: 2px; }
.border-purple { border-color: #9561e2; }
.text-grey-dark { color: #8795a1; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
.relative { position: relative; }
.group:hover .opacity-0 { opacity: 1; }
.bg-grey-darkest { background-color: #3d4852; }
.text-white { color: #fff; }
.p-4 { padding: 1rem; }
.rounded { border-radius: 0.25rem; }
.text-xs { font-size: 0.75rem; }
.overflow-x-auto { overflow-x: auto; }
.absolute { position: absolute; }
.top-0 { top: 0; }
.right-0 { right: 0; }
.mt-2 { margin-top: 0.5rem; }
.mr-2 { margin-right: 0.5rem; }
.bg-purple { background-color: #9561e2; }
.px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
.py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.opacity-0 { opacity: 0; }
.transition { transition: opacity 0.3s; }
</style>

<script>
import fetchToCurl from 'fetch-to-curl'
import { isEmpty, replace, forEach } from 'lodash'
import qs from 'qs'


export default {
    props: ['endpoint'],
    data() {
        return {
            current: 'curl',
            languages: ['curl', 'js']
        }
    },
    computed: {
        code() {
            if (this.current === 'curl') {
                return this.curl
            }
            if (this.current === 'js') {
                return this.javascript
            }
            return ''
        },
        curl() {
            let options = {
                method: this.endpoint.method,
                headers: this.endpoint.headers || {}
            }
            
            // Add Authorization header placeholder if not present
            if (this.$store.getters['token']) {
                options.headers['Authorization'] = `Bearer ${this.$store.getters['token']}`
            } else if (!options.headers['Authorization']) {
                 options.headers['Authorization'] = 'Bearer <token>'
            }

            if (!isEmpty(this.endpoint.body?.data)) {
                // Simplified body generation for snippet
                 let body = {}
                 forEach(this.endpoint.body.data, (v, k) => {
                     body[k] = v.example || '<value>'
                 })
                 options['body'] = body
            }

            return fetchToCurl(this.fullUrl, options)
        },
        javascript() {
            let url = this.fullUrl
            let method = this.endpoint.method
            let headers = this.endpoint.headers || {}

            if (this.$store.getters['token']) {
                headers['Authorization'] = `Bearer ${this.$store.getters['token']}`
            } else if (!headers['Authorization']) {
                 headers['Authorization'] = 'Bearer <token>'
            }

            let body = null
            if (!isEmpty(this.endpoint.body?.data)) {
                 body = {}
                 forEach(this.endpoint.body.data, (v, k) => {
                     body[k] = v.example || '<value>'
                 })
            }

            let opts = {
                method: method,
                headers: headers
            }
            
            if (body) {
                opts.body = JSON.stringify(body)
            }

            return `fetch("${url}", ${JSON.stringify(opts, null, 2)})
  .then(response => response.json())
  .then(data => console.info(data));`
        },
        fullUrl() {
            // Simplified URL generation for snippets using placeholders
            let result = this.endpoint.uri;
            if (this.endpoint.params) {
                forEach(this.endpoint.params, (value, key) => {
                    result = replace(result, `{${key}}`, `<${key}>`)
                })
            }
            
            let url = window.root + '/' + result

            if (!isEmpty(this.endpoint.query)) {
                // Simplified query string generation for snippet
                 let query = {}
                 forEach(this.endpoint.query, (v, k) => {
                     query[k] = `<${k}>`
                 })
                 let queryString = qs.stringify(query);
                 url = `${url}?${queryString}`
            }
            return url
        }
    },
    methods: {
        copy() {
            navigator.clipboard.writeText(this.code)
        }
    }
}
</script>
