<template>
    <div class="row col-xs-12 form-group">
        <radio name="radiobtn_dispatcher"
               checked
               value="1" v-model="dispatcher_id"
        >
            Глебас
        </radio>
        <radio name="radiobtn_dispatcher"
               checked
               value="2" v-model="dispatcher_id"
        >
            Димас
        </radio>
        <radio name="radiobtn_dispatcher"
               checked
               value="4" v-model="dispatcher_id"
        >
            Настя
        </radio>
    </div>
</template>

<script>
    export default {
        name: "DispatcherRadioList",
        props: ['dispatcherId'],
        data () {
            return {
                dispatcher_id: undefined
            }
        },
        mount() {
            this.dispatcher_id = dispatcherId;
            this.$emit('dispatcher-id', this.dispatcher_id);
        }
    }
</script>

<style scoped>

</style>