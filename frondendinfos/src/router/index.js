import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        redirect: '/register'
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../Views/register.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes 
})

export default router