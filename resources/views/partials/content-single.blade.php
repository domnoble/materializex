<article @php(post_class())>
    <header>
      <h1 class="entry-title center-align">{{ get_the_title() }}</h1>
    </header>
    {!! \App\mx_breadcrumbs() !!}
    <div class="card {{ get_option( 'mx_card_class' ) }}">
      @if ( has_post_thumbnail() )
      <div class="card-image">
        <img class="materialboxed" src="@php( the_post_thumbnail_url() )"/>
      </div>
      @endif
      <div class="card-content">
        <div class="entry-content">
          @php(the_content())
        </div>
      </div>
    </div>
    <footer class="valign-wrapper">
      @include('partials/entry-meta')
      @php(the_tags('<ul><li class="chip">', '</li><li class="chip">', '</li></ul>'))
    </footer>
    {!! \App\mx_link_pages(
      ['echo'           => 0,
       'before'         => '<div class="divider"></div><div class="center-align"><ul class="pagination">',
       'link_before'    => '<li class="waves-effect">',
       'link_after'     => '</li>',
       'seperator'      => ' ',
       'after'          => '</ul></div>'
       ]) !!}
    @php(comments_template('/partials/comments.blade.php'))
</article>
