<template>
    <section id="worker">
        <div class="worker-wrapper col-xs-12">
            <div class="client-wrapper col-xs-12">
                <h4 id="top" v-show="display"> > Страница Рабочего</h4>
            </div>
            <a href="#" id="scroll" v-scroll-to="{ el: '#' + id, onDone:onScrollDone}" >{{name}}</a>
            <div>
                <p>
                    Чтобы присоединиться к рабочей группе Арбайт и получать заявки на работу,
                    пройдите регистрацию в нашей системе. Для регистрации потребуются документы,
                    подтверждающие личность.
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
                    достоверности предоставленных мной персональных данных, в том числе с
                    использованием услуг иного лица на основании заключаемого с этим лицом договора,
                    в том числе государственного контракта, либо путем принятия соответствующего акта.
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
                    Я даю согласие на обработку персональных данных.
                </checkbox>
            </p>
            <div style="padding-left:30.8%">
                <button id="proceed" class="btn btn-default" v-bind:disabled="!checkbox">
                    <router-link v-bind:class="{ disabled: !checkbox }" :to="{ name: 'citizen' }"
                                 style="width: 100%; height: 100%; display: block;">
                        Продолжить
                    </router-link>
                </button>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        name: "worker",
        data: function () {
            return {
                display: true,
                name: "Вниз",
                id: "proceed",
                checkbox: 0,
                start: true
            }
        },
        methods: {
            onScrollDone() {
                document.getElementById('scroll').style.display = "none";
            }
        },
        mounted(){
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