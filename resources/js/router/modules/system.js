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
      path: 'permission',
      component: () => import('@/views/system/permission/index'),
      name: 'PermissionManage',
      meta: {
        title: '权限管理',
        icon: 'permission',
      },
      children: [
        {
          path: 'menu',
          component: () => import('@/views/system/permission/menu'),
          name: 'PermissionMenuManage',
          meta: {
            title: '菜单权限',
            icon: 'menu',
          }
        },
        {
          path: 'data',
          component: () => import('@/views/system/permission/data'),
          name: 'PermissionDataManage',
          meta: {
            title: '数据权限',
            icon: 'data',
          }
        },
      ]
    },
  ]
};

export default systemRouter
