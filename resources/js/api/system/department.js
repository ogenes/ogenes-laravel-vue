import request from '@/utils/request'

export function getList() {
  return request({
    url: '/api/department/list',
    method: 'get',
  })
}

export function save(data) {
  return request({
    url: '/api/department/save',
    method: 'post',
    data
  })
}

export function remove(data) {
  return request({
    url: '/api/department/remove',
    method: 'post',
    data
  })
}
