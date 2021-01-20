<template>

    <div class="myclass container">
        <div id="main_err" name="main_err">
            <div class="alert alert-danger" v-if="error && !success">
                <div v-if="error && errors.main">
                    <p>{{errors.main}}</p>
                </div>
                <div v-else>
                    <div v-if="error && (errors.fullname || errors.pass_series_number || errors.birth_date)">
                        <p>Ошибка в заполнении данных, либо ваш паспорт недействителен</p>
                    </div>
                    <div v-else>
                        <p>Ошибка в заполнении данных</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-success" v-if="success">
            <p>Данные сохранены в базе. Переходим к шагу 2 регистрации</p>
        </div>

        <form autocomplete="off" @submit.prevent="register" v-if="!success" method="post">

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.fullname }">
                <label for="fullname">Фамилия Имя Отчество</label>
                <input type="text" id="fullname" name="fullname" class="form-control" v-model="fullname" required>
                <span class="help-block" v-if="error && errors.fullname">{{ errors.fullname }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.phone_call }">
                <label for="phone_call">Телефон для связи (мобильный)</label>
                <div class="input-group">
                    <span class="input-group-addon"><span>+7</span></span>
                    <input type="tel"
                           v-model="phone_call"
                           name="phone_call"
                           id="phone_call"
                           placeholder="924 555-55-55"
                           v-mask="'999 999-99-99'"
                           autocomplete="tel"
                           maxlength="15"
                           class="form-control"
                           required
                    />
                </div>
                <span class="help-block" id="phone_call_help_block"
                      v-if="error && errors.phone_call">{{ errors.phone_call }}
                </span>
            </div>

            <div class="form-group" id="form-group-pc" v-bind:class="{ 'has-error': error && errors.phone_whatsapp }">
                <label for="phone_whatsapp">Телефон в Whatsapp</label>
                <button id="same_btn" type=button class="btn btn-default"
                        @click="phone_whatsapp = '+7 ' + phone_call">Тот же</button>
                <vue-tel-input
                        id="phone_whatsapp"
                        class="form-control"
                        v-model="phone_whatsapp"
                        required
                        placeholder="Введите номер"
                        autocomplete="on"
                        v-on:onValidate="pw_valid=$event.isValid"
                ></vue-tel-input>
                <span class="help-block" v-if="error && errors.phone_whatsapp">{{ errors.phone_whatsapp }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.pass_series_number }">
                <label for="pass_series_number">Серия и номер паспорта</label>
                <input type="text"
                       id="pass_series_number"
                       class="form-control"
                       v-model="pass_series_number"
                       required
                       v-mask="'9999 999999'"
                >
                <span class="help-block" v-if="error && errors.pass_series_number">{{ errors.pass_series_number }}</span>
                <span class="help-block" v-if="error && error_same_passport">
                    Если это были не вы, обратитесь к администратору.
                    Если же вы уже регистрировались, <router-link :to="{name:'login'}">войдите</router-link>
                </span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.birth_date }">
                <label for="birth_date">Дата рождения</label>
                <input type="text"
                       id="birth_date"
                       class="form-control"
                       v-model="birth_date"
                       required
                       v-mask="'99.99.9999'"
                >
                <span class="help-block" v-if="error && errors.birth_date">{{ errors.birth_date }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.pass_date }">
                <label for="pass_date">Дата выдачи паспорта</label>
                <input type="text"
                       id="pass_date"
                       class="form-control"
                       v-model="pass_date"
                       required
                       v-mask="'99.99.9999'"
                >
                <span class="help-block" v-if="error && errors.pass_date">{{ errors.pass_date }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.pass_code }">
                <label for="pass_code">Код подразделения</label>
                <input type="text"
                       id="pass_code"
                       class="form-control"
                       v-model="pass_code"
                       required
                       v-mask="'999-999'"
                >
                <span class="help-block" v-if="error && errors.pass_code">{{ errors.pass_code }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.address }">
                <label for="address">Фактический адрес проживания</label>
                <input type="text" id="address" class="form-control" v-model="address" required>
                <span class="help-block" v-if="error && errors.address">{{ errors.address }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.password }">
                <label for="password">Пароль</label>
                <vue-password
                        id="password"
                        v-model="password"
                        required
                        minlength="4"
                        disableStrength
                ></vue-password>
                <span class="help-block" v-if="error && errors.password">{{ errors.password }}</span>
            </div>

            <button id="proceed" type="submit" class="btn btn-default">Продолжить</button>
        </form>
    </div>
</template>


<script>
    import VueTelInput from 'vue-tel-input';
    import VuePassword from 'vue-password';

    const bcrypt = require('bcryptjs');
    const Cookies = require('js-cookie');

    export default {
        name: "register",

        components: {
            VueTelInput,
            VuePassword,
        },

        data(){
            return {
                fullname: '',
                phone_call: '',
                phone_whatsapp: '',
                pass_series_number: '',
                pass_scan: '',
                address: '',
                birth_date:'',
                pass_date:'',
                pass_code:'',
                password: '',
                user_type: '',
                saltRounds: 10,

                pw_valid: false,
                error_same_passport: false,
                error: false,
                errors: {},
                success: false,

                bindProps: {
                    defaultCountry: "ru",
                    disabledFetchingCountry: false,
                    disabled: false,
                    disabledFormatting: false,
                    placeholder: "Введите номер",
                    required: false,
                    enabledCountryCode: false,
                    enabledFlags: true,
                    preferredCountries: [],
                    onlyCountries: [],
                    ignoredCountries: [],
                    autocomplete: "off",
                    name: "Телефон",
                    maxLen: 18,
                    wrapperClasses: "",
                    inputClasses: "",
                    dropdownOptions: {
                        disabledDialCode: false
                    },
                    inputOptions: {
                        showDialCode: false
                    }
                }
            };
        },
        methods: {
            register() {
                var app = this;

                if (this.error == true) {
                    this.error = false;
                    this.errors.fullname = undefined;
                    this.errors.phone_call = undefined;
                    this.errors.phone_whatsapp = undefined;
                    this.errors.pass_series_number = undefined;
                    this.errors.birth_date = undefined;
                    this.errors.pass_date = undefined;
                    this.errors.pass_code = undefined;
                    this.errors.address = undefined;
                    this.errors.password = undefined;
                    this.errors.passport = undefined;
                }
                var fullname_RE = /^[а-яА-ЯёЁ\-\—\s]+$/,
                    phone_call_RE = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/,
                    pass_series_number_RE = /^\d{4} \d{6}$/,
                    birth_date_RE = /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/,
                    pass_date_RE = /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/,
                    pass_code_RE = /^\d{3}-\d{3}$/,
                    address_RE = /^[а-яА-ЯёЁ0-9\.\,\/\-\—\s]+$/,
                    password_RE = /[^A-Za-z0-9]/;

                this.phone_call = '+7 ' + this.phone_call;

                if (!fullname_RE.test(this.fullname)) {
                    this.error = true;
                    this.errors.fullname = 'Недопустимые символы, ФИО может состоять только из букв русского ' +
                        'алфавита, пробелов и дефисов(‐)';
                }

                if (!phone_call_RE.test(this.phone_call)) {
                    this.error = true;
                    this.errors.phone_call = 'Неверно указан номер телефона для звонков. ' +
                        'Возможно у вас лишние символы, либо слишком мало/много цифр';
                }
                if (this.pw_valid == false) {
                    this.error = true;
                    this.errors.phone_whatsapp = 'Неверно указан номер телефона в whatsapp. ' +
                        'Возможно у вас лишние символы, либо слишком мало/много цифр';
                }
                if (!pass_series_number_RE.test(this.pass_series_number)) {
                    this.error = true;
                    this.errors.pass_series_number = 'Серия и номер паспорта это 4 цифры, ' +
                        'пробел, затем 6 цифр. У вас иначе';
                }
                if (!birth_date_RE.test(this.birth_date)) {
                    this.error = true;
                    this.errors.birth_date = 'Неверный формат даты рождения. Пример правильной ' +
                        'даты 03.05.1997';
                }
                if (!pass_date_RE.test(this.pass_date)) {
                    this.error = true;
                    this.errors.pass_date = 'Неверный формат даты выдачи паспорта. Пример правильной ' +
                        'даты 03.05.2007';
                }
                if (!pass_code_RE.test(this.pass_code)) {
                    this.error = true;
                    this.errors.pass_code = 'Неверный формат кода подразделения. Пример правильного ' +
                        'кода 230-003';
                }

                if (!address_RE.test(this.address)) {
                    this.error = true;
                    this.errors.address = 'Вы использовали недопустимые символы в адресе. ' +
                        'Можно только русские буквы, цифры, пробел и . , - / \\ ';
                }
                if (password_RE.test(this.password)) {
                    this.error = true;
                    this.errors.password = 'Недопустимые символы в пароле. Могут быть только английские буквы и цифры';
                }

                var salt = bcrypt.genSaltSync(12);
                var hash = bcrypt.hashSync(this.password, salt);

                if (this.error == false) {
                    this.$auth.register({
                        data: {
                            fullname: app.fullname,
                            phone_call: app.phone_call,
                            phone_whatsapp: app.phone_whatsapp,
                            pass_series_number: app.pass_series_number,
                            birth_date: app.birth_date,
                            pass_date: app.pass_date,
                            pass_code: app.pass_code,
                            address: app.address,
                            password: app.password,
                            user_type: app.user_type,
                            page: 'register'
                        },
                        success: function () {
                            app.success = true;

                            this.$auth.login({
                                params: {
                                    phone_call: '+7 ' + app.phone_call,
                                    password: hash
                                },
                                success: function () {
                                },
                                error: function () {
                                },
                                rememberMe: true,
                                redirect: '/verify/' + this.user_type + '/' +
                                '+7 ' + app.phone_call,
                                fetchUser: true,
                            });
                        },
                        error: function (resp) {
                            app.error = true;
                            app.errors = resp.response.data.errors;
                            if (app.errors.pass_series_number !== undefined) {
                                app.error_same_passport = resp.response.data.error_same_passport;
                            }
                            this.scrollToErrorEl();
                        },
                        redirect: null
                    });
                }
                else {
                    this.scrollToErrorEl();
                }
                this.phone_call = this.phone_call.substring(3);
            },

            scrollToErrorEl() {
                if (typeof this.errors.fullname !== 'undefined') {
                    console.log('fullname');
                    document.getElementById("fullname").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.phone_call !== 'undefined') {
                    console.log('phone_call');
                    document.getElementById("phone_call").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.phone_whatsapp !== 'undefined') {
                    console.log('phone_whatsapp');
                    document.getElementById("phone_whatsapp").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.pass_series_number !== 'undefined') {
                    console.log('pass_series_number');
                    document.getElementById("pass_series_number").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.birth_date !== 'undefined') {
                    console.log('birth_date');
                    document.getElementById("birth_date").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.pass_date !== 'undefined') {
                    console.log('pass_date');
                    document.getElementById("pass_date").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.pass_code !== 'undefined') {
                    console.log('pass_code');
                    document.getElementById("pass_code").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.address !== 'undefined') {
                    console.log('address');
                    document.getElementById("address").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.password !== 'undefined') {
                    console.log('password');
                    document.getElementById("password").scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.main !== 'undefined') {
                    console.log('main');
                    document.getElementById("main_err").scrollIntoView({block: "center", behavior: "smooth"});
                }
            }

        },



        mounted(){
            var app = this;

            this.user_type = this.$route.params.user_type;

            app.fullname = 'Федоров Дмитрий Сергеевич';
            app.phone_call = '951 000-22-34';
            app.phone_whatsapp = '+7 951 000 22 34';
            app.pass_series_number = '0507 372739';
            app.birth_date = '08.06.1987';
            app.pass_date = '23.07.2007';
            app.pass_code = '250-006';
            app.address = "Кирова 68";


            /* подгружаем и активируем скрипт с подсказками*/
            const script = document.createElement('script');
            script.onload = () => {
                $('#fullname').suggestions({
                    token: "e82c9349c51b999d64e0495fa5f191ba88ac4a77",
                    type: "NAME",
                    /* Вызывается, когда пользователь выбирает одну из подсказок */
                    onSelect: function(suggestion) {
                        app.fullname = suggestion.value;
                    }
                });
                $('#address').suggestions({
                    token: "e82c9349c51b999d64e0495fa5f191ba88ac4a77",
                    type: "ADDRESS",
                    constraints: {
                        label: "Владивосток",
                        // ограничиваем поиск Владивостоком
                        locations: {
                            city: "Владивосток"
                        },
                        // НЕ даем пользователю возможность снять ограничение
                        deletable: false
                    },
                    // в списке подсказок не показываем область и город
                    restrict_value: true,
                    /* Вызывается, когда пользователь выбирает одну из подсказок */
                    onSelect: function(suggestion) {
                        app.address = suggestion.value.substring(15);
                    }
                });
            }
            script.src = '//cdn.jsdelivr.net/npm/suggestions-jquery@19.4.2/dist/js/jquery.suggestions.min.js';
            document.head.appendChild(script);
        }
    }
</script>

<style scoped>
    .myclass {
        font-size: 130%;
    }

    #foreign_pass_series_number {
        width: 170px;
    }
    #pass_date,#birth_date {
         width: 130px;
    }
    #who_give {
        width: 100px;
    }
    input[type="text"]
    {
        font-size: 110%;
    }
    input[type="tel"]
    {
        font-size: 110%;
        width:180px;
    }
    .vue-tel-input
    {
        font-size: 110%;
        width:280px;
    }
    #password {
        width: 150px;
    }
    #suggestions-constraints, .suggestions-constraints {
        width: 0px;
    }


    .container {
        padding: 40px 5% 15px 5%;
        background-color: #fff;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .input-group-addon {
        font-size: 110%;
        height: 33px;
    }

    #same_btn {
        font-size: 110%;
    }

    .help-block {
        display: block;
    }
</style>

<style lang="scss">
    @import '../assets/css/suggestions.min.css';
    #proceed {
        display: block;
        text-align: center;
        font-size: 130%;
        margin: auto
    }
</style>