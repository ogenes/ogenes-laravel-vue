const cssArray = [
  'Pagination',
  'Autocomplete',
  'Dropdown',
  'dropdown-menu',
  'dropdown-item',
  'Menu',
  'Submenu',
  'drawer',
  'text',
  'tags',
  'sidebar',
  'menu-item',
  'menu-item-group',
  'Input',
  'input-number',
  'Radio',
  'radio-group',
  'radio-button',
  'Checkbox',
  'checkbox-group',
  'Switch',
  'Select',
  'Option',
  'option-group',
  'Button',
  'button-group',
  'Table',
  'table-column',
  'date-picker',
  'time-select',
  'time-picker',
  'Popover',
  'Tooltip',
  'Breadcrumb',
  'breadcrumb-item',
  'Form',
  'form-item',
  'Tabs',
  'tab-pane',
  'Tag',
  'Tree',
  'Alert',
  'Slider',
  'Icon',
  'Row',
  'Col',
  'Upload',
  'Progress',
  'Spinner',
  'Badge',
  'Card',
  'Rate',
  'Steps',
  'Step',
  'Carousel',
  'Scrollbar',
  'carousel-item',
  'Collapse',
  'collapse-item',
  'Cascader',
  'color-picker',
  'Loading',
  'message-box',
  'Message'
];

export const theme = {
  changeTheme: function (themeValue) {
    if (themeValue === 'green') {
      for (let i = 0, len = cssArray.length; i < len; i++) {
        const itemPathRemove = '/static/theme/' + 'blue' + '/' + cssArray[i].toLowerCase() + '.css';
        removeCss(itemPathRemove);
        const itemPath = '/static/theme/' + themeValue + '/' + cssArray[i].toLowerCase() + '.css';
        loadCss(itemPath)
      }
    } else {
      for (let i = 0, len = cssArray.length; i < len; i++) {
        const itemPathRemove1 = '/static/theme/' + 'green' + '/' + cssArray[i].toLowerCase() + '.css';
        removeCss(itemPathRemove1);
        const itemPath1 = '/static/theme/' + themeValue + '/' + cssArray[i].toLowerCase() + '.css';
        loadCss(itemPath1)
      }
    }

    function loadCss(path) {
      const head = document.getElementsByTagName('head')[0];
      const link = document.createElement('link');
      link.href = path;
      link.rel = 'stylesheet';
      link.type = 'text/css';
      head.appendChild(link);
    }

    function removeCss(href) {
      const targetElement = 'link';
      const targetAttr = 'href';
      const allSuspects = document.getElementsByTagName(targetElement);
      for (let i = allSuspects.length; i >= 0; i--) {
        if (allSuspects[i] && allSuspects[i].getAttribute(targetAttr) != null && allSuspects[i].getAttribute(targetAttr).indexOf(href) !== -1) {
          allSuspects[i].parentNode.removeChild(allSuspects[i])
        }
      }
    }
  }
};
