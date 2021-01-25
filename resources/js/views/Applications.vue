<template>
    <div>
      <div v-if="error" class="alert alert-danger" >
        <div class="bs">
          <div class="container">
            <div class="row">
                <p class="big-text">{{ error_msg }}</p>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="panel panel-default">
            <div v-if="app_type === 'account'" class="panel-heading">
              Список заявок с рассчетного счета
            </div>

            <div class="panel-body">
              <p>Количество заявок : <b>{{app_count}}</b></p>

              <div class="input-group">
                <router-link :to="{ name: 'applicationCreate' }"
                             class="btn btn-default margin-r-b"
                             :class="{ disabled: !userCan('CreateApplications') }"
                >
                    Создать заявку
                </router-link>

                <button v-if="app_type == 'account'"
                        class="btn btn-default  margin-r-b"
                        @click="$router.push({ name: 'applications_with_page',
                            params: {page: 1} }); locationReload()"
                >
                  Обычные заявки
                </button>

                <button v-else
                  class="btn btn-default  margin-r-b"
                  @click="$router.push({ name: 'account_applications_with_page',
                    params:  {page: 1} }); locationReload()"
                >
                  Заявки на РС
                </button>
              </div>

              <div>
                <router-link v-if="userCan('WatchMoversReport')"
                             :to="{ name: 'accountancy' }" class="btn btn-default margin-r-b">
                  Отчет
                </router-link>

                <router-link v-if="userCan('WatchInstaReport')"
                             :to="{ name: 'accountancyInstagram' }" class="btn btn-default margin-r-b">
                  Отчет по Instagram
                </router-link>
              </div>

              <div>
                <button v-bind:disabled="(page == 1)"
                        class="btn btn-default"
                        @click="$router.push({ name: 'applications_with_page',
                          params:  {page: page - 1} }); loadData()">
                  Вперед
                </button>

                <button class="btn btn-default"
                        @click="$router.push({ name: 'applications_with_page',
                          params:  {page: Number(page) + 1} }); loadData()">
                  Назад
                </button>
              </div>

                <div>
                    <vue-good-table
                        :columns="columns"
                        :rows="rows"
                        :search-options="{
                            enabled: true
                        }"
                        :sort-options="{
                            enabled: true,
                            initialSortBy: [
                                {field: 'date', type: 'desc'},
                                {field: 'state', type: 'asc'},
                                {field: 'time' , type: 'asc'}
                            ]
                        }"
                        :groupOptions="{
  	                        enabled: true
                        }"
                        @on-cell-click="onCellClick"
                    >
                        <template slot="table-row" slot-scope="props">

                            <span v-if="props.column.field == 'copy'">
                                <button
                                        type="button"
                                        @click="copyToClipboard(props.row.composedAppText)">
                                    Ко
                                </button>
                            </span>

                            <span v-else-if="props.column.field == 'delete'">
                              <button
                                      v-bind:disabled="!userCan('DeleteApplications')"
                                      @click="deleteEntry(props.row.id, props.row.index)"
                              >
                                  Del
                              </button>
                            </span>

                            <span v-else>
                              <span v-if="props.column.field == 'address'" style="cursor: pointer">
                                <span

                                     v-bind:style="{
                                        color: state_colors[props.row.state],
                                        fontWeight: state_colors[props.row.state] == 'red' ? 'bold': ''
                                    }"
                                >
                                  <a
                                      :href="link+'/'+props.row.id"
                                      :style="{color: state_colors[props.row.state]}"
                                  >
                                      {{props.formattedRow[props.column.field]}}
                                  </a>
                                </span>
                              </span>
                              <span v-else style="cursor: default">
                                <div :style="{
                                    color: state_colors[props.row.state],
                                    fontWeight: state_colors[props.row.state] == 'red' ? 'bold': ''
                                }">
                                    {{props.formattedRow[props.column.field]}}
                                </div>
                              </span>
                            </span>

                        </template>
                    </vue-good-table>
                </div>

            </div>
        </div>
    </div>
</template>

<script>

    window.Clipboard = (function(window, document, navigator) {
        var textArea,
            copy;

        function isOS() {
            return navigator.userAgent.match(/ipad|iphone/i);
        }

        function createTextArea(text) {
            textArea = document.createElement('textArea');
            textArea.value = text;
            document.body.appendChild(textArea);
        }

        function selectText() {
            var range,
                selection;

            if (isOS()) {
                range = document.createRange();
                range.selectNodeContents(textArea);
                selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
                textArea.setSelectionRange(0, 999999);
            } else {
                textArea.select();
            }
        }

        function copyToClipboard() {
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }

        copy = function(text) {
            createTextArea(text);
            selectText();
            copyToClipboard();
        };

        return {
            copy: copy
        };
    })(window, document, navigator);

    export default {
        data: function () {
            return {
                downloadAppList: true,

                todos: [],
                completed: [],
                dataFields: ['todos', 'completed'],

                ID_DEVOCHKA: 4,

                address: '1',

                composedAppText: '',
                income_applications: [],
                date: '',
                date_param: '',
                app_type: '',
                component_name: 'applications',
                component_params: {dispatcher_name: 'gleb'},
                app_count: 0,

                error: false,
                error_msg: '',

                thereIsPagination: false,
                page: 1,

                columns: [
                    {
                        label: 'W',
                        field: 'copy',
                    },
                    {
                        label: 'Адрес',
                        field: 'address',
                    },
                    {
                        label: 'Дата',
                        field: 'date',
                        type: 'date',
                        dateInputFormat: 'yyyy-MM-dd', // expects 2018-03-16
                        dateOutputFormat: 'MM-dd', // outputs Mar 16th 2018
                    },
                    {
                        label: 'Время',
                        field: 'time',
                    },
                    {
                        label: 'Собрано',
                        field: 'worker_count',
                        type: 'number'
                    },
                    {
                        label: 'Всего',
                        field: 'worker_total',
                        type: 'number'
                    },
                    {
                        label: 'Прайс',
                        field: 'price',
                    },
                    {
                        label: 'Нал/Карта',
                        field: 'pay_method',
                    },
                    {
                        label: 'Диспетчер',
                        field: 'dispatcher',
                        hidden: true
                    },
                    {
                        label: 'Состояние',
                        field: 'state_label',
                    },
                    {
                        label: 'сост',
                        field: 'state',
                        type: 'number',
                        hidden: true
                    },
                    {
                        label: 'Заказчик',
                        field: 'client_phone_number',
                    },
                    {
                        label: 'Удалить',
                        field: 'delete',
                    }
                ],
                rows: [],
                apps: [],
                userPrivileges: [],

                link: '',

                state_colors: [],
                CLOSED_ST: -1
            }
        },
        mounted()
        {
            /*console.log('Route:');
            console.log(this.$window.route);
            console.log('User privileges');
            console.log(this.$window.userPrivileges);*/
            this.link = this.$axios.defaults.baseURL.substring(0, this.$axios.defaults.baseURL.length-4);
            this.link += '/app/show';
            this.loadData();
            //console.log(this.$store)
        },
        methods: {
            loadData()
            {
                var app = this;
                let parameters = '';

                if (this.$route.params.page !== undefined)
                {
                    this.thereIsPagination = true;
                    this.page = this.$route.params.page;
                    parameters += '/' + this.page;

                }
                console.log(this.page);
                if (this.$route.name == 'account_applications_with_page')
                {
                    this.app_type = 'account';
                    parameters += '/' + this.app_type;
                }

                this.$axios.get('/application/index' + parameters)
                    .then(function (resp) {
                        app.apps = resp.data.apps;
                        app.rows = resp.data.rows;
                        app.userPrivileges = resp.data.userPrivileges;

                        app.app_count = resp.data.appCount;
                        if (app.app_count > 0) {
                            app.rows = resp.data.rows;
                        }

                        app.state_colors = resp.data.state_colors;
                        app.CLOSED_ST = resp.data.CLOSED_ST;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        app.error = true;
                        app.error_msg = resp.response.data.error_msg;
                        //alert("Не удалось загрузить");
                    });

                this.address = this.$store.state.address;
            },

            userCan(privilege)
            {
                return (privilege in this.userPrivileges);
            },

            copyToClipboard(text)
            {
                Clipboard.copy(text);
            },

            locationReload()
            {
                location.reload();
            },

            deleteEntry(id, index) {
                if (confirm("Вы действительно хотите удалить?")) {
                    var app = this;
                    this.$axios.delete('/application/delete/' + id)
                        .then(function (resp) {
                            var result = resp.data.result;
                            if (result == 1) {
                                location.reload();
                            }
                        })
                        .catch(function (resp) {
                            var words = resp.toString().split(' ');
                            var errorStatus = words[words.length - 1];
                            if (errorStatus == '401') {
                                alert("Вы должны залогиниться!");
                                app.$router.push({name: 'login'});
                            }
                        });
                }
            },
            endApp: function(id) {
                var app = this;
                this.$axios.get('/application/end/' + id)
                    .then(function (resp) {
                        var result = resp.data.result;
                        if (result == 1) {
                            location.reload();
                        }
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось завершить заявку");
                    });
            },
            onRightClick(e) {
                let routeData = this.$router.resolve({name: 'applicationCard', params: {id: params.row.id}});
                window.open(routeData.href, '_blank');
            },
            onCopy: function (e) {
                alert('You just copied: ' + e.text)
            },
            onError: function (e) {
                alert('Failed to copy texts')
            }
        }
    }
</script>


<style scoped>

    .big-text {
      font-size: 200%;
    }

    button:disabled,
    button[disabled]{
        opacity: 0.65;
        cursor: not-allowed;
    }

    .disabled {
      opacity: 0.5;
      pointer-events: none;
    }

  .margin-r-b {
    margin-right: 10px;
    margin-bottom: 5px;
  }
</style>