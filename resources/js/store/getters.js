const getters = {
  sidebar: state => state.app.sidebar,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  account: state => state.user.account,
  email: state => state.user.email,
  mobile: state => state.user.mobile,
  name: state => state.user.name,
  introduction: state => state.user.introduction,
  roles: state => state.user.roles,
  menuMap: state => state.menu.menuMap,
  permission_routes: state => state.permission.routes,
  errorLogs: state => state.errorLog.logs,
};
export default getters
