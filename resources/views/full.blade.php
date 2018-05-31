{{--
  Template Name: Full Width Page
--}}

@extends('layouts.full')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.content-page')
  @endwhile
@endsection
