var urlParams = new URLSearchParams(window.location.search)
if (urlParams.has('free_plan_preview')) {
  sessionStorage.setItem('free_plan_preview', 1)
  window.history.replaceState('', '', window.location.pathname)
}
jQuery(document).ready(function () {
  if (sessionStorage.getItem('free_plan_preview') !== null) {
    jQuery('.builder-bottom-banner').css('display', 'flex')
  }
})