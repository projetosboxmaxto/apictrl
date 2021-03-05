@extends('layouts.master')

@section('content')

<?php

if ( ! isset($post_type ) ){
   $post_type = "post";

   if ( Request::get("post_type") != "" ){
   	       $post_type = "page";
   }
   
}

?>
      <postlist post_type="{{ $post_type }}"></postlist>

      
@endsection