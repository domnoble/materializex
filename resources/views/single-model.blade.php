@extends('layouts.full')

@section('content')
  <div class="container">
    <div class="row">
      @while(have_posts()) @php(the_post())
        @include('partials/content-single-'.get_post_type())
      @endwhile
   </div>
  </div>
@endsection
