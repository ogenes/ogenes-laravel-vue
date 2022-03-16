import request from '@/utils/request'

export function getOptions(data) {
  return request({
    url: '/api/data-permission/options',
    method: 'get',
    params: {data},
  })
}

export function getList(data) {
  return request({
    url: '/api/data-permission/list',
    method: 'get',
    params: {data},
  })
}


export function save(data) {
  return request({
    url: '/api/data-permission/save',
    method: 'post',
    data: {data},
  })
}

export function remove(data) {
  return request({
    url: '/api/data-permission/remove',
    method: 'post',
    data: {data},
  })
}
