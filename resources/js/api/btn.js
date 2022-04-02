export const BTN_DEPT_ADD = 'DepartmentManageAdd';
export const BTN_DEPT_EDIT = 'DepartmentManageEdit';
export const BTN_DEPT_DEL = 'DepartmentManageDel';
export const BTN_MAP_DEPT = {
  [BTN_DEPT_ADD]: '新增',
  [BTN_DEPT_EDIT]: '编辑',
  [BTN_DEPT_DEL]: '删除',
};

export const BTN_USER_ADD = 'UserManageAdd';
export const BTN_USER_EDIT = 'UserManageEdit';
export const BTN_USER_RESET = 'UserManageReset';
export const BTN_USER_STATUS = 'UserManageStatus';
export const BTN_USER_ROLE = 'UserManageEditRole';
export const BTN_MAP_USER = {
  [BTN_USER_ADD]: '新增',
  [BTN_USER_EDIT]: '编辑',
  [BTN_USER_RESET]: '重置密码',
  [BTN_USER_STATUS]: '切换状态',
  [BTN_USER_ROLE]: '编辑',
};

export const BTN_MENU_ADD = 'PermissionMenuManageAdd';
export const BTN_MENU_EDIT = 'PermissionMenuManageEdit';
export const BTN_MENU_DEL = 'PermissionMenuManageDel';
export const BTN_MAP_MENU = {
  [BTN_MENU_ADD]: '新增',
  [BTN_MENU_EDIT]: '编辑',
  [BTN_MENU_DEL]: '删除',
};

export const BTN_ROLE_ADD = 'RoleManageAdd';
export const BTN_ROLE_EDIT = 'RoleManageEdit';
export const BTN_ROLE_STATUS = 'RoleManageStatus';
export const BTN_ROLE_MENU = 'RoleManageMenu';
export const BTN_MAP_ROLE = {
  [BTN_ROLE_ADD]: '新增',
  [BTN_ROLE_EDIT]: '编辑',
  [BTN_ROLE_STATUS]: '切换状态',
  [BTN_ROLE_MENU]: '修改菜单',
};

export const BTN_DICT_ADD = 'DictManageAdd';
export const BTN_DICT_EDIT = 'DictManageEdit';
export const BTN_DICT_DEL = 'DictManageDel';
export const BTN_DICT_DATA_ADD = 'DictManageAddData';
export const BTN_DICT_DATA_EDIT = 'DictManageEditData';
export const BTN_DICT_DATA_DEL = 'DictManageDelData';
export const BTN_MAP_DICT = {
  [BTN_DICT_ADD]: '新增',
  [BTN_DICT_EDIT]: '编辑',
  [BTN_DICT_DEL]: '删除',
  [BTN_DICT_DATA_ADD]: '新增',
  [BTN_DICT_DATA_EDIT]: '编辑',
  [BTN_DICT_DATA_DEL]: '删除',
};

export const BTN_MAP = {
  ...BTN_MAP_DEPT,
  ...BTN_MAP_USER,
  ...BTN_MAP_MENU,
  ...BTN_MAP_ROLE,
  ...BTN_MAP_DICT,
};
