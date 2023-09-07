/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */


const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
const getStoredTheme = () => localStorage.getItem('theme')
const setStoredTheme = theme => localStorage.setItem('theme', theme)

if (getStoredTheme()) {
  document.documentElement.setAttribute('data-bs-theme', getStoredTheme())
}else{
  document.documentElement.setAttribute('data-bs-theme', preferredTheme)
  setStoredTheme(preferredTheme);
}

function flipTheme() {
  if(document.documentElement.getAttribute('data-bs-theme') == 'light'){
    document.documentElement.setAttribute('data-bs-theme', 'dark')
    setStoredTheme('dark');
  }else{
    document.documentElement.setAttribute('data-bs-theme', 'light')
    setStoredTheme('light');
  }
}