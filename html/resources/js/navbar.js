$(document).ready(function() {
  var pathname = window.location.pathname.substring(1);
  if (pathname === '') {
    pathname = "index.php";
  }
  $('#navbar li a[href="'+pathname+'"]').parent().attr('id', 'active');
});
