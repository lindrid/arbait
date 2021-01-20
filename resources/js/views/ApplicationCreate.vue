<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Создать заявку</div>
            <div class="panel-body">
                <form v-on:submit="saveForm()">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Услуга</label>
                            <input type="text" v-model="application.name" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Адрес</label>
                            <input type="text" v-model="application.address" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label class="control-label">Примечание</label>
                            <input type="text" v-model="application.misc" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <button class="btn btn-success">Создать</button>
                            <router-link to="/service" class="btn btn-default">Назад</router-link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                application: {
                    name: '',
                    user_name: '',
                    address: '',
                    misc: '',
                }
            }
        },
        methods: {
            saveForm() {
                event.preventDefault();
                var app = this;
                var newApplication = app.application;
                this.$axios.post('/v1/companies', newApplication)
                    .then(function (resp) {
                        app.$router.push({path: '/dashboard'}); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось создать");
                    });
            }
        }
    }
</script>