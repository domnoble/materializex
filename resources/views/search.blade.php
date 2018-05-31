@extends('layouts.ls')

@section('content')
  @include('partials.page-header')
  @include('partials.search-bar')

  @if (!have_posts())
    <div class="alert alert-warning">
      {{  __('Sorry, no results were found.', 'sage') }}
    </div>
  @endif
    <div class='grid'>
    @while(have_posts()) @php(the_post())
      @include('partials.content-search')
    @endwhile
    </div>


  {!! \App\mx_get_the_posts_navigation() !!}
@endsection
