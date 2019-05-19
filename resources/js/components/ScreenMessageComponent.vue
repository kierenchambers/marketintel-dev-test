<template>
    <div class="screen-message-content">
        <div class="screen-message-center">
            <h1 v-if="heading">{{ heading }}</h1>
            <p v-if="message">{{ message }}</p>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['heading', 'message']
    }
</script>
