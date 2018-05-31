<time class="updated chip" datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
<div class="byline author vcard chip">
  {{ __('By', 'sage') }}
  <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}" rel="author" class="fn"> {{ get_the_author() }}</a>
</div>
