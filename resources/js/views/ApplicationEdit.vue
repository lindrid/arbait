<template>
    <div>
        <div class="form-group">
            <router-link to="/" class="btn btn-default">Back</router-link>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Редактировать</div>
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
                            <button class="btn btn-success">Применить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            let app = this;
            let id = app.$route.params.id;
            app.applicationId = id;
            this.$axios.get('/v1/companies/' + id)
                .then(function (resp) {
                    app.application = resp.data;
                })
                .catch(function () {
                    alert("Не удалось загрузить")
                });
        },
        data: function () {
            return {
                applicationId: null,
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
                this.$axios.patch('/v1/companies/' + app.applicationId, newApplication)
                    .then(function (resp) {
                        app.$router.replace('/admin');
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось создать");
                    });
            }
        }
    }
</script>


<style scoped>

</style>