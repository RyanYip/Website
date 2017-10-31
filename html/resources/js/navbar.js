$(document).ready(function() {
  var pathname = window.location.pathname.substring(1);
  $('#navbar li a[href="'+pathname+'"]').parent().attr('id', 'active');
});
