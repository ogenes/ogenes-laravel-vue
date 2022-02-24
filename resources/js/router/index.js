import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);
export const constantRoutes = [
  {
    path: '/',
    name: 'Home',
    meta: {
      title: 'Home',
      requireAuth: false
    },
    component: () => import('@/views/index')
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
  {
    path: '/Users/register',
    name: 'Users-register',
    meta: {
      title: 'Register',
      requireAuth: false
    },
    component: () => import('@/views/user/register')
  },
  {
    path: '/Users/login',
    name: 'Users-login',
    meta: {
      title: 'Login',
      requireAuth: false
    },
    component: () => import('@/views/user/login')
  },
  {
    path: '/Users/active',
    name: 'Users-active',
    meta: {
      title: 'Active',
      requireAuth: false
    },
    component: () => import('@/views/user/active')
  },
  {
    path: '/Users/info',
    name: 'Users-info',
    meta: {
      title: '个人中心',
      requireAuth: true
    },
    component: () => import('@/views/user/info')
  },
];
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

