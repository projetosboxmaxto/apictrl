@extends('layouts.master')

@section('content')

<?php
/*
   $post_type = "post";

   if ( Request::get("post_type") != "" ){
   	       $post_type = "page";
   }
 * 
 * Request::get("id")
*/
?>
      <post post_type="{{ $post_type }}" id_load="{{ $id  }}" ></post>


@endsection