import Cookies from 'js-cookie';

const TokenKey = 'AUTH-TOKEN';
const EXP = 30;

export function getToken() {
  let token = Cookies.get(TokenKey)
  if (!token) {
    token = localStorageGetItem(TokenKey, EXP)
  }
  return token
}

export function setToken(token, persistent = false) {
  if (persistent) {
    Cookies.set(TokenKey, token);
    return localStorageSetItem(TokenKey, token)
  } else {
    return Cookies.set(TokenKey, token)
  }
}

export function removeToken() {
  localStorageRemoveItem(TokenKey)
  return Cookies.remove(TokenKey)
}

export function localStorageSetItem(key, value) {
  const curTime = new Date().getTime()
  localStorage.setItem(key, JSON.stringify({data: value, time: curTime}))
}

export function localStorageGetItem(key, exp) {
  const data = localStorage.getItem(key)
  const dataObj = JSON.parse(data)
  if (dataObj === null || new Date().getTime() - dataObj.time > exp * 864e+5) {
    return null
  } else {
    return dataObj.data
  }
}

export function localStorageRemoveItem(key) {
  localStorage.removeItem(key)
}
