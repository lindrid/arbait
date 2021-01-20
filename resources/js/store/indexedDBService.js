import { openDB } from 'idb'

const dbPromise = _ => {
    if (!('indexedDB' in window)) {
        throw new Error('Browser does not support IndexedDB')
    }
    // if installed from npm use 'openDB'
    return openDB('VueTodoDB', 1, upgradeDb => {
        if (!upgradeDb.objectStoreNames.contains('todos')) {
            upgradeDb.createObjectStore('todos')
        }
        if (!upgradeDb.objectStoreNames.contains('completed')) {
            upgradeDb.createObjectStore('completed')
        }
    })
}