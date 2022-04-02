function click(el, binding) {
  el.addEventListener('click', e => {
    el.classList.add('is-disabled');
    el.disabled = true;
    setTimeout(() => {
      el.disabled = false;
      el.classList.remove('is-disabled');
    }, 2000)
  })
}

export default {
  inserted(el, binding) {
    click(el, binding)
  },
  update(el, binding) {
    click(el, binding)
  }
}
