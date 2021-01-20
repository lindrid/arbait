<template>
    <section id="worker">
        <div class="worker-wrapper col-xs-12">

            <a href="#" id="scroll" v-scroll-to="{ el: '#' + id, onDone:onScrollDone}" >{{name}}</a>
            <div>
                <p v-if="user_type == 'w'">
                    Чтобы присоединиться к рабочей группе Арбайт и получать заявки на работу,
                    пройдите регистрацию в нашей системе.
                </p>
                <p v-if="user_type == 'd'">
                    Для регистрации в системе потребуется документ, подтверждающий личность: паспорт РФ.
                    Если вы не являетесь гражданином РФ, тогда паспорт вашей страны.
                </p>
                <div class="page-subtitle-wrapper">
                    <h3>ТРЕБУЕТСЯ СОГЛАСИЕ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ</h3>
                </div>
                <p>
                    Приложение требует ввода персональных данных, которые будут переданы на сервер
                    Службы заказа рабочих "Арбайт" (ИП Федоров Д.С).
                </p>
                <p>
                    Под обработкой персональных данных в указанном законе понимаются действия
                    (операции) с персональными данными, включая сбор, запись, систематизацию,
                    накопление, хранение, уточнение (обновление, изменение), извлечение,
                    использование, передачу (распространение, предоставление, доступ),
                    обезличивание, блокирование, удаление, уничтожение персональных данных.
                </p>
                <p>
                    Даю свое согласие на обработку персональных данных и разрешаю проверку
                    достоверности предоставленных мной персональных данных Службе заказа рабочих
                    "Арбайт" (ИП Федоров Д.С).
                </p>
                <p>
                    Гарантирую, что представленная мной информация является полной, точной и достоверной,
                    а также что при представлении информации не нарушаются действующее законодательство
                    Российской Федерации, законные права и интересы третьих лиц. Вся представленная
                    информация заполнена мною в отношении себя лично.
                </p>
                <p>
                    Настоящее согласие действует в течение всего периода хранения персональных данных,
                    если иное не предусмотрено законодательством Российской Федерации.
                </p>
            </div>
            <p>
                <checkbox name="terms" v-model="checkbox">
                    Я даю согласие на обработку персональных данных, в том числе подтверждаю все, указанное выше.
                </checkbox>
            </p>
            <div style="padding-left:30.8%">
                <button id="proceed" class="btn btn-default" v-bind:disabled="!checkbox"
                        @click="saveProgressAndGoToCitizen()"
                >
                        Продолжить
                </button>
            </div>
        </div>
    </section>
</template>

<script>
    import CheckRegistrationMixin from '../check_registration_mixin';

    export default {
        name: "personal-data",

        mixins:[CheckRegistrationMixin],

        data: function () {
            return {
                display: true,
                name: "Вниз",
                id: "proceed",
                checkbox: 0,
                start: true,
                user_type: ''
            }
        },

        methods: {
            onScrollDone() {
                document.getElementById('scroll').style.display = "none";
            },
            saveProgressAndGoToCitizen() {
                var success = true;
                var app = this;
                this.$axios.post(
                    'save-progress',
                    {
                        page: 'personal-data',
                        user_type: app.user_type
                    }
                ).
                catch(function (resp) {
                    alert("На странице возникла ошибка. Возможно нет связи с сервером, проверьте интернет");
                    success = false;
                });

                if (success) {
                   this.$router.push({
                       name: 'citizen',
                       params: {user_type: app.user_type}
                    });
                }
            }
        },


        mounted(){
            this.user_type = this.$route.params.user_type;
            this.checkRegistrationProgress('personal-data', this.user_type);
        }
    }
</script>

<style scoped>
    p {
        font-size: 130%;
    }
    h3 {
        text-align: center;
    }

    #proceed {
        display: block;
        text-align: center;
        font-size: 130%;
    }

    #scroll {
        background-color: #000;
        border-radius: 50%;
        bottom: 50px;
        color: #f4f009;
        font-size: 16px;
        height: 50px;
        line-height: 50px;
        outline: 0;
        position: fixed;
        right: 50px;
        text-align: center;
        width: 50px;
    }

    .disabled {
        pointer-events:none;
        opacity:0.6;
    }

    .page-subtitle-wrapper {
        position: relative;
        display: block;
        background-color: #e7e7e7;
        color: #908e04;
        border-bottom: 1px solid #ddd;
        padding: 25px 0;
    }
</style>