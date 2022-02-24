import zhLocale from 'element-ui/lib/locale/lang/zh-CN'

const userForm = {
  username: '请输入用户名',
  email: '请输入邮箱',
  password: '请输入密码',
  confirmPassword: '再次输入密码',
  rememberMe: '记住我',
  login: '登录',
  register: '注册',
  registered: '已注册?',
};

const queryForm = {
  clear: '清空文件',
  upload: '上传到服务器',
  query: '查询',
  confirm: '确定',
  submit: '提交',
  cancel: '取消',
  startTime: '开始时间',
  endTime: '结束时间',
  pleaseInput: '请输入',
};

const cn = {
  queryForm: queryForm,
  home: {
    title: '首页',
    name: '首页',
  },
  about: {
    title: '说明',
    name: '说明',
  },
  pictures: {
    title: '图片',
    name: '图片',
  },
  language: '英文',
  user: {
    name: '用户',
    profile: {
      title: '个人中心',
      name: '个人中心',
      data: {
        email: '邮箱',
        username: '用户名',
        mobile: '手机号',
        bind: ' 绑定',
        unbind: '解绑',
      },
      bindForm: {
        mobile: '手机号',
        mobilePlaceholder: '请输入手机号',
        code: '验证码',
        codePlaceholder: '请输入验证码',
        getCode: '获取验证码',
        sent: '已发送',
      },
    },
    logout: {
      title: '退出',
      name: '退出',
    },
    login: {
      title: '登录',
      name: '登录',
      data: userForm,
    },
    register: {
      title: '注册',
      name: '注册',
      data: userForm,
    },
  },
  ...zhLocale
};

export default cn;
