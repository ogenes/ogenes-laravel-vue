import request from '@/utils/request'

export function getUserGroup() {
  return request({
    url: '/api/dashboard/userGroup',
    method: 'get',
  })
}
