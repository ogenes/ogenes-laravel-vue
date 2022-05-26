/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Cookies from 'js-cookie'

import 'normalize.css/normalize.css' // a modern alternative to CSS resets

import ElementUI from 'element-ui'
import 'element-theme-chalk';
import 'element-ui/lib/theme-chalk/index.css';

import i18n from './i18n/i18n';

import '@/styles/element-variables.module.scss'
import '@/styles/index.scss' // global css
import router from '@/router';
import store from '@/store';
import '@/permission' // permission control
import '@/icons' // icon
import '@/websocket.js' // websockets

Vue.use(ElementUI, {
  i18n: (key, value) => i18n.t(key, value),
  size: Cookies.get('size') || 'medium'
});

import VueResource from 'vue-resource'
import VCalendar from 'v-calendar';

Vue.use(VueResource);
Vue.use(VCalendar);

import Editor from 'bin-ace-editor';
import 'brace/ext/language_tools';
import 'brace/mode/json';
import 'brace/snippets/json';
import 'brace/theme/chrome';

Vue.component(Editor.name, Editor);

// directive
import noMoreClick from '@/directive/no-more-click';
import permission from '@/directive/permission';

Vue.use(noMoreClick);
Vue.use(permission);

Vue.component('index-component', () => import('./components/IndexComponent.vue'));

const app = new Vue({
  el: '#app',
  store,
  router,
  i18n
});
