<template>
    <div>
            <section class="select-skills worker-skills">
                <div class="message" v-bind:class="{ 'is-primary': cwaIsOpen, 'is-closed': !cwaIsOpen }">
                    <div class="category message-header"
                         @click="cwaIsOpen=!cwaIsOpen"
                    >
                        Создать и назначить рабочего
                    </div>

                    <div class="message-body message-content">
                        <div class="row">
                            <div class="form-group" v-bind:class="{ 'has-error': error && errors.phone_call }">
                                <label for="phone_call">Телефон для звонков</label>
                                <input
                                        id="phone_call"
                                        class="form-control"
                                        v-model="phone_call"
                                        placeholder="Введите номер"
                                >
                                <span class="help-block" v-if="error && errors.phone_call">{{ errors.phone_call }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group" v-bind:class="{ 'has-error': error && errors.phone_whatsapp }">
                                <label for="phone">Телефон Whatsapp</label>
                                <button id="same_btn" type=button class="btn btn-default"
                                        @click="phone_whatsapp = phone_call">Тот же</button>
                                <input
                                        id="phone"
                                        name="phone"
                                        class="form-control"
                                        v-model="phone_whatsapp"
                                        placeholder="Введите номер"
                                >
                                <span class="help-block" v-if="error && errors.phone_whatsapp">{{ errors.phone_whatsapp }}</span>
                            </div>
                        </div>

                        <radio name="district"
                               value="1" v-model="district"
                        >
                            заря, вторяк
                        </radio>
                        <radio name="district"
                               value="2" v-model="district"
                        >
                            Столетие, фирсова, бам, молодежная
                        </radio>
                        <radio name="district"
                               value="3" v-model="district"
                        >
                            снеговая падь
                        </radio>
                        <radio name="district"
                               value="4" v-model="district"
                        >
                            первая речка
                        </radio>
                        <radio name="district"
                               value="5" v-model="district"
                        >
                            некрасовская, гоголя, центр
                        </radio>
                        <radio name="district"
                               value="6" v-model="district"
                        >
                            третья рабочая, толстого, шилкинская
                        </radio>

                        <radio name="district"
                               value="7" v-model="district"
                        >
                            чуркин
                        </radio>
                        <radio name="district"
                               value="8" v-model="district"
                        >
                            баляева
                        </radio>
                        <radio name="district"
                               value="9" v-model="district"
                        >
                            луговая
                        </radio>

                        <radio name="district"
                               value="10" v-model="district"
                        >
                            нейбута
                        </radio>
                        <radio name="district"
                               value="11" v-model="district"
                        >
                            спортивная/клубная
                        </radio>
                        <radio name="district"
                               value="12" v-model="district"
                        >
                            тихая
                        </radio>
                        <radio name="district"
                               value="13" v-model="district"
                        >
                            эгершельд
                        </radio>
                        <radio name="district"
                               value="14" v-model="district"
                        >
                            остров русский
                        </radio>
                        <radio name="district"
                               value="15" v-model="district"
                        >
                            пригород
                        </radio>

                        <div class="row">
                            <div class="form-group" v-bind:class="{ 'has-error': error && errors.address }">
                                <label for="phone_call">Адрес</label>
                                <input
                                        id="address"
                                        class="form-control"
                                        v-model="address"
                                        placeholder="пример: Кубанская 20"
                                >
                                <span class="help-block" v-if="error && errors.address">{{ errors.address }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group" v-bind:class="{ 'has-error': error && errors.age }">
                                <label for="phone_call">Возраст</label>
                                <input
                                        id="age"
                                        class="form-control"
                                        v-model="age"
                                        placeholder="Сколько лет"
                                >
                                <span class="help-block" v-if="error && errors.age">{{ errors.age }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                <button @click="saveForm()" class="btn btn-success">Создать</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <div class="panel panel-default">
            <div class="panel-heading">Выбрать рабочего</div>
            <div class="panel-body">

            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: "workerList",
        data: function () {
            return {
                phone_call: '',
                phone_whatsapp: '',
                address: '',
                age: '',
                cwaIsOpen: 0,   //create worker area is open or not
                success: false,
                district: '1',

                error: false,
                errors: {},
            }
        },
        methods: {
            saveForm() {
                //event.preventDefault();
                var app = this;
                if (this.error == true) {
                    this.error = false;
                    this.errors.phone_call = undefined;
                    this.errors.phone_whatsapp = undefined;
                    this.errors.address = undefined;
                }

                this.$axios.get('/workers/create', {
                    params: {
                        phone_call: app.phone_call,
                        phone_whatsapp: app.phone_whatsapp,
                        district: app.district,
                        address: app.address,
                        age: app.age
                    }
                }).then(response => {
                    if (response.status == 200) {
                        app.day_income = response.data.day_income;
                        app.day_outcome = response.data.day_outcome;
                        app.day_profit = response.data.day_profit;

                        app.success = true;
                    }
                    console.log(response);
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    console.log(app.errors);
                    if (typeof app.errors.phone_call !== 'undefined') {
                        document.getElementById("phone_call").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    if (typeof app.errors.phone_whatsapp !== 'undefined') {
                        document.getElementById("phone_whatsapp").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    if (typeof app.errors.address !== 'undefined') {
                        document.getElementById("address").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                });
            }
        },

        mounted() {
            //this.district = '5';
        }
    }
</script>

<style scoped>
    .panel {
        font-size: 130%;
    }
    body {
        margin: 0;
    }
    #text {
        height: 25vh;
    }
    .help-block{
        color: red;
    }
</style>