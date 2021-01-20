<template>
    <section id="home">
        Главная страница
       <!-- <label for="example-input">Время выполнения услуги</label>
        <b-input-group class="mb-5">
            <b-form-input
                    id="example-input"
                    v-model="value"
                    type="text"
            ></b-form-input>
            <b-input-group-append>
                <b-form-timepicker
                        v-model="value"
                        button-only
                        right
                        locale="en"
                        aria-controls="example-input"
                ></b-form-timepicker>
            </b-input-group-append>
        </b-input-group>
     <!--       <h1>Вы:</h1>

            <button class="btn btn-default" type="submit">
                <router-link :to="{ name: 'personal-data', params: { user_type: 'd' }  }">Диспетчер</router-link>
            </button>

            <button class="btn btn-default" type="submit">
                <router-link :to="{ name: 'personal-data', params: { user_type: 'w' } }">Рабочий</router-link>
            </button>
<!--
            <button class="btn btn-default" type="submit">
                <router-link :to="{ name: 'citizen' }">CITIZENь</router-link>
            </button>
            <button class="btn btn-default" type="submit">
                <router-link :to="{ name: 'register-worker' }">regwork</router-link>
            </button>
            <button class="btn btn-default" type="submit">
                <router-link :to="{ name: 'verify', params: {phone: '+79510002234'} }">verify</router-link>
            </button>
            <button class="btn btn-default" type="submit">
                <router-link :to="{ name: 'dashboard' }">Личный кабинет</router-link>
            </button>
            <br>
            <button v-if="!$auth.check()" class="btn btn-default" type="submit">
                <router-link :to="{ name: 'login' }">Войти в систему</router-link>
            </button>
            <br>
            <button v-if="$auth.check()" class="btn btn-default" type="submit">
                <a href="#" @click.prevent="$auth.logout()">Выйти</a>
            </button>
            <button v-if="$auth.check()" class="btn btn-default" type="submit">
                <router-link :to="{ name: 'verify' }">Подтвердить номер</router-link>
            </button>!-->
    </section>
</template>

<script>
    export default {
        name: "home",
        components: {
        },
        data: function ()
        {
            return {
                choosen_service: undefined,
                value: '',
                time: new Date()
            }
        },
        methods: {
            is_active(col_number) {
                if (this.choosen_service == col_number) {
                    return true;
                }
            }
        }
    }
</script>

<style scoped>

   /* .col-centered{
        float: none;
        margin: 0 auto;
    }

    .vcenter {
        display: inline-block;
        vertical-align: middle;
        float: none;
    }
*/
   /*.col-container {
       display: flex;
       width: 100%;
   }*/

   /*@media only screen and (max-width: 600px) {
       .col {
           display: block;
           width: 50%;
       }
   }*/
  /* @media (min-width: 768px) {
       .row.equal {
           display: flex;
           flex-wrap: wrap;
       }
   }
   .equal {
       display: flex;
       display: -webkit-flex;
       flex-wrap: wrap;
   }*/

   .text-large {
       font-size: 200%;
   }
/*
   a.list-group-item {
       overflow: hidden;
       display: table;
   }

   a.list-group-item > div {
       height: 100%;
       display: table-cell;
       vertical-align: bottom;
       float:none;
   }

    .list-group-item.margin {
        height: 100%;
        margin-bottom: 1px;
    }

    .list-group {
        height: 100%;
    }*/
   .list-group-item {
       position: relative;
       display: block;
       padding: 0px 0px;
       margin-bottom: -1px;
       border: 1px solid #ddd;
       vertical-align: center;
   }

   /*@media only screen and (max-width: 400px) {
       .list-group-item {
           height: 16vh;
       }
   }*/

    .btn {
        font-size: 130%;
    }

    .center2 {
        width: 200px;
        font-size: 170%;
        position: fixed;
        top: 32%;
        left: 20%;
        transfrom: translateY(-32%);
        transfrom: translateX(-20%);
    }

    .center4 {
        width: 200px;
        font-size: 170%;
        position: fixed;
        top: 62%;
        left: 20%;
        transfrom: translateY(-62%);
        transfrom: translateX(-20%);
    }
</style>