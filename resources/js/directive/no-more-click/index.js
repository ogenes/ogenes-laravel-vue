import click from './click'

const install = function (Vue) {
  Vue.directive('no-more-click', click)
};

if (window.Vue) {
  window['no-more-click'] = click;
  Vue.use(install); // eslint-disable-line
}

click.install = install;
export default click
