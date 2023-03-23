// File so that I can access my endpoints in my Vue files

import axios from "axios"

export default {
    setUser: function (obj) {
        return axios.post('/api/user', obj);
    },

    getUsers: function () {
        return axios.get('/api/user');
    },

    clearSession: function () {
        return axios.get('/api/clear');
    },

    getSession: function() {
        return axios.get('/api/userSession')
    },

    guess: function (number) {
        let obj = {answer: number}
        return axios.post('/api/guess', obj)
    },

    sendGuess: function (obj) {
        return axios.patch('/api/sendguess', obj)
    },
}