import Layout from '@/layout'

const systemRouter = {
  path: '/system',
  component: Layout,
  redirect: '/system/department',
  alwaysShow: true, // will always show the root menu
  name: 'System',
  meta: {},
  children: [
    {
      path: 'department',
      component: () => import('@/views/system/department'),
      name: 'DepartmentManage',
      meta: {}
    },
    {
      path: 'user',
      component: () => import('@/views/system/user'),
      name: 'UserManage',
      meta: {}
    },
    {
      path: 'menu',
      component: () => import('@/views/system/menu'),
      name: 'MenuManage',
      meta: {}
    },
  ]
};

export default systemRouter
