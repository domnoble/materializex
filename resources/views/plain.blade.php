{{--
  Template Name: Plain Page
--}}

@extends('layouts.plain')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.content-page')
  @endwhile
@endsection
