<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Список заявок за {{date}}</div>
            <div class="panel-body">
                <p>Количество картинок : <b>{{pic_count}} шт</b></p>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Картинка</th>
                        <th>Сумма</th>
                        <th width="100">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(pic, index) in income_pictures">
                        <td>{{ pic.text }}</td>
                        <td>{{ pic.money_amount }}</td>
                        <td>
                            <router-link :to="{name: 'accEditIncome', params: {id: pic.id}}"
                                         class="btn btn-xs btn-default">
                                Реактировать
                            </router-link>
                            <a href="#"
                               class="btn btn-xs btn-danger"
                               v-on:click="deleteEntry(pic.id, index)">
                                Удалить
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <router-link :to="{name: component_name, params: component_params}" class="btn btn-default">
                Назад
            </router-link>
        </div>

    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                income_pictures: [],
                date: '',
                component_name: 'accountancyInstagram',
                component_params: {public_name: 'rabota'},
                pic_count: 0
                //prevRoute: ''
            }
        },
        mounted() {
            this.date = this.$route.params.date;
            var app = this;

            if (typeof this.date === 'undefined') {
                this.$axios.get('/accountancy/instagram/index')
                    .then(function (resp) {
                        console.log(resp);
                        app.income_pictures = resp.data.pics;
                        app.date = resp.data.date;
                        app.pic_count = resp.data.pic_count;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            }
            else {
                app.component_name = 'accountancyInstagram';
                app.component_params = {public_name: 'rabota'};
                this.$axios.get('/accountancy/instagram/index/' + app.date)
                    .then(function (resp) {
                        console.log(resp);
                        app.income_pictures = resp.data.pics;
                        app.date = resp.data.date;
                        app.pic_count = resp.data.pic_count;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            }
        },
        methods: {
            deleteEntry(id, index) {
                if (confirm("Вы действительно хотите удалить?")) {
                    var app = this;
                    this.$axios.delete('/accountancy/instagram/pic/delete/' + id)
                        .then(function (resp) {
                            app.income_pictures.splice(index, 1);
                        })
                        .catch(function (resp) {
                            alert("Не удалось удалить");

                        });
                }
            }
        }
    }
</script>
