<template>
    <div>
        <h2>Войдите в систему</h2>
        <div class="alert alert-danger" v-if="error">
            <p class="big-text">{{ error_msg }}</p>
        </div>
        <form autocomplete="off" @submit.prevent="login" method="post">
            <div class="form-group">
                <label for="phone_call">Номер телефона</label>
                <input type="text" id="phone_call" class="form-control" placeholder="+7 924 123-45-67" v-model="phone_call" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" class="form-control" v-model="password" required>
            </div>
            <button type="submit" class="btn btn-default">Войти</button>
        </form>
    </div>
</template>

<script>
    const bcrypt = require('bcryptjs');

    export default {
        name: "login",
        data(){
            return {
                phone_call: null,
                password: null,
                error: false
            }
        },
        methods: {
            login(){
                var app = this;
                var salt = bcrypt.genSaltSync(12);
                var hash = bcrypt.hashSync(this.password, salt);
                this.$auth.login({
                    params: {
                        phone_call: app.phone_call,
                        password: app.password
                    },
                    success: function () {
                        app.$router.push({name: 'applications_with_param', params: {date: 'actual'}});
                        location.reload();
                    },
                    error: function (resp) {
                        app.error = true;
                        app.error_msg = resp.response.data.status;
                    },
                    rememberMe: true,
                    //redirect: '/apps',
                    fetchUser: true,
                });
            },
        }
    }
</script>

<style scoped>
  .big-text {
    font-size: 150%;
  }
</style>