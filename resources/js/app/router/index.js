import { createRouter, createWebHistory } from 'vue-router'
import Layout from '../views/layout/Layout.vue'
import { isLogged } from '@/app/utils/auth'

export const constantRoutes = [
    {
        path: '/intranet/login',
        component: () => import('../views/login/index.vue'),
        hidden: true
    },
    {
        path: '/intranet',
        component: Layout,
        redirect: '/intranet/dashboard',
        children: [
            {
                path: 'dashboard',
                component: () => import('../views/dashboard/index.vue'),
                name: 'dashboard',
                meta: { title: 'Dashboard'}
            }
        ]
    },
    {
        path: '/intranet/users',
        component: Layout,
        children: [
            {
                path: '/intranet/users',
                component: () => import('../views/manage/users/index.vue'),
                name: 'users',
                meta: { title: 'users'}
            }
        ]
    },
    {
        path: '/intranet/roles',
        component: Layout,
        children: [
            {
                path: '/intranet/roles',
                component: () => import('../views/manage/roles/index.vue'),
                name: 'roles',
                meta: { title: 'roles'}
            }
        ]
    },
    {
        path: '/intranet/permissions',
        component: Layout,
        children: [
            {
                path: '/intranet/permissions',
                component: () => import('../views/manage/permissions/index.vue'),
                name: 'permissions',
                meta: { title: 'permissions'}
            }
        ]
    },
    {
        path: '/intranet/profile',
        component: Layout,
        children: [
            {
                path: '/intranet/profile',
                component: () => import('../views/profile/profile/index.vue'),
                name: 'profile',
                meta: { title: 'profile'}
            }
        ]
    },
]

export const asyncRoutes = [
    //chartsRoutes,
    //adminRoutes,
    //errorRoutes,
    { path: '/:pathMatch(.*)*', name: 'NotFound', redirect: '/404', hidden: true }
  ]

const router = createRouter({
    routes: constantRoutes,
    history: createWebHistory()
})

router.beforeEach((to, from, next) => {
    if (isLogged()) {
        next()
    } else {
        if (to.path !== '/intranet/login') {
            next('/intranet/login')
        } else {
            next()
        }
    }
})

export function resetRouter() {
    const asyncRouterNameArr = asyncRoutes.map((mItem) => mItem.name)
    asyncRouterNameArr.forEach((name) => {
      if (router.hasRoute(name)) {
        router.removeRoute(name)
      }
    })
  }

export default router
