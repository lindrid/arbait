<template>
    <section id="worker">
        <div class="worker-wrapper col-xs-12">
            <div class="client-wrapper col-xs-12">
                <h4 id="top" v-show="display"> Гражданство</h4>
            </div>
            <div id="divElement" >
                <radio class="center1" name="nationality" value="1" v-model="nationality"
                       :onchange="onRadioClick()"
                >
                   Я гражданин РФ
                </radio>
                <radio class="center2"  name="nationality" value="2" v-model="nationality"
                       :onchange="onRadioClick()"
                >
                    Я гражданин другой страны
                </radio>
                <p>
                    <router-link :to="{ name: 'personal-data', params: {user_type: this.user_type} }" class="btn btn-default center3">
                        Назад
                    </router-link>
                    <button id="proceed" class="btn btn-default center4"  v-bind:disabled="!nationality"
                            v-on:click="saveProgressAndGoToRegister"
                    >
                            Продолжить
                    </button>
                </p>
            </div>
        </div>
    </section>
</template>

<script>

    export default {
        name: "citizen",
        data: function () {
            return {
                start: true,
                display: true,
                nationality: 0,
                RF_citizen: false,
                alien_citizen: false,
                route_name: '',
                user_type: ''
            }
        },
        methods: {
            onRadioClick() {
                if (this.nationality == 1) {
                    this.route_name = 'register';
                }
                else if (this.nationality == 2) {
                    this.route_name = 'register-alien';
                }
            },

            saveProgressAndGoToRegister() {
                var success = true;
                var app = this;

                this.$axios.post(
                    'save-progress',
                    {
                        page: 'citizen',
                        user_type: app.user_type
                    }
                ).
                catch(function (resp) {
                    alert("Возникла ошибка. Возможно, нет связи с сервером, проверьте интернет");
                    success = false;
                });

                if (success) {
                    this.$router.push({
                        name: app.route_name,
                        user_type: app.user_type
                    });
                }
            }
        },
        mounted(){
            this.user_type = this.$route.params.user_type;
        }
    }
</script>

<style scoped>
    .disabled {
        pointer-events:none;
        opacity:0.6;
    }

    .center1 {
        font-size: 170%;
        position: fixed;
        top: 30%;
        left: 10%;
        transfrom: translateY(-30%);
        transfrom: translateX(-10%);
    }

    .center2 {
        font-size: 170%;
        position: fixed;
        top: 40%;
        left: 10%;
        transfrom: translateY(-40%);
        transfrom: translateX(-10%);
    }

    .center3 {
        font-size: 130%;
        position: fixed;
        top: 60%;
        left: 10%;
        transfrom: translateY(-60%);
        transfrom: translateX(-10%);
    }

    .center4 {
        font-size: 130%;
        position: fixed;
        top: 60%;
        left: 45%;
        transfrom: translateY(-60%);
        transfrom: translateX(-45%);
    }

</style>