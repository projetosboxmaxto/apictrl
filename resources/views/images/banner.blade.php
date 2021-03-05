@extends('layouts.master')

@section('content')


<?php
   $banner_type = "principal";

   if ( Request::get("type") != "" ){
   	       $banner_type = Request::get("type");
   }

?>

<div id="app">

      <imagebanner ptype="{{ $banner_type }}"></imagebanner>


</div>

          
@endsection