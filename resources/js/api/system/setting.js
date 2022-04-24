import request from '@/utils/request'

export function getAll() {
  return request({
    url: '/api/setting/getAll',
    method: 'get',
  })
}

export function getOne(data) {
  return request({
    url: '/api/setting/getOne',
    method: 'get',
    params: {data},
  })
}

export function save(data) {
  return request({
    url: '/api/setting/save',
    method: 'post',
    data: {data},
  })
}
