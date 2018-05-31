<!doctype html>
<html @php(language_attributes())>
  @include('partials.head')
  <body @php(body_class(array(get_option( 'mx_main_background_class' ))))>
    @php(do_action('get_header'))
    @include('partials.header')
    @include('partials.nav')
    <div class="wrap container" role="document">
      <div class="content row">
        @if (App\display_sidebar())
        <aside class="sidebar col s3">
          @include('partials.sidebar')
        </aside>
        <main class="main col s9">
          @yield('content')
        </main>
        @else
        <main class="main col s12">
          @yield('content')
        </main>
        @endif
      </div>
    </div>
    @php(do_action('get_footer'))
    @include('partials.footer')
    @php(wp_footer())
  </body>
</html>
