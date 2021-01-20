<template>
    <div id="app">
        <section class="select-skills">
            <div class="skills-header" style="text-align: center">
                Укажите кем работали и какие у вас навыки
            </div>

            <div class="worker-skills" v-for="(category, index) in occ_categories">

                <div class="message" v-bind:class="{ 'is-primary': occupation_acc_is_open[category.id],
                    'is-closed': !occupation_acc_is_open[category.id] }"
                >

                    <div class="category message-header"
                         @click="toggleOccupationAccordion(category.id, false)">{{category.name}}
                    </div>

                    <div class="message-body">
                        <div class="message-content">
                            <form action="">

                                <div class="message subcat" v-for="occupation in occupations[category.parent_id]"
                                     v-bind:class="{ 'is-primary': occupation_acc_is_open[occupation.id],
                                     'is-closed': !occupation_acc_is_open[occupation.id] }"
                                >
                                    <div v-if="occupation_is_complex[occupation.id] == false">
                                        <div class="message-header">
                                            <div class="occu_row">
                                                <div class="occu_input_wrap" style="display: inline-block">
                                                    <input class="box-wash"
                                                           type="checkbox" id="horns"
                                                           name="horns"
                                                           :checked="is_checked[occupation.id]"
                                                           @click="toggleOccupationAccordion(occupation.id, true)">
                                                </div>
                                                <div class="occu_label"
                                                     style="display: inline-block"
                                                     @click="toggleOccupationAccordion(occupation.id, true)">
                                                        {{occupation.name}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="message-body"
                                             v-for="skill in skills[occupation.id]"
                                        >
                                            <div v-if="skill_is_complex[skill.id] == true">
                                                <div class="occu_row"
                                                     style="display: inline-block"
                                                     :class="{ [skill_class_names[skill.id]]: true }"
                                                >
                                                    {{skill.name}} <span>{{ ((skill_captures[skill.id])) }}</span>
                                                </div>

                                                <div v-for="nskill in nested_skills[skill.id]"
                                                     style="padding-left:7%;"
                                                     @click="selectNestedSkillLevel(nskill.id, nskill.parent_id)"
                                                     :style="{ backgroundColor: skill_colors[nskill.id] }"
                                                     :class="{ [skill_class_names[nskill.id]]: true }"
                                                >
                                                    {{nskill.name}} <span>{{ ((skill_captures[nskill.id])) }}</span>
                                                </div>
                                            </div>
                                            <div v-else class="ordinary_skill"
                                                 style="display: inline-block"
                                                 @click="selectLevel(skill.id)"
                                                 :style="{ backgroundColor: skill_colors[skill.id] }"
                                                 :class="{ [skill_class_names[skill.id]]: true }"
                                            >
                                                {{skill.name}} <span>{{ ((skill_captures[skill.id])) }}</span>
                                            </div>
                                        </div>
                                    </div>


                                    <div v-if="occupation_is_complex[occupation.id] == true">
                                        <div class="complex_occupation message-header">
                                            <div class="occu_row">
                                                <div class="occu_label"
                                                     style="display: inline-block; background-color: #f2d63c"
                                                     @click="toggleOccupationAccordion(occupation.id, false)">
                                                        {{occupation.name}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="complex_occupation message-body"
                                            v-for="nocc in nested_occupations[occupation.id]"
                                        >
                                            <div class="message"
                                                 v-bind:class="{ 'is-primary': occupation_acc_is_open[nocc.id],
                                                 'is-closed': !occupation_acc_is_open[nocc.id] }"
                                            >
                                                <div class="message-header">
                                                    <div class="occu_row">
                                                        <div class="occu_input_wrap" style="display: inline-block">
                                                            <input class="box-wash"
                                                                   type="checkbox" id="horns"
                                                                   name="horns"
                                                                   :checked="is_checked[nocc.id]"
                                                                   @click="toggleOccupationAccordion(nocc.id, true)">
                                                        </div>
                                                        <div class="occu_label"
                                                             style="display: inline-block"
                                                             @click="toggleOccupationAccordion(nocc.id, true)">
                                                                {{nocc.name}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="message-body"
                                                     v-for="skill in skills[nocc.id]"
                                                >
                                                    <div v-if="skill_is_complex[skill.id] == true">
                                                        <div class="occu_row"
                                                             style="display: inline-block"
                                                             :class="{ [skill_class_names[skill.id]]: true }"
                                                        >
                                                            {{skill.name}} <span>{{ ((skill_captures[skill.id])) }}</span>
                                                        </div>

                                                        <div v-for="nskill in nested_skills[skill.id]"
                                                             style="padding-left:7%;"
                                                             @click="selectNestedSkillLevel(nskill.id, nskill.parent_id)"
                                                             :style="{ backgroundColor: skill_colors[nskill.id] }"
                                                             :class="{ [skill_class_names[nskill.id]]: true }"
                                                        >
                                                            {{nskill.name}} <span>{{ ((skill_captures[nskill.id])) }}</span>
                                                        </div>
                                                    </div>
                                                    <div v-else class="ordinary_skill"
                                                         style="display: inline-block"
                                                         @click="selectLevel(skill.id)"
                                                         :style="{ backgroundColor: skill_colors[skill.id] }"
                                                         :class="{ [skill_class_names[skill.id]]: true }"
                                                    >
                                                        {{skill.name}} <span>{{ ((skill_captures[skill.id])) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <br>
        <div style="padding-left:44.8%">
            <router-link to="/offer" class="btn btn-default">Назад</router-link>
            <button class="btn btn-success" v-on:click="saveUserOccupationsAndSkills">Далее</button>
        </div>
    </div>
</template>



<script>
    import axios from 'axios';
    import route from '../route';

    export default {
        data() {
            return {
                isActive : 1,

                skills: [],
                nested_skills: [],
                skill_is_complex: [],
                skill_levels: [],
                skill_class_names : [],
                skill_colors : [],
                skill_captures: [],

                level1: "",

                activetab: 1,

                occ_categories : [],
                occupations : [],
                nested_occupations : [],
                occupation_is_complex: [],
                occupation_acc_is_open: [],
                skill_acc_is_open : [],
                is_checked : [],
                checked_occupations: []
            }
        },

        computed : {

        },

        methods: {
            toggleOccupationAccordion : function (id, need_save) {
                this.occupation_acc_is_open[id] = !this.occupation_acc_is_open[id];
                this.is_checked[id] = !this.is_checked[id];
                if (this.is_checked[id] && need_save) {
                    this.checked_occupations.push(id);
                }
            },

            selectLevel: function (skill_id) {
                if (this.skill_levels[skill_id] == -1) {
                    this.skill_captures[skill_id] = "(Есть опыт)";
                    this.skill_colors[skill_id] = '#4dbc3c';
                    this.skill_levels[skill_id] = 1;
                }
                else if (this.skill_levels[skill_id] == 0) {
                    this.skill_captures[skill_id] = "(Есть опыт)";
                    this.skill_colors[skill_id] = '#4dbc3c';
                    this.skill_levels[skill_id] = 1;
                }
                else  if (this.skill_levels[skill_id] == 1) {
                    this.skill_captures[skill_id] = "(Профессионал)";
                    this.skill_colors[skill_id] = '#1FCAE5';
                    this.skill_levels[skill_id] = 2;
                }
                else if (this.skill_levels[skill_id] == 2) {
                    this.skill_captures[skill_id] = "(Нет опыта)";
                    this.skill_colors[skill_id] = '#e5451b';
                    this.skill_levels[skill_id] = 0;
                }
            },

            selectNestedSkillLevel: function (skill_id, parent_skill_id) {
                this.selectLevel(skill_id);

                function objectLength(obj) {
                    var result = 0;
                    for(var prop in obj) {
                        if (obj.hasOwnProperty(prop)) {
                            // or Object.prototype.hasOwnProperty.call(obj, prop)
                            result++;
                        }
                    }
                    return result;
                }

                //no_xp - количество навыков с "нет опыта"
                //has_xp - количество навыков с "есть опыт"
                //prof - количество навыков с "профессионал"
                //undef - количество навыков с "не указано"
                var lvls = {no_xp:0, has_xp:0, prof: 0, undef: 0};
                var complex_skill = this.nested_skills[parent_skill_id];
                var nested_skills_count = objectLength(complex_skill);

                for (var i = 0; i < nested_skills_count; i++) {
                    if (this.skill_levels[complex_skill[i].id] == 0) {
                        lvls.no_xp++;
                    }
                    else if (this.skill_levels[complex_skill[i].id] == 1) {
                        lvls.has_xp++;
                    }
                    else if (this.skill_levels[complex_skill[i].id] == 2) {
                        lvls.prof++;
                    }
                    else {
                        lvls.undef++;
                    }
                }

                if (lvls.undef == 0) {
                    if (lvls.prof == nested_skills_count) {
                        this.skill_captures[parent_skill_id] = "(Профессионал)";
                    }
                    else if (lvls.no_xp == nested_skills_count) {
                        this.skill_captures[parent_skill_id] = "(Нет опыта)";
                    }
                    else if ((lvls.has_xp > 0) || (
                        (lvls.prof > 0) && (lvls.no_xp > 0) )
                    ) {
                        this.skill_captures[parent_skill_id] = "(Есть опыт)";
                    }
                }
                else {
                    this.skill_captures[parent_skill_id] = "(Еще не всё)";
                }
            },

            getData: function () {
                axios.get(route('worker_occs_skills.index'))
                    .then(responce => {
                        this.occ_categories = responce.data.categories;
                        this.occupations = responce.data.occupations;
                        this.nested_occupations = responce.data.nested_occupations;
                        this.occupation_is_complex = responce.data.occupation_is_complex;
                        this.occupation_acc_is_open = responce.data.occupation_acc_is_open;
                        this.skill_acc_is_open = responce.data.skill_acc_is_open;
                        this.skills = responce.data.skills;
                        this.nested_skills = responce.data.nested_skills;
                        this.skill_is_complex = responce.data.skill_is_complex;
                        this.skill_levels = responce.data.skill_levels;
                        this.skill_colors = responce.data.skill_colors;
                        this.skill_class_names = responce.data.skill_class_names;
                        this.skill_captures = responce.data.skill_captures;
                        this.is_checked = responce.data.chkbox_is_checked;
                        console.log(responce.data);
                    });
            },

            saveUserOccupationsAndSkills() {
                var app = this;
                this.$axios.post(route('worker_occs_skills.store'),
                    {
                        skill_levels: this.skill_levels,
                        checked_occupations: this.checked_occupations
                    }
                ).then(function (resp) {
                    app.$router.push({
                        name: 'confirm-worker-xp',
                        params: {id: 1}
                    });
                }).catch(function (resp) {
                    console.log(resp);
                    alert("Не удалось создать");
                });
            }

        },
        mounted () {
            this.getData();
        }
    };
    /*beforeCreate: function(){
        this.$auth.logout();
        console.log('as')
    },*/
</script>

<style scoped>

</style>