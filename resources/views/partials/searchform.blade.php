<form role="search" method="get" class="search-form" action="{{home_url( '/' )}}">
  <input type="search" class="search-field" placeholder="{{esc_attr_x( 'Search …', 'placeholder' )}}" value="{{get_search_query()}}" name="s" />
  <input type="submit" class="search-submit btn" value="{{esc_attr_x( 'Search', 'submit button' )}}" />
</form>
