<!doctype html>
<html @php(language_attributes())>
  @include('partials.head')
  <body @php(body_class(array(get_option( 'mx_main_background_class' ))))>
    @php(show_admin_bar(false))
    @yield('content')
    @php(wp_footer())
  </body>
</html>
