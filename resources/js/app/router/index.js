import { createRouter, createWebHistory } from 'vue-router'
import Layout from '../views/layout/Layout.vue'
import { isLogged } from '@/app/utils/auth'

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
