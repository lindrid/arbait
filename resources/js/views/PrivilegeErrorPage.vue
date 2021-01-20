<template>
    <div>
        <div class="alert alert-danger">
            <p class="big-text">{{ error_msg }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "login",
        data(){
            return {
                error_msg: ''
            }
        },
        methods:
        {

        },
        mounted ()
        {
            this.error_msg = this.$window.errorMsg;
            console.log(this.$window.errorMsg);
            console.log(this.$window.route);
        }
    }
</script>

<style scoped>
  .big-text {
    font-size: 150%;
  }
</style>