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
      meta: {
        title: '部门管理',
        icon: 'department',
      }
    },
    {
      path: 'user',
      component: () => import('@/views/system/user'),
      name: 'UserManage',
      meta: {
        title: '用户管理',
        icon: 'user',
      }
    },
    {
      path: 'menu',
      component: () => import('@/views/system/menu'),
      name: 'MenuManage',
      meta: {
        title: '菜单管理',
        icon: 'menu',
      }
    },
  ]
};

export default systemRouter
