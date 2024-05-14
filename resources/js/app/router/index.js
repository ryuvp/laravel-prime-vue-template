import { createRouter, createWebHistory } from 'vue-router'
import Layout from '../views/layout/Layout.vue'

export const constantRoutes = [
    {
        path: '/intranet',
        component: Layout,
        redirect: '/intranet/dashboard',
        children: [
            {
                path: 'dashboard',
                component: () => import('../views/dashboard/index.vue'),
                name: 'dashboard',
                meta: { title: 'Dashboard', icon: 'dashboard', noCache: true }
            }
        ]
    }, {
        path: '/intranet/login',
        component: () => import('../views/login/index.vue'),
        hidden: true
    }
]

const router = createRouter({
    routes: constantRoutes,
    history: createWebHistory()
})

export default router
