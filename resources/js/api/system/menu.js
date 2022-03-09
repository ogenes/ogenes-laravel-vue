import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/api/menu/list',
    method: 'get',
    params: {data},
  })
}

export function getOptions() {
  return request({
    url: '/api/menu/options',
    method: 'get',
  })
}

export function save(data) {
  return request({
    url: '/api/menu/save',
    method: 'post',
    data: {data},
  })
}

export function remove(data) {
  return request({
    url: '/api/menu/remove',
    method: 'post',
    data: {data},
  })
}

export const MENU_TYPE_OPTION = {
  1: {
    label: '目录',
    class: 'info',
  },
  2: {
    label: '菜单',
    class: 'success',
  },
  3: {
    label: '按钮',
    class: 'warning',
  },
  4: {
    label: '接口',
    class: 'danger',
  },
  5: {
    label: '数据',
    class: 'primary',
  },
};
