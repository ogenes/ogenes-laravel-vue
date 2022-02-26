import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/api/tmp/login',
    method: 'post',
    data
  })
}

export function getInfo(token) {
  return request({
    url: '/api/tmp/userInfo',
    method: 'get',
    params: { token }
  })
}

export function logout() {
  return request({
    url: '/api/tmp/logout',
    method: 'post'
  })
}
