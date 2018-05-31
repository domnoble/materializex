<article @php(post_class(array(get_option('mx_card_class'),'card','grid-item','grid-item--width2')))>
  @if ( has_post_thumbnail() )
  <header class="card-image">
    <img class="materialboxed" src="@php( the_post_thumbnail_url() )"/>
    <h class="entry-title card-title"><a href="{{ get_permalink() }}">{{ get_the_title() }}</a></span>
  </header>
  <div class="card-content ">
  @else
  <div class="card-content ">
    <header>
      <h2 class="entry-title card-title"><a href="{{ get_permalink() }}">{{ get_the_title() }}</a></h2>
    </header>
  @endif
    <div class="entry-summary">
      @php(the_excerpt())
    </div>
  </div>
  <footer class="card-action">
    <a href="{{ get_permalink() }}">Read More</a>
    @include('partials/entry-meta')
  </footer>
</article>
