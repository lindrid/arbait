<template>
    <div id="app">
        <section class="select-skills">
            <div class="skills-header" style="text-align: center">
                Укажите сколько времени работали
            </div>

            <div class="worker-skills" v-for="occupation in occupations">

                <div class="message" v-bind:class="{ 'is-primary': 1, 'is-closed': 0 }"
                >

                    <div class="category message-header">
                        {{occupation.name}}
                    </div>

                    <div class="message-body">
                        <radio v-bind:name="radiobtn_class_names[occupation.id]"
                               value="0" v-model="radiobtn_values[occupation.id]"
                        >
                            Меньше года
                        </radio>
                        <radio v-bind:name="radiobtn_class_names[occupation.id]"
                               value="1" v-model="radiobtn_values[occupation.id]"
                        >
                            От 1 до 2ух лет
                        </radio>
                        <radio v-bind:name="radiobtn_class_names[occupation.id]"
                               value="2" v-model="radiobtn_values[occupation.id]"
                        >
                            От 2ух и более лет
                        </radio>
                    </div>

                </div>

            </div>
        </section>

        <br>
        <div style="padding-left:44.8%">
            <router-link to="/confirm-worker" class="btn btn-default">Назад</router-link>
            <button class="btn btn-success" v-on:click="saveUserOccupationsXpTime">Сохранить</button>
            <button id="bla" class="btn btn-success" v-on:click="verInnPass">ИннПаспорт</button>
        </div>
    </div>
</template>

<script>
    import route from '../route';


    export default {
        name: "confirm-worker-occ-xp-time",

        data() {
            return {
                occupations: [],
                radiobtn_class_names: [],
                radiobtn_values: [],
                user_id: null,
                response: null
            }
        },

        computed: {},

        methods: {
            getData: function () {
                this.user_id = this.$route.params.id;
                this.$axios.get(route('confirm-worker-xp.show'))
                    .then(responce => {
                        this.occupations = responce.data.occupations;
                        this.radiobtn_class_names = responce.data.radiobtn_class_names;
                        this.radiobtn_values = responce.data.radiobtn_values;
                    })
                    .catch(function () {
                        alert("Не удалось загрузить")
                    });
            },

            saveUserOccupationsXpTime() {
                var app = this;
                this.$axios.post(route('confirm-worker-xp.store'),
                    {
                        radiobtn_values: this.radiobtn_values,
                        user_id: this.user_id
                    }
                ).then(function (resp) {
                    app.$router.push({
                        name: 'home',
                    });
                }).catch(function (resp) {
                    console.log(resp);
                    alert("Не удалось создать");
                });
            },

            verInnPass: function () {
                this.$axios.get('/verify_inn_pass')
                    .then(response => {
                        this.response = response.data.response;
                    })
                    .catch(function () {
                        alert("Не удалось загрузить")
                    });
                console.log(this.response);
            }

        },

        mounted() {
            this.getData();
        }
    }
</script>

<style>
    .checkbox-component > input + label > .input-box,
    .radio-component > input + label > .input-box {
        font-size: 1.2em;
        text-align: center;
        line-height: 1;
        color: transparent;
    }
    .checkbox-component > input + label > .input-box > .input-box-tick,
    .radio-component > input + label > .input-box > .input-box-circle {
        display: none;
    }
    .checkbox-component > input + label > .input-box:before,
    .radio-component > input + label > .input-box:before {
        content: '✔';
    }
    .checkbox-component > input:checked + label > .input-box:before,
    .radio-component > input:checked + label > .input-box:before {
        color: #000;
    }
</style>