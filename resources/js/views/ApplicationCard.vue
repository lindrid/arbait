<template>
  <div>

    <div class="panel panel-default">
      <div class="panel-heading">{{application.price + ' / ' + application.price_for_worker}} <b>{{application.address}},
        <span :style="{ color: state_colors[application.state] }">
                    {{state_labels[application.state]}}
                </span>
        <span v-if="application.edg == 1" style="color: red">, ЕДГ</span>
      </b></div>
      <div class="panel-body">
        <div class="form-group big-text">
          <button class="btn btn-default" @click="openPhoneNumber">
            <i  class="glyphicon glyphicon glyphicon-earphone">
            </i>
          </button>

          <button type="button" class="btn btn-default"
                  v-clipboard:copy="application.client_phone_number + ' заказчик'"
                  v-clipboard:error="onError">
            <i  v-bind:class="{
                                    'glyphicon glyphicon-copy': true
                                }">
            </i>
          </button>

          {{ application.client_phone_number }}
        </div>

        <div v-html="application.composedAppText"></div>

        <div class="form-group">
          <button type="button"
                  class="btn btn-default"
                  v-clipboard:copy="application.appTextCopyToCb"
                  v-clipboard:error="onError"
                  v-bind:disabled="!userCan('ChangeApplicationInsides')"
          >
            <i class="glyphicon glyphicon-copy"></i>
          </button>


          <router-link v-if="userCan('ChangeApplicationInsides')"
                  :to="{name: 'applicationEdit', params: {id: this.application.id}}"
                        class="btn btn-default">
            <i  class="glyphicon glyphicon-edit"></i>
          </router-link>
        </div>
        <br>


        <div v-if="(application.state == 1) && (userCan('ChangeApplicationInsides'))">
          <b>Назначить рабочего на заявку: </b>

          <br><br>

          <div class="input-group col-xs-12 col-md-4">
            <vue-tel-input
                    id="phone"
                    name="phone"
                    class="form-control"
                    v-model="worker.phone_whatsapp"
                    :placeholder="phonePlaceholder"
                    v-on:onValidate="getWorkerCardBy(worker.phone_whatsapp)"
            ></vue-tel-input>
            <div class="input-group-btn">
              <button class="btn btn-default" @click="openPhoneCall">
                <i class="glyphicon glyphicon-earphone"></i>
              </button>
            </div>
            <div class="input-group-btn">
              <button
                      class="btn btn-default"
                      @click="addPlusWorker"
                      :disabled="plusWorkerBtnDisabled"
              >
                <i  class="glyphicon glyphicon-plus"></i>
              </button>
              <button
                      class="btn btn-default"
                      @click="addInsteadWorker"
                      :disabled="insteadWorkerBtnDisabled"
              >
                <i  class="glyphicon glyphicon-user"></i>
              </button>
            </div>
          </div>
          <span class="help-block alert-danger" v-if="error && errors.phone_whatsapp">{{ errors.phone_whatsapp }}</span>

          <div class="input-group col-xs-8 col-sm-3" v-show="phone_call_visible">
            <vue-tel-input
                    id="phone_call"
                    name="phone_call"
                    class="form-control "
                    v-model="worker.phone_call"
                    placeholder="Номер для звонка"
                    v-on:onValidate="getWorkerCardBy(worker.phone_call)"
            ></vue-tel-input>
          </div>
          <span class="help-block alert-danger" v-if="error && errors.phone_call">{{ errors.phone_call }}</span>


          <div v-for="(pworker, index) in plus_workers" class="input-group col-xs-10 col-md-4">
            <vue-tel-input
                    class="form-control"
                    v-model="pworker.phone"
                    :placeholder="workerPhonePlaceholder(index)"
            ></vue-tel-input>
            <div v-if="debit_cards.length > 1" class="input-group-btn">
                            <span class="label label-default" style="font-size:20px;">
                                {{plus_workers[index].card_index}}
                            </span>
            </div>
            <div v-if="debit_cards.length > 1" class="input-group-btn">
              <button
                      class="btn btn-default"
                      @click="changePlusWorkerCard(index)"
                      :disabled="changeCardBtnDisabled"
              >
                <i class="glyphicon glyphicon-credit-card"></i>
              </button>
            </div>

            <div class="input-group-btn">
              <button
                      class="btn btn-default"
                      @click="addPlusWorker"
                      :disabled="plusWorkerBtnDisabled"
              >
                <i class="glyphicon glyphicon-plus"></i>
              </button>
            </div>
            <div class="input-group-btn">
              <button class="btn btn-default" @click="deletePlusWorker(index)">
                <i class="glyphicon glyphicon-remove"></i>
              </button>
            </div>
          </div>

          <div v-for="(iworker, index) in instead_workers" class="input-group col-xs-10 col-md-4">
            <vue-tel-input
                    class="form-control"
                    v-model="iworker.phone"
                    :placeholder="workerPhonePlaceholder(index)"
            ></vue-tel-input>
            <div class="input-group-btn">
              <button
                      class="btn btn-default"
                      @click="addInsteadWorker"
                      :disabled="insteadWorkerBtnDisabled"
              >
                <i class="glyphicon glyphicon-user"></i>
              </button>
            </div>
            <div class="input-group-btn">
              <button class="btn btn-default" @click="deleteInsteadWorker(index)">
                <i class="glyphicon glyphicon-remove"></i>
              </button>
            </div>
          </div>

          <br>

          <div v-for="(dcard, index) in debit_cards" class="input-group col-xs-12 col-md-6">
                        <span
                                class="input-group-addon"
                                @click="debitCardForAssignTieOrNot(index)"
                        >
                            <label
                            >
                                {{dc_tie_label[debit_cards[index].tie]}}
                            </label>
                        </span>
            <span class="input-group-addon"><span>Карта {{index+1}}</span></span>
            <span class="new-line">
              <input
                      class="form-control"
                      v-model="dcard.number"
              />
            </span>

            <div class="input-group-btn" id="debitCardGroupBtn">
              <button class="btn btn-default" @click="setCardByPhone(index)">
                <i  class="glyphicon glyphicon-phone"></i>
              </button>

              <button
                      class="btn btn-default"
                      @click="addDebitCard"
                      :disabled="!debitCardBtnEnabled"
              >
                <i class="glyphicon glyphicon-plus"></i>
              </button>

              <button class="btn btn-default"
                      @click="dcard.number = ''"
                      :disabled="dcard.number == ''"
              >
                <i class="glyphicon glyphicon-remove-circle"></i>
              </button>

              <button class="btn btn-default"
                      @click="deleteCard(index)"
                      :disabled = "index == 0"
              >
                <i class="glyphicon glyphicon-remove"></i>
              </button>
            </div>
          </div>
          <span class="help-block alert-danger" v-if="error && errors.debit_cards">{{ errors.debit_cards }}</span>


          <br>
          <form autocomplete="off" @submit.prevent="assignWorker" method="post">
            <div
                    v-if="error && errors.assign_worker_phone"
                    v-html="errors.assign_worker_phone" class="alert alert-danger">
            </div>
            <br>

            <button id="addWorkerBtn" type="submit" class="btn btn-default">Назначить</button>
            <br><br>
          </form>
        </div>

        <span v-if="application.worker_count < application.worker_total">
                    <span style="color: red;">
                        {{ action_label }} человек: {{ application.worker_count }} / {{ application.worker_total }}
                    </span>
                </span>
        <span v-else>
                    <span style="color: green;">
                        {{ action_label }} человек: {{ application.worker_count }} / {{ application.worker_total }}
                    </span>
                </span>

        <button type="button" class="btn btn-default"
                v-clipboard:copy="workersPhoneNumbers"
                v-clipboard:error="onError"
                v-bind:disabled="!userCan('ChangeApplicationInsides')"
        >
          <i  v-bind:class="{
                                    'glyphicon glyphicon-copy': true
                                }">
          </i>
        </button>


        <br>

        <div style="overflow-x:auto;">
          <table class="worker-table" id="worker-table">
          <tr>
            <th><b>Тел. номер</b></th>
            <th><b>Карта</b></th>
            <th><b>Денег</b></th>
            <th><b>Часов</b></th>
            <th><b>Ответ-ный</b></th>
            <th><b>Удалить</b></th>
          </tr>
          <tr>
          <tr v-for="worker in workers">

            <td >
              <div v-bind:style="{
                        color: backgroundColorFunc(worker.index)
                      }">
                <div v-if="worker.parent == 1">
                  <strong v-on:click="toggleChildWorkers(worker)" class="pointer">
                    {{worker.phone}}(+{{child_workers[worker.id].length}})
                  </strong>
                </div>
                <div v-else>
                  <input
                          v-bind:disabled="(application.state > ENDED_ST) ||
                            !userCan('ChangeApplicationInsides')"
                          v-model="worker.phone"
                          class="col-sm-input-phone"
                  >
                </div>
              </div>
            </td>

            <td class="to-left">
              <div v-bind:style="{
                        color: backgroundColorFunc(worker.index)
                      }">
                          <span >
                            <label
                                    class="dc_label"
                                    @click="worker.tie_debit_card = !worker.tie_debit_card"
                                    v-bind:disabled="!userCan('ChangeApplicationInsides')"
                            >
                              {{ dc_tie_label[worker.tie_debit_card] }}
                            </label>
                          </span>

                <input
                        v-bind:disabled="(application.state > READY_TO_PAY_ST) ||
                                    !userCan('ChangeApplicationInsides')"
                        v-model="worker.debit_card"
                        class="col-sm-input-debitcard"
                >
                <button type="button" class="btn btn-default"
                        v-if="
                                          application.state == READY_TO_PAY_ST &&
                                          application.pay_method != PM_CASH
                                  "
                        v-clipboard:copy="getNumbers(worker.debit_card)"
                        v-clipboard:error="onError"
                        v-bind:disabled="!userCan('ChangeApplicationInsides')"
                >
                  <i  class="glyphicon glyphicon-copy"></i>
                </button>
                <button type="button" class="btn btn-default"
                        v-if="application.state == READY_TO_PAY_ST"
                        @click="workerWasPayed(worker.index)"
                        v-bind:disabled="!userCan('ChangeApplicationInsides')"
                >
                  <i  class="glyphicon glyphicon-ok"></i>
                </button>
              </div>
            </td>

            <td class="to-left">
              <span v-if="(worker.parent == 1)">
                  <input
                          v-bind:disabled="(application.state > ENDED_ST) ||
                            !userCan('ChangeApplicationInsides')"
                          v-model="worker.total_parent_money"
                          class="col-sm-input-money"
                  >

&nbsp;                         <b><u>{{1+child_workers[worker.id].length}}чел</u></b>

                  <br><br>
                  <input
                          v-bind:disabled="(application.state > ENDED_ST) ||
                            (!userCan('ChangeApplicationInsides'))"
                          v-model="worker.money"
                          @input="changeTotalParentMoney(worker.index, worker.id)"
                          @click="$event.target.select()"
                          class="col-sm-input-money"
                  >
              </span>
              <span v-else>
                <input
                        v-bind:disabled="(application.state > ENDED_ST) ||
                          !userCan('ChangeApplicationInsides')"
                        v-model="worker.money"
                        @click="$event.target.select()"
                        class="col-sm-input-money"
                >
              </span>
            </td>

            <td class="to-left">
                      <span v-if="worker.parent == 1 &&
                                  worker.relation_type != 'c'"
                      >
                          <input
                                  v-bind:disabled="(application.state > ENDED_ST) ||
                                    !userCan('ChangeApplicationInsides')"
                                  v-model="worker.total_parent_work_hours"
                                  class="col-sm-input-workhours"
                          >
                          &nbsp; <b><u>{{1+child_workers[worker.id].length}}чел</u></b>
                          <br><br>
                          <input
                                  v-bind:disabled="(application.state > ENDED_ST) ||
                                    !userCan('ChangeApplicationInsides')"
                                  v-model="worker.work_hours"
                                  @input="changeTotalParentWorkHoursAndMoney(worker.index, worker.id)"
                                  @click="$event.target.select()"
                                  class="col-sm-input-workhours"
                          >
                      </span>
              <span v-else>
                          <input
                                  v-bind:disabled="(application.state > ENDED_ST) ||
                                    !userCan('ChangeApplicationInsides')"
                                  v-model="worker.work_hours"
                                  @input="changeMoney(worker.index)"
                                  @click="$event.target.select()"
                                  class="col-sm-input-workhours"
                          >
                      </span>
            </td>

            <td>
              <label>
                <input  v-bind:disabled="(application.state > ENDED_ST) ||
                                    !userCan('ChangeApplicationInsides')"
                        v-model="worker.responsible_for_money"
                        type="checkbox"
                />
              </label>
            </td>

            <td>
              <a href="#"
                 v-bind:disabled="(application.state > CLOSED_ST) ||
                                    !userCan('ChangeApplicationInsides')"
                 class="btn btn-xs btn-danger"
                 v-on:click="deleteEntryById(worker.id, null)"
              >
                Удалить
              </a>
            </td>
          </tr>

        </table>
        </div>

        <div v-if="application.state >= ENDED_ST">
          <br>

          <div v-if="application.pay_method === PM_ACCOUNT">
            <div class="input-group col-8 col-xs-12 col-md-3">
                            <span class="input-group-addon">
                                <span v-if="application.state != PAYED_ST">
                                    Клиент платит c НДС
                                </span>
                                <span v-else>Доход с НДС</span>
                            </span>
              <input
                      disabled
                      class="form-control"
                      v-model="application.income_with_nds"
              />
            </div>
            <div class="input-group col-xs-6 col-md-2">
                                <span class="input-group-addon">
                                    <span>НДС</span>
                                </span>
              <input
                      disabled
                      class="form-control"
                      v-model="application.nds"
              />
            </div>
            <br>
          </div>


          <div class="input-group col-8 col-xs-12 col-md-3">
                        <span class="input-group-addon">
                            <span v-if="application.state != PAYED_ST">
                                Клиент платит
                            </span>
                            <span v-else>Доход</span>
                        </span>
            <input
                    v-bind:disabled="!this.userCan('ChangeApplicationInsides') ||
                      application.payed_by_client  ||
                      (application.state > ENDED_ST && application.state != NOT_PAYED_ST)"
                    class="form-control"
                    v-model="application.income"
            />
            <div class="input-group-btn">
              <button  class="btn btn-default"
                       @click="refreshIncome()"
                       v-bind:disabled="!this.userCan('ChangeApplicationInsides')"
              >
                <i  class="glyphicon glyphicon-refresh"></i>
              </button>

              <span v-if="application.state != PAYED_ST">
                                <button v-if="!application.payed_by_client" type="button" class="btn btn-default"
                                        @click="clientPayedMoney"
                                        v-bind:disabled="!this.userCan('ChangeApplicationInsides')"
                                >
                                    <i  class="glyphicon glyphicon-ok"></i>
                                </button>
                                <button v-if="application.payed_by_client" type="button" class="btn btn-default"
                                        @click="clientPayedMoney"
                                        v-bind:disabled="!this.userCan('ChangeApplicationInsides')"
                                >
                                    <i  class="glyphicon glyphicon-remove"></i>
                                </button>
                            </span>
              <span v-else>
                                <button v-if="application.payed_by_client" type="button" class="btn btn-default"
                                        @click="clientPayedMoney"
                                        v-bind:disabled="!this.userCan('ChangeApplicationInsides')"
                                >
                                    <i  class="glyphicon glyphicon-remove"></i>
                                </button>
                            </span>
            </div>
          </div>

          <span v-if="application.state != PAYED_ST && application.state > CLOSED_ST">
                        <font size="3" color="green" v-if="application.payed_by_client">
                            Клиент оплатил
                        </font>
                        <font size="3" color="red" v-if="!application.payed_by_client">
                            Клиент не оплатил
                        </font>
                    </span>

          <div v-if="application.state == PAYED_ST" class="input-group col-xs-6 col-md-2">
                        <span class="input-group-addon">
                            <span>Расход</span>
                        </span>
            <input
                    disabled
                    class="form-control"
                    v-model="application.outcome"
            />
          </div>
          <div v-if="application.state == PAYED_ST">
            <div class="input-group col-xs-6 col-md-2">
                            <span class="input-group-addon">
                                <span>Прибыль</span>
                            </span>
              <input
                      disabled
                      class="form-control"
                      v-model="application.profit"
              />
            </div>
          </div>

        </div>

        <br>

        <div v-if="userCan('ChangeApplicationInsides')" id="operations" class="form-group">
          <button
                  v-bind:disabled="(application.state == PAYED_ST)? true: false"
                  @click="save()"
                  class="btn btn-default"
          >
            Cохранить
          </button>
          &nbsp;&nbsp;&nbsp;
          <button v-if="application.state == CLOSED_ST"
                  @click="endApp()" class="btn btn-default">
            Завершить
          </button>
          &nbsp;<input
                v-if="(application.state == CLOSED_ST)"
                id = "end_work_hours"
                v-model="work_hours"
                class="col-sm-input-workhours"
                @focus="$event.target.select()"
        >&nbsp;&nbsp;<b>{{printWorkHoursLabel}}</b>
          <button v-if="application.state == ENDED_ST"
                  @click="readyToPayApp()" class="btn btn-default">
            Готово!
          </button>
          <button v-if="application.state == READY_TO_PAY_ST"
                  @click="rollback()" class="btn btn-default">
            Откатить
          </button>
        </div>
        <span

                class="help-block alert-danger"
                v-if="error && errors.operations"
        >
                    {{ errors.operations }}
                </span>

        <br>

        <div class="form-group">
          <router-link to="/apps" class="btn btn-default">Назад</router-link>
          <button onclick="window.close();" class="btn btn-default">Закрыть</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
    import VueTelInput from 'vue-tel-input';

    export default {
        name: "applicationCard",
        components: {
            VueTelInput,
        },
        computed: {

            workersPhoneNumbers ()
            {
                var s = "Номера рабочих по заявке:\n";
                for (var i = 0; i < this.workers.length; i++) {
                    if (this.workers[i].phone.length > 4) {
                        s = s + this.workers[i].phone + "\n";
                    }
                }

                return s;
            },

            printWorkHoursLabel: function ()
            {
                if (this.work_hours == '') return 'Часов';
                if (this.work_hours == 1) return 'Час';
                if (this.work_hours == 2) return 'Часа';
                if (this.work_hours == 3) return 'Часа';
                if (this.work_hours == 4) return 'Часа';
                if (this.work_hours == 5) return 'Часов';
                if (this.work_hours == 6) return 'Часов';
                if (this.work_hours == 7) return 'Часов';
                if (this.work_hours == 8) return 'Часов';
                if (this.work_hours == 9) return 'Часов';
                if (this.work_hours == 10) return 'Часов';
                if (this.work_hours == 11) return 'Часов';
                if (this.work_hours == 12) return 'Часов';
                if (this.work_hours == 13) return 'Часов';
                if (this.work_hours == 14) return 'Часов';
            },

            debitCardBtnEnabled: function () {
                return (
                    (this.plus_workers.length +1 > this.debit_cards.length)||
                    (this.instead_workers.length > this.debit_cards.length)
                );
            },

            plusWorkerBtnDisabled: function () {
                return (
                    (this.instead_workers.length > 0) ||
                    ! (
                        this.application.worker_total - this.application.worker_count
                        > this.plus_workers.length + 1
                    )
                );
            },

            insteadWorkerBtnDisabled: function () {
                return (
                    (this.plus_workers.length > 0) ||
                    ! (
                        this.application.worker_total - this.application.worker_count
                        > this.instead_workers.length
                    )
                );
            },

            changeCardBtnDisabled: function ()
            {
                return (this.debit_cards.length == 1);
            },


        },
        data: function () {
            return {
                CLOSED_ST: -1,
                ENDED_ST: -1,
                READY_TO_PAY_ST: -1,
                PAYED_ST: -1,
                NOT_PAYED_ST: -1,
                PM_CASH: -1,
                PM_ACCOUNT: -1,

                application: {
                    id: -1,
                    address: '',
                    state: 0,
                    income: '',
                    client_phone_number: '',
                    composedAppText: '',
                    appTextCopyToCb: '',
                    payed_by_client: 0
                },

                state_labels: [],
                state_colors: [],

                worker: {
                    phone_whatsapp: '',
                    phone_call: ''
                },

                plus_workers: [],
                instead_workers:[],
                debit_cards: [],

                child_workers: [],
                openAccordionsIndexes: [],
                openAccordionsStrCount: [],

                styleOfOpenAccStrings : 'border: 2px solid darkblue; padding: 5px;',
                styleOfGotMoneyAccStrings : 'border: 2px solid green; padding: 5px;',

                client_phone: 'tel:',

                phone_call_visible: false,
                phonePlaceholder: 'Номер общий',
                phone_icon_press_cnt: [],
                action_label: 'Найдено ',

                columns: [
                    {
                        label: 'Тел. номер',
                        field: 'phone',
                        width: '200px'
                    },
                    {
                        label: 'Карта',
                        field: 'debit_card',
                        width: '300px'
                    },
                    {
                        label: 'Денег',
                        field: 'money',
                        width: '200px'
                    },
                    {
                        label: 'Часов',
                        field: 'work_hours',
                        width: '200px'
                    },
                    {
                        label: 'Отв-ый',
                        field: 'responsible_for_money',
                        width: '100px'
                    },
                    {
                        label: 'создана',
                        field: 'created_at',
                        hidden: true,
                        sortable: true
                    },
                    {
                        label: 'Удалить',
                        field: 'delete',
                    }
                ],
                workers: [
                    /*
                        id
                        phone
                        debit_card,
                        money,
                        work_hours,
                        got_money,

                        has_anothers_card,

                        parent_worker_id,
                        child,
                        parent,

                        total_money_same_card,

                        total_parent_work_hours,
                        total_parent_money,
                        relation_type,
                     */
                ],
                original_workers_copy: [],
                original_child_workers_copy: [],

                got_money_worker_count: 0,

                work_hours: '',

                error: false,
                errors: {},

                pw_valid: true,
                pw_valid2: false,

                dc_tie_to_assign_worker: [],
                dc_tie_label: {
                    true:  'П', // Привязывать
                    false: 'Н'  // Не привязывать
                },

                userPrivileges: []
            }
        },
        methods: {
            userCan(privilege)
            {
                return (privilege in this.userPrivileges);
            },

            getNumbers(text)
            {
                function isCharNumber(c) {
                    return c >= 0 && c <= 9;
                }

                let stringWithNumbers = '';
                for (let i = 0; i < text.toString().length; i++)
                {
                    if (isCharNumber(text.charAt(i)) || (text.charAt(i) === '+'))
                    {
                        if (text.charAt(i) !== ' ')
                        {
                            stringWithNumbers += text.charAt(i);
                        }
                    }
                }

                return stringWithNumbers;
            },

            toggleChildWorkers(parentWorker)
            {
                var table = document.getElementById("worker-table");
                var childWorkers = this.child_workers[parentWorker.id];
                var childWorkersCount = childWorkers.length;
                var parentWorkersIndexes = this.openAccordionsIndexes;
                var dI = 0;
                var userWantsCloseThisAccordion = false;
                var indexOfClosingAcc = -1;

                console.log(parentWorker);

                for (var j = 0; j < parentWorkersIndexes.length; j++)
                {
                    if (parentWorkersIndexes[j] < parentWorker.index)
                    {
                        dI += this.openAccordionsStrCount[j];
                    }
                    else if (parentWorkersIndexes[j] == parentWorker.index)
                    {
                        userWantsCloseThisAccordion = true;
                        indexOfClosingAcc = j;
                    }
                }

                if (userWantsCloseThisAccordion)
                {
                    for (var i = 0; i < this.openAccordionsStrCount[indexOfClosingAcc]; i++)
                    {
                        table.deleteRow(parentWorker.index + 3 + dI);
                    }
                    this.openAccordionsIndexes.splice(indexOfClosingAcc, 1);
                    this.openAccordionsStrCount.splice(indexOfClosingAcc, 1);
                    return;
                }

                this.openAccordionsIndexes.push(parentWorker.index);
                this.openAccordionsStrCount.push(childWorkersCount);

                for (var i = 0; i < childWorkersCount; i++)
                {
                    var app = this;
                    var row = table.insertRow(parentWorker.index + 3 + dI + i);
                    var parentId = parentWorker.id;
                    var cellStyle;

                    if (parentWorker.got_money) {
                        cellStyle = this.styleOfGotMoneyAccStrings;
                    }
                    else {
                        cellStyle = this.styleOfOpenAccStrings;
                    }

                    row.className = "openAccordionRow";

                    /******************** cell1 *********************/
                    var cell1 = row.insertCell(0);
                    var phoneInput = document.createElement('input');

                    cell1.style.cssText = cellStyle;
                    phoneInput.type = "text";
                    phoneInput.name = "phone_name_" + childWorkers[i].id;
                    phoneInput.id = 'phone_id_' + childWorkers[i].id;
                    phoneInput.value = childWorkers[i].phone;
                    phoneInput.style.cssText = 'width:150px';
                    if (!this.userCan('ChangeApplicationInsides'))
                    {
                        phoneInput.disabled = true;
                    }

                    if (this.application.state > this.ENDED_ST) {
                        phoneInput.disabled = true;
                        //phoneInput.classList.add('lightgrey');
                        phoneInput.style.cssText = 'width:150px; color: black; background-color: #f2f2f2;' +
                            '-webkit-text-fill-color: black;\n' +
                            '-webkit-opacity: 1;';
                    }

                    phoneInput.oninput = function (worker) {
                        return function () {
                            app.changeChildParam('phone', worker.index, parentId);
                        };
                    }(childWorkers[i]);

                    cell1.appendChild(phoneInput);

                    /******************** cell2 *********************/
                    var cell2 = row.insertCell(1);
                    var dcInput = document.createElement('input');

                    cell2.style.cssText = cellStyle;
                    dcInput.type = "text";
                    dcInput.name = "debit_card_name_" + childWorkers[i].id;
                    dcInput.id = 'debit_card_id_' + childWorkers[i].id;
                    dcInput.value = childWorkers[i].debit_card;
                    dcInput.style.width = '200px';
                    if (!this.userCan('ChangeApplicationInsides'))
                    {
                        dcInput.disabled = true;
                    }

                    if (this.application.state > this.ENDED_ST) {
                        dcInput.disabled = true;
                        dcInput.style.cssText += 'color: black; background-color: #f2f2f2;' +
                            '-webkit-text-fill-color: black;\n' +
                            '-webkit-opacity: 1;';
                    }

                    dcInput.oninput = function (worker) {
                        return function () {
                            app.changeChildParam('debit_card', worker.index, parentId);
                        };
                    }(childWorkers[i]);

                    cell2.appendChild(dcInput);

                    /******************** cell3 *********************/
                    var cell3 = row.insertCell(2);
                    var moneyInput = document.createElement('input');

                    cell3.style.cssText = cellStyle;
                    moneyInput.type = "text";
                    moneyInput.name = "money_name_" + childWorkers[i].id;
                    moneyInput.id = 'money_id_' + childWorkers[i].id;
                    moneyInput.style.cssText = 'width:80px;';
                    if (!this.userCan('ChangeApplicationInsides'))
                    {
                        moneyInput.disabled = true;
                    }

                    if (childWorkers[i].money === undefined) {
                        moneyInput.value = '';
                    }
                    else {
                        moneyInput.value = childWorkers[i].money;
                    }

                    if (this.application.state !== this.ENDED_ST) {
                        moneyInput.disabled = true;
                        moneyInput.style.cssText += 'color: black; background-color: #f2f2f2;' +
                            '-webkit-text-fill-color: black;\n' +
                            '-webkit-opacity: 1;';
                    }

                    moneyInput.oninput = function (worker) {
                        return function () {
                            app.changeChildParam('money', worker.index, parentId);
                        };
                    }(childWorkers[i]);

                    moneyInput.onclick = function() {
                        document.execCommand("selectall",null,false);
                    };

                    cell3.appendChild(moneyInput);

                    /******************** cell4 *********************/
                    var cell4 = row.insertCell(3);
                    var workHoursInput = document.createElement('input');

                    cell4.style.cssText = cellStyle;
                    workHoursInput.type = "text";
                    workHoursInput.name = "work_hours_name_" + childWorkers[i].id;
                    workHoursInput.id = 'work_hours_id_' + childWorkers[i].id;
                    workHoursInput.style.cssText = 'width:80px;';
                    if (!this.userCan('ChangeApplicationInsides'))
                    {
                        workHoursInput.disabled = true;
                    }

                    if (childWorkers[i].work_hours === undefined) {
                        workHoursInput.value = '';
                    }
                    else {
                        workHoursInput.value = childWorkers[i].work_hours;
                    }

                    if (this.application.state !== this.ENDED_ST) {
                        workHoursInput.disabled = true;
                        workHoursInput.style.cssText += 'color: black; background-color: #f2f2f2;' +
                            '-webkit-text-fill-color: black;\n' +
                            '-webkit-opacity: 1;';
                    }

                    workHoursInput.oninput = function (worker) {
                        return function () {
                            app.changeChildParam('work_hours', worker.index, parentId);
                        };
                    }(childWorkers[i]);

                    workHoursInput.onclick = function() {
                        document.execCommand("selectall",null,false);
                    };

                    cell4.appendChild(workHoursInput);

                    /******************** cell5 *********************/
                    var cell5 = row.insertCell(4);
                    cell5.style.cssText = cellStyle;
                    cell5.innerHTML = '';//childWorkers[i].responsible_for_money;

                    /******************** cell6 *********************/
                    var cell6 = row.insertCell(5);
                    var btn = document.createElement('input');

                    cell6.style.cssText = cellStyle;
                    btn.type = "button";
                    btn.name = "btn_name_" + childWorkers[i].id;
                    btn.id = 'btn_id_' + childWorkers[i].id;
                    btn.className = "btn btn-xs btn-danger";
                    btn.value = "Удалить";

                    if ((this.application.state > this.CLOSED_ST) ||
                        (!this.userCan('ChangeApplicationInsides')))
                    {
                        btn.disabled = true;
                    }

                    btn.onclick = function (worker) {
                        return function () {
                            app.deleteEntryById(worker.id, parentId);
                        };
                    }(childWorkers[i]);

                    cell6.appendChild(btn);
                }
            },

            changeChildParam(param, index, parentId)
            {
                let workerId = this.child_workers[parentId][index].id;
                let input = document.getElementById(param + '_id_' + workerId);

                if (param === 'phone') {
                    this.child_workers[parentId][index].phone = input.value;
                }

                if (param === 'debit_card') {
                    this.child_workers[parentId][index].debit_card = input.value;
                }

                let app = this;
                let changeMoneyAndTotalParentMoney = function (inputValue) {
                    let beforeEditVal = app.child_workers[parentId][index].money;
                    let diff = inputValue - beforeEditVal;

                    app.child_workers[parentId][index].money = inputValue;

                    for (let i = 0; i < app.workers.length; i++) {
                        if (parentId === app.workers[i].id) {
                            app.workers[i].total_parent_money =
                                Number(app.workers[i].total_parent_money) + diff;
                            break;
                        }
                    }
                };

                if (param === 'money') {
                    changeMoneyAndTotalParentMoney(input.value);
                }

                if (param === 'work_hours') {
                    let beforeEditVal = this.child_workers[parentId][index].work_hours;
                    let diff = input.value - beforeEditVal;

                    this.child_workers[parentId][index].work_hours = input.value;

                    for (let i = 0; i < app.workers.length; i++) {
                        if (parentId === app.workers[i].id) {
                            app.workers[i].total_parent_work_hours =
                                Number(app.workers[i].total_parent_work_hours) + diff;
                            break;
                        }
                    }

                    let inputMoney = document.getElementById('money_id_' + workerId);
                    inputMoney.value = this.application.price_for_worker * input.value;
                    changeMoneyAndTotalParentMoney(inputMoney.value);
                }

                //console.log(input.value);
                console.log(this.workers);
                console.log(this.child_workers);
            },

            changeTotalParentMoney(workerIndex, workerId)
            {
                this.workers[workerIndex].total_parent_money = this.workers[workerIndex].money;
                for (let i = 0; i < this.child_workers[workerId].length; i++) {
                    this.workers[workerIndex].total_parent_money = Number(this.workers[workerIndex].total_parent_money) +
                        Number(this.child_workers[workerId][i].money);
                }
            },

            changeTotalParentWorkHoursAndMoney(workerIndex, workerId)
            {
                this.workers[workerIndex].money = this.workers[workerIndex].work_hours *
                    this.application.price_for_worker;

                this.workers[workerIndex].total_parent_money =
                    this.workers[workerIndex].money;

                this.workers[workerIndex].total_parent_work_hours =
                    this.workers[workerIndex].work_hours;

                for (let i = 0; i < this.child_workers[workerId].length; i++) {
                    this.workers[workerIndex].total_parent_work_hours =
                        Number(this.workers[workerIndex].total_parent_work_hours) +
                        Number(this.child_workers[workerId][i].work_hours);

                    this.workers[workerIndex].total_parent_money =
                        Number(this.workers[workerIndex].total_parent_money) +
                        Number(this.child_workers[workerId][i].money);
                }
            },

            changeMoney(workerIndex)
            {
                this.workers[workerIndex].money = this.workers[workerIndex].work_hours *
                    this.application.price_for_worker;
            },

            clientPayedMoney()
            {
                var app = this;
                this.application.payed_by_client = !this.application.payed_by_client;

                this.$axios.post('/application/payedbyclient', {
                    id : this.application.id,
                    payed_by_client: this.application.payed_by_client,
                    income: this.application.income
                }).
                then(response => {
                    if (response.status == 200) {
                        location.reload();
                    }
                    else {
                        app.error = true;
                        app.errors = {
                            operations: response.data.errors.operations
                        };
                        document.getElementById("operations").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                }).
                catch(error => {
                    app.error = true;
                    app.errors = {
                        operations: error.response.data.message
                    };
                    document.getElementById("operations").scrollIntoView({block: "center", behavior: "smooth"});
                });
            },

            refreshIncome()
            {
                var workHours = 0;
                for (var i = 0; i < this.workers.length; i++)
                {
                    workHours += parseInt(this.workers[i].work_hours);
                    if (this.workers[i].parent == 1)
                    {
                        var pid = this.workers[i].id;
                        for (var j = 0; j < this.child_workers[pid].length; j++)
                        {
                            workHours += parseInt(this.child_workers[pid][j].work_hours);
                        }
                    }
                }
                this.application.income = workHours * this.application.price;
            },

            openPhoneNumber()
            {
                window.open('tel:' + this.application.client_phone_number);
            },

            selectEndWorkHours() {
                var input = document.getElementById("end_work_hours");
                var s = input.value;
                if (s.length) {
                    window.setTimeout(function() {
                        input.setSelectionRange(s.length-2, s.length);
                    }, 0);
                }
            },

            debitCardForAssignTieOrNot: function (index)
            {
                var val = this.debit_cards[index].tie;
                this.debit_cards[index].tie = !val;
            },

            debitCardTieOrNot: function (rowIndex)
            {
                var val = this.workers[rowIndex].tie_debit_card;
                this.workers[rowIndex].tie_debit_card = !val;
                //this.$set(this.dc_edit_worker, rowIndex, !val);
                //console.log(this.dc_operations);
            },

            backgroundColorFunc: function (rowIndex)
            {
                if (this.workers[rowIndex].got_money == 1) {
                    return 'green';
                }
                return '';
            },

            workerWasPayed(rowIndex)
            {
                if (this.workers[rowIndex].got_money == 0) {
                    var app = this;

                    var same_workers = this.getWorkersWithSameCard(rowIndex);
                    this.$axios.post('/application/worker/got/money',
                        {
                            'app_id': app.application.id,
                            'worker_id': app.workers[rowIndex].id,
                            'parent': app.workers[rowIndex].parent,
                            'workers_with_same_debit_card': same_workers
                        }
                    ).then(response => {
                        if (response.status == 200) {
                            app.workers[rowIndex].got_money = 1;
                            let allWorkersGotMoney = response.data.allWorkersGotMoney;
                            app.error = false;
                            if (allWorkersGotMoney) {
                                app.$router.push({name: 'applications_with_page', params: {page: 1}});
                            }
                        }
                    }).catch(function (error) {
                        app.error = true;
                        //console.log(error.response.data);
                        app.errors = error.response.data.errors;
                        console.log(app.errors);
                    });
                }
            },

            getWorkersWithSameCard(rowIndex)
            {
                var debit_card = this.workers[rowIndex].debit_card;
                var same_workers = [];

                if ( typeof(debit_card) !== "undefined" && debit_card !== null ) {
                    debit_card = debit_card.toString().trim();
                }

                for (var i = 0; i < this.workers.length; i++) {
                    var everyWorkerkDC = this.workers[i].debit_card;
                    if ( typeof(everyWorkerkDC) !== "undefined" && everyWorkerkDC !== null ) {
                        everyWorkerkDC = everyWorkerkDC.toString().trim();
                    }
                    if (everyWorkerkDC      == debit_card &&
                        this.workers[i].id  != this.workers[rowIndex].id)
                    {
                        same_workers.push(this.workers[i].id);
                    }
                }

                return same_workers;
            },

            refreshMoney(rowIndex)
            {
                this.workers[rowIndex].money = this.application.price_for_worker * this.workers[rowIndex].work_hours;
            },

            refreshParentWorkHours(rowIndex)
            {
                var parent_worker_id = this.workers[rowIndex].id;

                if (this.workers[rowIndex].relation_type == '+') {
                    this.workers[rowIndex].total_parent_work_hours = parseInt(this.workers[rowIndex].work_hours);
                }

                if (this.workers[rowIndex].relation_type == 'i') {
                    this.workers[rowIndex].total_parent_work_hours = 0;
                }

                for (var i = 0; i < this.workers.length; i++) {
                    if (parent_worker_id == this.workers[i].parent_worker_id) {
                        this.workers[rowIndex].total_parent_work_hours += parseInt(this.workers[i].work_hours);
                    }
                }
            },

            refreshParentMoney(rowIndex)
            {
                var parent_worker_id = this.workers[rowIndex].id;
                let childWorkers = this.child_workers[parent_worker_id];
                this.workers[rowIndex].total_parent_money = 0;

                for (var i = 0; i < childWorkers.length; i++) {
                    this.workers[rowIndex].total_parent_money +=
                        parseInt(childWorkers[i].money);
                }

                if (this.workers[rowIndex].relation_type == '+') {
                    this.workers[rowIndex].total_parent_money += parseInt(this.workers[rowIndex].money);
                }
            },

            refreshSameCardMoney(rowIndex)
            {
                var debit_card = this.workers[rowIndex].debit_card;

                if ( typeof(debit_card) !== "undefined" && debit_card !== null ) {
                    debit_card = debit_card.toString().trim();
                }

                for (var i = 0; i < this.workers.length; i++) {
                    var everyWorkerkDC = this.workers[i].debit_card;
                    if ( typeof(everyWorkerkDC) !== "undefined" && everyWorkerkDC !== null ) {
                        everyWorkerkDC = everyWorkerkDC.toString().trim();
                    }
                    if (everyWorkerkDC      == debit_card &&
                        this.workers[i].id  != this.workers[rowIndex].id)
                    {
                        same_workers.push(this.workers[i].id);
                    }
                }

                return same_workers;
            },

            synchronizeMoney(rowIndex)
            {
                for (var i = 0; i < this.workers.length; i++) {
                    this.workers[i].money = this.workers[rowIndex].money;
                }
                //console.log(this.workers[rowIndex]);
            },

            synchronizeWorkHours(rowIndex) {
                for (var i = 0; i < this.workers.length; i++) {
                    this.workers[i].work_hours = this.workers[rowIndex].work_hours;
                }
                //console.log(this.workers[rowIndex]);
            },

            deleteEntry(index)
            {
                if (this.application.state > this.CLOSED_ST) {
                    return;
                }
                if (confirm("Вы действительно хотите удалить рабочего?")) {
                    var app = this;
                    this.$axios.post('/application/delete/worker',
                        {
                            application_id: app.application.id,
                            worker_id: app.workers[index].id,
                            workers: app.workers,
                            parent_worker_id: app.workers[index].parent_worker_id
                        }
                    )
                        .then(response => {
                            if (response.status == 200) {
                                //this.day_income = response.data.day_income;
                                this.error = false;
                                location.reload();
                            }
                        }).catch(function (error) {
                        var words = error.toString().split(' ');
                        var errorStatus = words[words.length - 1];
                        if (errorStatus == '401') {
                            alert("Вы должны залогиниться!");
                            app.$router.push({name: 'login'});
                        }
                    });
                }
            },

            deleteEntryById(workerId, parentWorkerId)
            {
                if (this.application.state > this.CLOSED_ST) {
                    return;
                }

                var phone = '';
                if (parentWorkerId === null) {
                    for (var i = 0; i < this.workers.length; i++) {
                        if (this.workers[i].id == workerId) {
                            phone = this.workers[i].phone;
                            break;
                        }
                    }
                }
                else {
                    console.log(this.child_workers[parentWorkerId][1]);
                    for (var i = 0; i < this.child_workers[parentWorkerId].length; i++) {
                        if (this.child_workers[parentWorkerId][i].id == workerId) {
                            phone = this.child_workers[parentWorkerId][i].phone;
                            break;
                        }
                    }
                }

                if (confirm("Вы действительно хотите удалить рабочего " + phone + " ?")) {
                    var app = this;

                    this.$axios.post('/application/delete/worker',
                        {
                            application_id: app.application.id,
                            worker_id: workerId,
                            child_workers: app.child_workers,
                            parent_worker_id: parentWorkerId
                        }
                    )
                        .then(response => {
                            if (response.status == 200) {
                                //this.day_income = response.data.day_income;
                                this.error = false;
                                location.reload();
                            }
                        }).catch(function (error) {
                        var words = error.toString().split(' ');
                        var errorStatus = words[words.length - 1];
                        if (errorStatus == '401') {
                            alert("Вы должны залогиниться!");
                            app.$router.push({name: 'login'});
                        }
                    });
                }
            },

            changePlusWorkerCard: function (index)
            {
                if (this.plus_workers[index].card_index
                    < this.debit_cards.length)
                {
                    this.plus_workers[index].card_index++
                }
                else {
                    this.plus_workers[index].card_index = 1;
                }
            },

            addDebitCard: function ()
            {
                this.debit_cards.push({
                    number: "",
                    tie: true
                });
                this.phone_icon_press_cnt.push(0);
            },

            addPlusWorker: function ()
            {
                this.plus_workers.push({
                    phone: "",
                    card_index: 1
                });
            },

            addInsteadWorker: function ()
            {
                this.instead_workers.push({
                    phone: "",
                    card_index: 1
                });
            },

            deletePlusWorker: function (index)
            {
                this.plus_workers.splice(index, 1);
            },

            deleteInsteadWorker: function (index)
            {
                this.instead_workers.splice(index, 1);
            },

            deleteCard: function (index)
            {
                this.debit_cards.splice(index, 1);
                this.phone_icon_press_cnt.splice(index, 1);

                let app = this;

                this.plus_workers.forEach(function(item){
                    console.log(app.debit_cards);
                    console.log(item.card_index);
                    console.log(app.debit_cards.length);
                    if (item.card_index > app.debit_cards.length) {
                        item.card_index -= 1;
                    }
                });

                this.instead_workers.forEach(function(item){
                    console.log(app.debit_cards);
                    console.log(item.card_index);
                    console.log(app.debit_cards.length);
                    if (item.card_index > app.debit_cards.length) {
                        item.card_index -= 1;
                    }
                });
            },

            workerPhonePlaceholder: function (index)
            {
                return "Номер рабочего " + index.toString();
            },

            assignWorker: function ()
            {
                var app = this;

                this.$axios.post('/application/assign/worker',
                    {
                        application_id: this.application.id,
                        phone_whatsapp: this.worker.phone_whatsapp,
                        phone_call: this.worker.phone_call,
                        debit_cards: this.debit_cards,
                        plus_workers: this.plus_workers,
                        instead_workers: this.instead_workers,
                    }
                ).then(response => {
                    if (response.status == 200) {
                        //this.day_income = response.data.day_income;
                        this.error = false;
                        location.reload();
                    }
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    console.log(app.errors);
                    if (typeof app.errors.assign_worker_phone !== 'undefined') {
                        document.getElementById("assign_worker_error").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                });
            },

            endApp()
            {
                var app = this;

                this.$axios.post('/application/end', {
                    app_id: app.application.id,
                    work_hours: app.work_hours
                }).then(response => {
                    if (response.status == 200) {
                        //this.day_income = response.data.day_income;
                        this.error = false;
                        location.reload();
                    }
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    console.log(app.errors);
                    if (typeof app.errors.assign_worker_phone !== 'undefined') {
                        document.getElementById("assign_worker_error").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                });
            },

            readyToPayApp()
            {
                var app = this;

                this.$axios.post('/application/ready/to/pay/' + app.application.id, {}
                ).then(response => {
                    if (response.status == 200) {
                        this.error = false;
                        location.reload();
                    }
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    if (typeof app.errors.operations !== 'undefined') {
                        document.getElementById("operations").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    console.log(app.errors);
                });
            },

            rollback()
            {
                var app = this;

                this.$axios.post('/application/rollback/' + app.application.id, {}
                ).then(response => {
                    if (response.status == 200) {
                        this.error = false;
                        location.reload();
                    }
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    if (typeof app.errors.operations !== 'undefined') {
                        document.getElementById("operations").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    console.log(app.errors);
                });
            },

            save: function ()
            {
                var app = this;

                this.$axios.post('/application/save',
                    {
                        application_id: app.application.id,
                        application_income: app.application.income,
                        workers: app.workers,
                        child_workers: app.child_workers,
                        original_workers_copy: app.original_workers_copy,
                        original_child_workers_copy: app.original_child_workers_copy,
                    }
                ).then(response => {
                    if (response.status == 200) {
                        //this.day_income = response.data.day_income;
                        this.error = false;
                        location.reload();
                    }
                }).catch(function (error) {
                    app.error = true;
                    //console.log(error.response.data);
                    app.errors = error.response.data.errors;
                    if (typeof app.errors.operations !== 'undefined') {
                        document.getElementById("operations").scrollIntoView({block: "center", behavior: "smooth"});
                    }
                    console.log(app.errors);
                });
            },

            getWorkerCardBy: function (phone)
            {
                if (this.debit_cards[0].number != '' || phone == '' || phone.length < 16)
                    return;

                var app = this;
                this.$axios.get('/worker/card/' + phone)
                    .then(function (resp) {
                        app.debit_cards[0].number = resp.data.debit_card;
                        //console.log(app.debit_cards);
                        //console.log(resp.data);
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        alert("Не удалось загрузить");
                    });
            },

            openPhoneCall: function ()
            {
                this.phone_call_visible = !this.phone_call_visible;
                // plus button was clicked
                if (this.phone_call_visible == true) {
                    this.phonePlaceholder = 'Номер в Whatsapp';
                }
                else {
                    this.phonePlaceholder = 'Номер общий';
                }
            },

            setCardByPhone: function (index)
            {
                console.log(index);
                console.log(this.phone_icon_press_cnt);
                this.phone_icon_press_cnt[index]++;
                var pressCount = this.phone_icon_press_cnt[index];

                if (pressCount == 1) {
                    this.debit_cards[index].number = this.worker.phone_whatsapp;
                }
                else if (pressCount == 2) {
                    this.debit_cards[index].number = this.worker.phone_call;
                }
                else if (this.plus_workers.length > 0) {
                    if (pressCount - 3 <= this.plus_workers.length - 1) {
                        this.debit_cards[index].number = this.plus_workers[pressCount-3].phone;
                    }
                    else {
                        this.debit_cards[index].number = this.worker.phone_whatsapp;
                        pressCount = 1;
                    }
                }
                else if (this.instead_workers.length > 0) {
                    if (pressCount - 3 <= this.instead_workers.length - 1) {
                        this.debit_cards[index].number = this.instead_workers[pressCount-3].phone;
                    }
                    else {
                        this.debit_cards[index].number = this.worker.phone_whatsapp;
                        pressCount = 1;
                    }
                }
                else {
                    this.debit_cards[index].number = this.worker.phone_whatsapp;
                    pressCount = 1;
                }

                this.phone_icon_press_cnt[index] = pressCount;
            },

            onError: function (e) {
                alert('Failed to copy texts')
            }
        },

        updated()
        {
            let inputGroupBtn = document.querySelector('#debitCardGroupBtn');
            let width = (window.innerWidth > 0) ? window.innerWidth : screen.width;

            if (width < 550) {
                inputGroupBtn.classList.remove('input-group-btn');
            }
            //console.log('inputGroupBtn');
            //console.log(inputGroupBtn);
        },

        mounted()
        {
            let app = this;
            let id = app.$route.params.id;
            this.addDebitCard();

            this.$axios.get('/application/show/' + id)
                .then(function (resp) {
                    app.application = resp.data.application;
                    app.userPrivileges = resp.data.userPrivileges;

                    if (app.application.income == 0) {
                        //app.application.income = '';

                    }
                    app.application.id = id;
                    app.workers = resp.data.workers;
                    app.child_workers = resp.data.child_workers;
                    app.original_child_workers_copy = resp.data.child_workers_copy;

                    for (var i = 0; i < app.workers.length; i++) {
                        var worker = {
                            phone:      app.workers[i].phone,
                            debit_card: app.workers[i].debit_card,
                            money:      app.workers[i].money,
                            work_hours: app.workers[i].work_hours,
                            total_parent_work_hours: app.workers[i].total_parent_work_hours,
                            total_parent_money: app.workers[i].total_parent_money,
                            responsible_for_money:  app.workers[i].responsible_for_money,
                            tie_debit_card: app.workers[i].tie_debit_card
                        };
                        app.original_workers_copy[i] = worker;
                    }

                    app.client_phone += app.application.client_phone_number;
                    if ((app.application.state == 3) || (app.application.state == 4)) {
                        app.action_label = 'Отработало ';
                    }

                    app.state_labels = resp.data.state_labels;
                    app.state_colors = resp.data.state_colors;
                    app.CLOSED_ST = resp.data.CLOSED_ST;
                    app.ENDED_ST = resp.data.ENDED_ST;
                    app.READY_TO_PAY_ST = resp.data.READY_TO_PAY_ST;
                    app.PAYED_ST = resp.data.PAYED_ST;
                    app.NOT_PAYED_ST = resp.data.NOT_PAYED_ST;
                    app.PM_CASH = resp.data.PM_CASH;
                    app.PM_ACCOUNT = resp.data.PM_ACCOUNT;
                })
                .catch(function () {
                    alert("Не удалось загрузить")
                });
        }
    }
</script>


<style scoped>

  span.new-line {
    display: block;
  }

  .pointer {
    cursor: pointer;
  }

  .worker-table {
    width: 100%;
    text-align: center;
    font-size: 16px;
    border: 1px solid black;
  }

  @media (max-width: 600px) {
    .worker-table {
      width: 300%;
    }
  }

  @media (min-width: 600px) and (max-width: 800px) {
    .worker-table {
      width: 200%;
    }
  }

  @media (min-width: 800px) and (max-width: 1000px) {
    .worker-table {
      width: 150%;
    }
  }

  .worker-table .to-left {
    text-align: left;
  }

  .worker-table th {
    background-color: #dfdfdf;
    padding: 15px;
    text-align: center;
    border: 1px solid black;
  }

  .worker-table td {
    padding: 10px;
    border: 1px solid black;
  }

  .openAccordionRow {
    border: 2px solid seagreen;
  }

  button:disabled,
  button[disabled] {
    cursor: pointer;
  }

  .big-text {
    font-size: 150%;
  }

  .material-design-icon.icon-2x > .material-design-icon__svg {
    height: 18px;
    width: 18px;
  }

  .dc_span {
    background-color: blue;
    width: 20px;
  }
  .dc_label {
    width: 25px;
    text-align: justify;
    /*background-color: blue;
    position: absolute;
    padding: 0px 20px;*/
  }
  .col-sm-input1 {
    -ms-flex: 0 0 100%;
    flex: 0 0 100%;
    max-width: 100%;
  }

  .col-sm-input-phone {
    -ms-flex: 0 0 130px;
    flex: 0 0 130px;
    max-width: 130px;
  }

  .col-sm-input-debitcard {
    -ms-flex: 0 0 180px;
    flex: 0 0 180px;
    width: 230px;
    margin-right: 10px;
  }

  .col-sm-input-money {
    -ms-flex: 0 0 60px;
    flex: 0 0 60px;
    max-width: 60px;
  }

  .col-sm-input-workhours {
    -ms-flex: 0 0 40px;
    flex: 0 0 40px;
    max-width: 40px;
  }

  .col-sm-3minus {
    -ms-flex: 0 0 17%;
    flex: 0 0 17%;
    max-width: 17%;
  }

  .col-sm-3plus {
    -ms-flex: 0 0 26%;
    flex: 0 0 26%;
    max-width: 26%;
  }

  .col-sm-3plus2 {
    -ms-flex: 0 0 30%;
    flex: 0 0 30%;
    max-width: 30%;
  }

  .col-sm-3plus3 {
    -ms-flex: 0 0 35%;
    flex: 0 0 35%;
    max-width: 35%;
  }

  input:disabled {
    background-color: #F2F2F2;
    color: black;
    -webkit-text-fill-color: black;
    -webkit-opacity: 1;
  }

  .lightgrey {
    background-color: #F2F2F2;
  }
</style>