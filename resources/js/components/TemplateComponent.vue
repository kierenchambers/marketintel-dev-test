<template>
    <div>
        <div class="container-fluid" id="app-header">
            <div class="row">
                <div class="col-md-10">
                    <h1>Ayima full stack developer test</h1>
                </div>
                <div class="col-md-2">
                    <a @click.prevent="changeScreenSize" class="btn btn-wide-screen" href="#">
                        {{ wideScreenBtnText }}
                    </a>
                </div>
            </div>
        </div>
        <div id="app-body">
            <router-view :wide="wideScreen" @hideScreenMessage="hideScreenMessage"
                         @showScreenMessage="showScreenMessage"></router-view>
        </div>
        <screen-message-component :heading="messageComponent.heading" :message="messageComponent.message"
                                  v-if="messageComponent.show"></screen-message-component>
    </div>
</template>

<script>
    import ScreenMessageComponent from '../components/ScreenMessageComponent';

    export default {
        data: function () {
            return {
                wideScreen: false,
                messageComponent: {
                    show: true,
                    heading: 'Loading',
                    message: 'Please wait as the application loads.'
                }
            }
        },
        computed: {
            wideScreenBtnText: function () {
                return this.wideScreen ? 'Box Layout' : 'Full Width Layout';
            }
        },
        methods: {
            changeScreenSize: function () {
                this.wideScreen = !this.wideScreen;
            },
            showScreenMessage: function (heading, message) {
                this.messageComponent.heading = heading;
                this.messageComponent.message = message;
                this.messageComponent.show = true;
            },
            hideScreenMessage: function () {
                this.messageComponent.show = false;
            }
        },
        components: {
            ScreenMessageComponent
        }
    }
</script>
