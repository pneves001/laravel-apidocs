<template>
    <div class="parameters mb-4">
        <h4 class="text-sm font-bold uppercase tracking-wider text-grey-darker mb-2">{{ title }}</h4>
        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="p-3 bg-grey-lightest font-bold uppercase text-xs text-grey-dark border-b border-grey-light">Name</th>
                        <th class="p-3 bg-grey-lightest font-bold uppercase text-xs text-grey-dark border-b border-grey-light">Type</th>
                        <th class="p-3 bg-grey-lightest font-bold uppercase text-xs text-grey-dark border-b border-grey-light">Required</th>
                        <th class="p-3 bg-grey-lightest font-bold uppercase text-xs text-grey-dark border-b border-grey-light">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(param, name) in params" :key="name" class="hover:bg-grey-lightest">
                        <td class="p-3 border-b border-grey-light font-mono text-sm text-purple-dark">{{ name }}</td>
                        <td class="p-3 border-b border-grey-light text-sm text-grey-darker">{{ param.type || 'string' }}</td>
                        <td class="p-3 border-b border-grey-light text-sm text-grey-darker">
                            <span v-if="param.required" class="bg-red-light text-white text-xs px-2 py-1 rounded">Yes</span>
                            <span v-else class="text-grey text-xs">No</span>
                        </td>
                        <td class="p-3 border-b border-grey-light text-sm text-grey-darker">{{ param.description || '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.parameters {
    margin-bottom: 1rem;
}
.parameters h4 {
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #606f7b;
    margin-bottom: 0.5rem;
}
.bg-white { background-color: #fff; }
.rounded { border-radius: 0.25rem; }
.shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
.overflow-hidden { overflow: hidden; }
.w-full { width: 100%; }
.text-left { text-align: left; }
.border-collapse { border-collapse: collapse; }
.p-3 { padding: 0.75rem; }
.bg-grey-lightest { background-color: #f8fafc; }
.font-bold { font-weight: 700; }
.uppercase { text-transform: uppercase; }
.text-xs { font-size: 0.75rem; }
.text-grey-dark { color: #8795a1; }
.border-b { border-bottom-width: 1px; }
.border-grey-light { border-color: #dae1e7; }
.hover\:bg-grey-lightest:hover { background-color: #f8fafc; }
.font-mono { font-family: monospace; }
.text-sm { font-size: 0.875rem; }
.text-purple-dark { color: #794acf; }
.text-grey-darker { color: #606f7b; }
.bg-red-light { background-color: #ef5753; }
.text-white { color: #fff; }
.px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
.py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.text-grey { color: #b8c2cc; }
</style>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        params: {
            type: Object,
            required: true
        }
    }
}
</script>
