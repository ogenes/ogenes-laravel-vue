import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/api/actionLog/list',
    method: 'get',
    params: {data},
  })
}

export function getOptions(data) {
  return request({
    url: '/api/actionLog/options',
    method: 'get',
    params: {data},
  })
}
