<template>

    <div class="myclass container">
        <div id="main_err" name="main_err">
            <div class="alert alert-danger" v-if="error && !success">
                <div v-if="error && errors.main">
                    <p>{{errors.main}}</p>
                </div>
                <div v-else>
                    <p>Ошибка в заполнении данных</p>
                </div>
            </div>
        </div>

        <div class="alert alert-success" v-if="success">
            <p>Данные сохранены в базе. Переходим к шагу 2 регистрации</p>
        </div>

        <form autocomplete="off" @submit.prevent="register" v-if="!success" method="post">

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.fullname }">
                <label for="fullname">Фамилия Имя Отчество(если есть)</label>
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
                      v-if="error && errors.phone_call">{{ errors.phone_call }}</span>
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

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.foreign_pass_series_number }">
                <label for="foreign_pass_series_number">Серия и номер паспорта иностранного гражданина</label>
                <input type="text"
                       id="foreign_pass_series_number"
                       class="form-control"
                       v-model="foreign_pass_series_number"
                       required
                >
                <span class="help-block" v-if="error && errors.foreign_pass_series_number">{{ errors.foreign_pass_series_number }}</span>
                <span class="help-block" v-if="error && error_same_passport">
                    Если это были не вы, обратитесь к администратору.
                    Если же вы уже регистрировались, тогда <router-link :to="{name:'login'}">войдите</router-link>
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
                <label for="pass_date">Дата выдачи документа</label>
                <input type="text"
                       id="pass_date"
                       class="form-control"
                       v-model="pass_date"
                       required
                       v-mask="'99.99.9999'"
                >
                <span class="help-block" v-if="error && errors.pass_date">{{ errors.pass_date }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.who_give }">
                <label for="who_give">Кем выдан документ</label>
                <input type="text"
                       id="who_give"
                       class="form-control"
                       v-model="who_give"
                       required
                >
                <span class="help-block" v-if="error && errors.who_give">{{ errors.who_give }}</span>
            </div>

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.address }">
                <label for="address">Фактический адрес проживания (где вы сейчас живете)</label>
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

            <div class="form-group" v-bind:class="{ 'has-error': error && errors.passport }">
                <label for="passport_image">Загрузите фото паспорта</label>
                <input type="file" id="passport_image" ref="file" v-on:change="onChangeFileUpload()"/>
                <span class="help-block" v-if="error && errors.passport">{{ errors.passport }}</span>
            </div>

         <button type="submit" class="btn btn-default">Продолжить</button>
        </form>
    </div>
</template>

<!--
Вид документа:
паспорт иностранного гражданина,
вид на жительство
свидетельство о лице без гражданства, беженца // свидетельство о беженстве (беженство)
гражданство РФ
Регистрация временного пребывания (РВП)
!-->

<!--
Серия для РВП 2 цыфры + пробел + 2 цифры, для ВНЖ 2 цифры, паспорт узб 2 цифры серия
номер документа -- паспорт узб 7 цифр номер, таджикистана 9 цифр
Дата выдачи документа -- стандартная дата
Кем выдан документ -- это длинное поле
Срок действия для РВП и Вида на жительство

* Для верификации предоставленных данных требуется скан-копия документа.
!-->

<script>
    import VueTelInput from 'vue-tel-input';
    import VuePassword from 'vue-password';

    const bcrypt = require('bcryptjs');

    export default {
        components: {
            VueTelInput,
            VuePassword
        },
        name: "register-alien-worker",
        data(){
            return {
                fullname: '',
                phone_call: '',
                phone_whatsapp: '',
                foreign_pass_series_number: '',
                pass_scan: '',
                address: '',
                birth_date:'',
                pass_date:'',
                who_give:'',
                password: '',
                type: 'w',
                passport_image: '',
                hasImage: false,

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
            onChangeFileUpload(){
                this.passport_image = this.$refs.file.files[0];
            },

            register(){
                var app = this;

                if (this.error == true) {
                    this.error = false;
                    this.errors.fullname = undefined;
                    this.errors.phone_call = undefined;
                    this.errors.phone_whatsapp = undefined;
                    this.errors.foreign_pass_series_number = undefined;
                    this.errors.birth_date = undefined;
                    this.errors.pass_date = undefined;
                    this.errors.who_give = undefined;
                    this.errors.address = undefined;
                    this.errors.password = undefined;
                    this.errors.passport_image = undefined;
                }
                var fullname_RE = /^[а-яА-ЯёЁ\-\—\s]+$/,
                    phone_call_RE = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/,
                    birth_date_RE = /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/,
                    pass_date_RE = /^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.](19|20)\d\d$/,
                    address_RE = /^[а-яА-ЯёЁ0-9\.\,\/\-\—\s]+$/,
                    password_RE = /[^A-Za-z0-9]/;


                if (!fullname_RE.test(this.fullname)) {
                    this.error = true;
                    this.errors.fullname = 'Недопустимые символы, ФИО может состоять только из букв русского ' +
                        'алфавита, пробелов и дефисов(‐)';
                }
                this.phone_call = '+7 ' + this.phone_call;
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
                    let formData = new FormData();
                    formData.append('passport_image', this.passport_image);
                    formData.append('phone_call', this.phone_call);

                    //console.log(this.passport_image);
                    this.$axios.post( '/upload-img',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).
                    then(function(){
                        console.log('SUCCESS!!');
                        this.$auth.register({
                            data: {
                                fullname            : app.fullname,
                                phone_call          : app.phone_call,
                                phone_whatsapp      : app.phone_whatsapp,
                                foreign_pass_series_number  : app.foreign_pass_series_number,
                                birth_date          : app.birth_date,
                                pass_date           : app.pass_date,
                                who_give            : app.who_give,
                                address             : app.address,
                                password            : hash,
                                type                : app.type,
                                page                : 'register-aworker',
                            },

                            success: function () {
                                app.success = true;
                                //app.$router.push({name: 'verify'});
                            },
                            error: function (resp) {
                                app.error = true;
                                app.errors = resp.response.data.errors;
                                if (app.errors.foreign_pass_series_number !== undefined) {
                                    app.error_same_passport = resp.response.data.error_same_passport;
                                }
                                this.scrollToErrorEl();
                            },
                            redirect: null
                        });
                    }).
                    catch(function(){
                        console.log('FAILURE!!');
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
                    document.getElementById("fullname").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.phone_call !== 'undefined') {
                    console.log('phone_call');
                    document.getElementById("phone_call").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.phone_whatsapp !== 'undefined') {
                    console.log('phone_whatsapp');
                    document.getElementById("phone_whatsapp").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.foreign_pass_series_number !== 'undefined') {
                    console.log('pass_series_number');
                    document.getElementById("foreign_pass_series_number").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.birth_date !== 'undefined') {
                    console.log('birth_date');
                    document.getElementById("birth_date").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.pass_date !== 'undefined') {
                    console.log('pass_date');
                    document.getElementById("pass_date").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.who_give !== 'undefined') {
                    console.log('pass_code');
                    document.getElementById("who_give").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.address !== 'undefined') {
                    console.log('address');
                    document.getElementById("address").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.password !== 'undefined') {
                    console.log('password');
                    document.getElementById("password").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.passport_image !== 'undefined') {
                    console.log('passport_image');
                    document.getElementById("passport_image").
                    scrollIntoView({block: "center", behavior: "smooth"});
                }
                else if (typeof this.errors.main !== 'undefined') {
                    console.log('main');
                    document.getElementById("main_err").
                        scrollIntoView({block: "center", behavior: "smooth"});
                }
            },
        },


        mounted(){
            var app = this;

            app.fullname = 'Шербек Иглеевич Оглы';
            app.phone_call = '914 123-33-54';
            app.phone_whatsapp = '+998 99 823 44 34';
            app.foreign_pass_series_number = 'AB 12412421';
            app.birth_date = '08.06.1985';
            app.pass_date = '23.05.2017';
            app.who_give = 'Правительство Узбекистана';
            app.address = "ул Кубанская, д 10";
            app.password = '1234';

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
</style>