import axios from "axios"

export default {
    setUser: function (obj) {
        return axios.post('/api/user', obj);
    },

    getUsers: function () {
        return axios.get('/api/user', { headers: { 'Accept': 'application/json' } });
    },

    guess: function (number) {
        let obj = {answer: number}
        return axios.post('/api/guess', obj)
    },

    sendGuess: function (obj) {
        return axios.patch('/api/sendguess', obj)
    }
}