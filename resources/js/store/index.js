import Vue from 'vue';
import Vuex from 'vuex';
import idbs from './indexedDBService';


Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        address: 'www.leningrad www.ru'
    },
    actions: {
        checkStorage ({ state, commit }) {
            state.dataFields.forEach(async field => {
                try {
                    let data = await idbs.checkStorage(field)
                    // IndexedDB did not find the data, try localStorage
                    if (data === undefined) data = ls.checkStorage(field)
                    // LocalStorage did not find the data, reset it
                    if (data === null) data = []
                    commit('setState', { field, data })
                } catch (e) {
                    // The value in storage was invalid or corrupt so just set it to blank
                    commit('setState', { field, data: [] })
                }
            })
        },
        async saveTodos ({ state }) {
            try {
                await Promise.all(state.dataFields.map(field => {
                    return idbs.saveToStorage(field, state[field])
                }))
            }
            catch (e) {
                state.dataFields.forEach(field => {
                    return ls.saveToStorage(field, state[field])
                })
            }
        }
    }
})
