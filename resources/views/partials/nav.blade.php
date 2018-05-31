@if (is_user_logged_in())
  @php ( $navigationClass = 'primary-navigation-li' )
@else
  @php ( $navigationClass = 'primary-navigation' )
@endif
<nav class=" {{ $navigationClass }} {{ get_option( 'mx_header_menu_class' ) }}">
  <div class="nav-wrapper container">
  @if (has_nav_menu('primary_navigation'))

    {!!     wp_nav_menu( array(
        'menu'              => 'primary_navigation',
        'theme_location'    => 'primary_navigation',
        'depth'             => 2,
        'container'         => 'div',
        'container_class'   => 'collapse navbar-collapse',
        'container_id'      => 'primary-navigation',
        'menu_class'        => 'nav navbar-nav',
        'fallback_cb'       => 'MaterializeX_Nav_Walker::fallback',
        'walker'            => new \App\MaterializeX_Nav_Walker())
    ); !!}

  @endif
  </div>
</nav>
