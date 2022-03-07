import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/api/user/login',
    method: 'post',
    data
  })
}

export function getInfo() {
  return request({
    url: '/api/user/info',
    method: 'get',
  })
}

export function logout() {
  return request({
    url: '/api/user/logout',
    method: 'post'
  })
}

export function getList(data) {
  return request({
    url: '/api/user/list',
    method: 'get',
    params: { data }
  })
}

export const USER_STATUS_OPTION = [
  {
    label: '正常',
    value: 1,
  },
  {
    label: '禁用',
    value: 0,
  }
];
