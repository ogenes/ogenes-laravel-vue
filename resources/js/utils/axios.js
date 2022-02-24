import axios from 'axios'
import Cookies from 'js-cookie';
import {getToken} from '@/utils/auth'
import {guid} from '@/utils/index'

// create an axios instance
const service = axios.create({
  withCredentials: true, // send cookies when cross-domain requests
  timeout: 2 * 60 * 1000 // request timeout, 单位 ms, 暂定 5 分钟
});

service.interceptors.request.use(
  config => {
    // do something before request is sent
    if (getToken()) {
      config.headers['Authorization'] = getToken()
    }
    const key = 'UNIQ-REQ';
    if (!Cookies.get(key)) {
      Cookies.set(key, guid());
    }

    return config
  },
  error => {
    // do something with request error
    console.log(error); // for debug
    return Promise.reject(error)
  }
);

service.interceptors.response.use(function (response) {
  if (typeof response.data === 'string') {
    throw new Error(response.data);
  }
  if (response.data.hasOwnProperty('code') && response.data.code !== 0 && response.data.hasOwnProperty('msg')) {
    throw new Error(response.data.msg);
  }
  return response
}, function (error) {
  return Promise.reject(error)
});

export default service
