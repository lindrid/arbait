<template>
    <div class="center">
        <p>
            Нажмите "получить код", к вам поступит звонок,
            снимите трубку, звонок сразу сбросится.
            Четыре последние цифры номера - это и есть код подтверждения.
        </p>
        <div class="container">
            <form autocomplete="off" @submit.prevent="send" method="post">
                <input class="damn center2 child"
                       name="number"
                       required=""
                       type="tel"
                       placeholder="+7 914 123-45-67"
                       v-model="tel_number"
                       v-mask="'+7 999 999-99-99'"
                       maxlength="16"
                />
                <button class="btn btn-success center2 child" type="submit">Получить код</button>
            </form>
        </div>
        <div class="container child2">
            <form autocomplete="off" @submit.prevent="verify" method="post">
                <input class="center2 child"
                       required=""
                       type="number"
                       placeholder="1234"
                       v-model="user_ver_code"
                       maxlength="4"
                       v-mask="'9999'"
                />
                <button class="btn btn-success center2 child" type="submit">Отправить код</button>
            </form>
        </div>
    </div>
</template>

<script>
    //import CheckRegistrationMixin from '../check_registration_mixin';

    const Cookies = require('js-cookie');

    export default {
        name: "verify",

        //mixins:[CheckRegistrationMixin],

        data(){
            return {
                email: null,
                password: null,
                error: false,
                message: 'Введите код',
                user_ver_code: '',
                tel_number: '',
                user_type: ''
            }
        },
        methods: {
            send: function () {
                Cookies.set('user_ver_NUM', this.tel_number);
                console.log(this.tel_number);

                this.$axios.post('/send_ver_request')
                    .then(function (responce) {
                        console.log(responce);
                        if (responce.data.error == false) {
                            Cookies.set('server_ver_ID', responce.data.server_ver_ID);
                            Cookies.set('server_ver_CODE', responce.data.server_ver_CODE);
                            Cookies.set('server_ver_CNT', responce.data.server_ver_CNT);
                        }
                        else {
                            alert(responce.data.error_msg);
                        }
                    });
            },
            verify() {
                var app = this;
                this.$axios.post('/verify_user',
                    {
                        user_ver_code: this.user_ver_code,
                        page: 'verify'
                    })
                    .then(function (resp) {
                        console.log(resp);
                        if (resp.status == 200) {
                            alert("Номер подтвержден");
                            app.$router.push({name: 'offer', params: { user_type: app.user_type }});
                        }
                        else if (resp.status == 400) {
                            alert("Неверный код");
                        }
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Ошибка");
                    });

            },
        },

        mounted() {
            //this.checkRegistrationProgress('verify');
            this.user_type = this.$route.params.user_type;
            this.tel_number = this.$route.params.phone;
        }
    }
</script>

<style scoped>
    p {
        font-size: 150%;
        text-align: center;
    }
    .center {
        margin: auto;
    }
    .center2 {
        width: 200px;
        font-size: 170%;
        margin: auto;
    }
    .child {
        margin: 10px; /* Отступы вокруг */
    }
    .child2 {
        margin: 15px 0px 0px 0px; /* Отступы вокруг */
    }
</style>