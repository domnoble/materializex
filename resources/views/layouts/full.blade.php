<!doctype html>
<html @php(language_attributes())>
  @include('partials.head')
  <body @php(body_class(array(get_option( 'mx_main_background_class' ))))>
    @php(do_action('get_header'))
    @include('partials.header')
    @include('partials.nav')
    <main>
      @yield('content')
    </main>
    @php(do_action('get_footer'))
    @include('partials.footer')
    @php(wp_footer())
  </body>
</html>
