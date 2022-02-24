import enLocale from 'element-ui/lib/locale/lang/en'

const userForm = {
  username: 'Username',
  email: 'Email',
  password: 'Password',
  confirmPassword: 'ConfirmPassword',
  rememberMe: 'Remember me',
  login: 'Login',
  register: 'Register',
  registered: 'Already Registered?',

};

const queryForm = {
  clear: 'clear',
  upload: 'upload',
  query: 'query',
  confirm: 'confirm',
  submit: 'submit',
  cancel: 'cancel',
  startTime: 'startTime',
  endTime: 'endTime',
  pleaseInput: 'Please input',

};

const en = {
  queryForm: queryForm,
  home: {
    title: 'Home',
    name: 'Home',
  },
  about: {
    title: 'About',
    name: 'About',
  },
  pictures: {
    title: 'Pictures',
    name: 'Pictures',
  },
  language: 'Chinese',
  user: {
    name: 'user',
    profile: {
      title: 'Profile',
      name: 'Profile',
      data: {
        email: 'Email',
        username: 'Username',
        mobile: 'Mobile',
        bind: ' bind',
        unbind: 'unbind',
      },
      bindForm: {
        mobile: 'Mobile',
        mobilePlaceholder: 'please input',
        code: 'Code',
        codePlaceholder: 'please input',
        getCode: 'send code',
        sent: 'has been sent',
      },
    },
    logout: {
      title: 'Logout',
      name: 'Logout',
    },
    login: {
      title: 'Login',
      name: 'Login',
      data: userForm,
    },
    register: {
      title: 'Register',
      name: 'Register',
      data: userForm,
    }
  },
  ...enLocale
};

export default en;
