<template>
    <div class="home-wrapper">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name"
                   required v-model="newItem.name" placeholder=" Enter some name">
        </div>
        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" class="form-control" id="age" name="age"
                   required v-model="newItem.age" placeholder=" Enter your age">
        </div>
        <div class="form-group">
            <label for="profession">Profession:</label>
            <input type="text" class="form-control" id="profession" name="profession"
                   required v-model="newItem.profession" placeholder=" Enter your profession">
        </div>

        <button class="btn btn-primary" @click.prevent="createItem()" id="name" name="name">
            <span class="glyphicon glyphicon-plus"></span> ADD
        </button>
        <p class="text-center alert alert-danger" v-bind:class="{ hidden: hasError }">Please fill all fields!</p>

        <div class="table table-borderless" id="table">
            <table class="table table-borderless" id="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Profession</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tr v-for="item in items">
                    <td>@{{ item.id }}</td>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.age }}</td>
                    <td>@{{ item.profession }}</td>

                    <td id="show-modal" @click="showModal=true; setVal(item.id, item.name, item.age, item.profession)"  class="btn btn-info" ><span
                            class="glyphicon glyphicon-pencil"></span></td>
                    <td @click.prevent="deleteItem(item)" class="btn btn-danger"><span
                            class="glyphicon glyphicon-trash"></span></td>
                </tr>
            </table>
        </div>

        <modal v-if="showModal" @close="showModal=false">
            <h3 slot="header">Edit Item</h3>
            <div slot="body">

                <input type="hidden" disabled class="form-control" id="e_id" name="id"
                       required  :value="this.e_id"> Name: <input type="text" class="form-control" id="e_name" name="name"
                             required  :value="this.e_name">
                Age: <input type="number" class="form-control" id="e_age" name="age"
                            required  :value="this.e_age">
                Profession: <input type="text" class="form-control" id="e_profession" name="profession"
                                   required  :value="this.e_profession">


            </div>
            <div slot="footer">
                <button class="btn btn-default" @click="showModal = false">
                    Cancel
                </button>

                <button class="btn btn-info" @click="editItem()">
                    Update
                </button>
            </div>
        </modal>

    </div>
</template>

<script>
    export default {
        name: "create",
        data() {
            return {
                items: [],
                hasError: true,
                newItem: { 'name': '','age': '','profession': '' },
            }
        },
        methods: {
            createItem() {
                var _this = this;
                var input = this.newItem;

                if (input['name'] == '' || input['age'] == '' || input['profession'] == '' ) {
                    this.hasError = false;
                } else {
                    this.hasError = true;
                    this.$axios.post('/vueitems', input).then(function (response) {
                        _this.newItem = { 'name': '' };
                        _this.getVueItems();
                    });
                }
            },
            getVueItems() {
                var _this = this;

                this.$axios.get('/vueitems').then(function (response) {
                    _this.items = response.data;
                });
            },
            setVal(val_id, val_name, val_age, val_profession) {
                this.e_id = val_id;
                this.e_name = val_name;
                this.e_age = val_age;
                this.e_profession = val_profession;
            },
            editItem(){
                var i_val = document.getElementById('e_id');
                var n_val = document.getElementById('e_name');
                var a_val = document.getElementById('e_age');
                var p_val = document.getElementById('e_profession');

                this.$axios.post('/edititems/' + i_val.value, {val_1: n_val.value, val_2: a_val.value,val_3: p_val.value })
                    .then(response => {
                        this.getVueItems();
                        this.showModal=false
                    });
            },
            deleteItem(item) {
                var _this = this;
                this.$axios.post('/vueitems/' + item.id).then(function (response) {
                    _this.getVueItems();
                    _this.hasDeleted = false
                });
            }
        },
        mounted() {
            this.getVueItems();
        },
    }
</script>

<style scoped>

</style>