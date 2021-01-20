<template>
    <section id="client">
        <div class="client-wrapper col-xs-12">
            <h4 v-show="display">Услуги</h4>
            <div class="client-slider">
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><router-link :to="{ name: 'service' }">Грузчики</router-link></div>
                        <div class="swiper-slide"><router-link :to="{ name: 'service' }">Грузоперевозки</router-link></div>
                        <div class="swiper-slide"><router-link :to="{ name: 'service' }">Землекопы</router-link></div>
                        <div class="swiper-slide"><router-link :to="{ name: 'service' }">Уборка территории, уборка снега</router-link></div>
                        <div class="swiper-slide"><router-link :to="{ name: 'service' }">Демонтаж</router-link></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        name: "client",
        mounted() {
            let recaptchaScript = document.createElement('script')
            recaptchaScript.setAttribute('src', '/public/js/custom.js')
            document.head.appendChild(recaptchaScript)
            // $( ".current-brand" ).css('display', 'block');

        },
        data: function () {
            return {
                display: true
            }
        },
    }
</script>

<style scoped>

</style>