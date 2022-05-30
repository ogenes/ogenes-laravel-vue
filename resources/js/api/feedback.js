import request from '@/utils/request'

export function getOptions() {
  return request({
    url: '/api/feedback/options',
    method: 'get',
  })
}

export function add(data) {
  return request({
    url: '/api/feedback/add',
    method: 'post',
    data: {data},
  })
}
