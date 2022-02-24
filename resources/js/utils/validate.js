const chinese = /^[\u2E80-\u9FFF]+$/;
const idCard = /(^\\d{15}$)|(^\\d{18}$)|(^\\d{17}(\\d|X|x)$)/;
const email = /^.+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
const mobile = /^[1][3,4,5,7,8]\d{9}$/;
const password = /^.{6,32}$/;

export function validStr(type, str) {
  switch (type) {
    case "chinese":
      return chinese.test(str);
    case "idCard":
      return idCard.test(str);
    case "email":
      return email.test(str);
    case "mobile":
      return mobile.test(str);
    case "password":
      return password.test(str);
    default:
      return false;
  }
}
