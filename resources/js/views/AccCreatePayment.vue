<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Переводы</div>
            <div class="panel-body">
                <form v-on:submit="saveForm()">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Текст из чата</label>
                            <textarea id="text" v-model="text" class="form-control">
                            </textarea>
                            <span class="help-block" v-if="error && errors.text">{{ errors.text }}</span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <button class="btn btn-success">Сохранить</button>

                        </div>
                    </div>

                    <div class="alert alert-success" v-if="success">
                        <div v-for="(string, index) in strings">
                            {{ index }} => {{ string }}  <br>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <a id="myLink" href="#" @click="focusTab()">MTS Bank</a>
        <button class="btn btn-default" @click="log()">Console</button>

        <div class="alert alert-danger" v-if="error">
            <div v-if="error">
                <div v-for="(err, index) in errors">
                    <p>Строка {{index+1}}. {{err.message}}</p>
                </div>
                <div v-for="err in apps_errors">
                    <p>Заявка {{err.address}}. {{err.message}}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "acc-create-payment",
        data: function () {
            return {
                text: '',
                strings: {},
                success: false,
                mtsTab: false,
                error: false,
                errors: {},
                apps_errors: {}
            }
        },
        methods: {
            log() {
                this.mtsTab.focus();
            },
            focusTab() {
                this.mtsTab = window.open("https://online.mtsbank.ru/webmvc/", "_blank") ;
                this.mtsTab.focus();
            },
            saveForm() {
                event.preventDefault();
                var app = this;

                if (this.error == true) {
                    this.errors.text = undefined;
                }

                this.$axios.post('/accountancy/payment/add',
                    {
                        text: this.text,
                    }
                ).then(response => {
                    if (response.status == 200) {
                        this.strings = response.data.strings;
                        var date = response.data.date;
                        app.success = true;
                        app.error = false;

                        app.$router.push({
                            name: 'acc-payments',
                            params: {date: date}
                        });
                    }
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    app.apps_errors = error.response.data.apps_errors;
                    console.log(app.errors);
                    console.log(app.apps_errors);
                });
            }
        },
        mounted () {

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