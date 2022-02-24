import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Layout from '@/layout'

export const constantRoutes = [
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/index'),
        name: 'Home',
        meta: {
          title: 'Home',
          requireAuth: false
        }
      },
      {
        path: '/About',
        name: 'About',
        meta: {
          title: 'About',
          requireAuth: false
        },
        component: () => import('@/views/about')
      },
      {
        path: '/Pictures',
        name: 'Pictures',
        meta: {
          title: 'Pictures',
          requireAuth: true
        },
        component: () => import('@/views/pictures')
      },
    ]
  },
  {
    path: '/Users',
    component: Layout,
    redirect: '/Users/register',
    children: [
      {
        path: 'register',
        name: 'Users-register',
        meta: {
          title: 'Register',
          requireAuth: false
        },
        component: () => import('@/views/user/register')
      },
      {
        path: 'login',
        name: 'Users-login',
        meta: {
          title: 'Login',
          requireAuth: false
        },
        component: () => import('@/views/user/login')
      },
      {
        path: 'active',
        name: 'Users-active',
        meta: {
          title: 'Active',
          requireAuth: false
        },
        component: () => import('@/views/user/active')
      },
      {
        path: 'info',
        name: 'Users-info',
        meta: {
          title: '个人中心',
          requireAuth: true
        },
        component: () => import('@/views/user/info')
      },
    ]
  },
];

export const asyncRoutes = [];

const createRouter = () => new VueRouter({
  mode: 'history',
  routes: constantRoutes
});


const router = createRouter();

export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher // reset router
}

export default router

