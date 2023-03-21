import { createRouter, createWebHistory } from 'vue-router';
import Home from './views/HomePage.vue';
import Game from './views/PlayGame.vue';
import Error from "./views/404.vue";

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/game',
        name: 'Game',
        component: Game,
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/:pathMatch(.*)*',
        name: "404",
        component: Error
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router