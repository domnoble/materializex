@php
if (post_password_required()) {
  return;
}
@endphp

@if (!post_type_supports(get_post_type(), 'comments'))
  <div class="alert alert-warning">
    {{ __('Comments are not supported on this post type.', 'sage') }}
  </div>
@elseif (!comments_open())
<div class="alert alert-warning">
  {{ __('Comments are closed.', 'sage') }}
</div>
@else
  <section id="comments" class="comments">
    <div class="card {{ get_option( 'mx_card_class' ) }}">
      <div class="card-content">
        @if (have_comments())
            <h2  class="header card-title">
              {!! sprintf(_nx('One response to &ldquo;%2$s&rdquo;', '%1$s responses to &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'sage'), number_format_i18n(get_comments_number()), '<span>' . get_the_title() . '</span>') !!}
            </h2>

            <div class="comment-list">
              {!! wp_list_comments(['walker' => new App\MaterializeX_Walker_Comment, 'style' => 'div']) !!}
            </div>

            @if (get_comment_pages_count() > 1 && get_option('page_comments'))
              <nav>
                <ul class="pager">
                  @if (get_previous_comments_link())
                    <li class="previous">@php(previous_comments_link(__('&larr; Older comments', 'sage')))</li>
                  @endif
                  @if (get_next_comments_link())
                    <li class="next">@php(next_comments_link(__('Newer comments &rarr;', 'sage')))</li>
                  @endif
                </ul>
              </nav>
            @endif
        @endif

        @php(comment_form())
    </div>
  </div>
  </section>
@endif
