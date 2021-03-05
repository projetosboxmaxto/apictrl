<?php
function guidv4()
{
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$t = env("VERSION_ASSET");
$must_reload = env("RELOAD_DEV");

if ( $must_reload ){

      $t = guidv4();
}
   

?>

<!-- jQuery 3 -->
@if ( false )

<script src="{{ config('app.base_template', '/') }}bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
	
@endif


@if ( false )
<!-- Morris.js charts -->
<script src="{{ config('app.base_template', '/') }}bower_components/raphael/raphael.min.js"></script>
<script src="{{ config('app.base_template', '/') }}bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="{{ config('app.base_template', '/') }}bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="{{ config('app.base_template', '/') }}plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="{{ config('app.base_template', '/') }}plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="{{ config('app.base_template', '/') }}bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
@endif


<script src="{{ config('app.base_assets', '/') }}js/app.js?h=<?=$t ?>"></script>


<script src="{{ config('app.base_assets', '/') }}js/sweetalert/sweetalert2.all.min.js"></script>

      
<script src="{{ config('app.base_assets', '/') }}js/jquery-migrate-3.0.0.js?g=000009"></script>

<!-- Bootstrap 3.3.7 -->
<script src="{{ config('app.base_template', '/') }}bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- daterangepicker -->
<script src="{{ config('app.base_template', '/') }}bower_components/moment/min/moment.min.js"></script>
<script src="{{ config('app.base_template', '/') }}bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="{{ config('app.base_template', '/') }}bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ config('app.base_template', '/') }}plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="{{ config('app.base_template', '/') }}bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="{{ config('app.base_template', '/') }}bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="{{ config('app.base_template', '/') }}dist/js/adminlte.min.js"></script>
<script src="{{ config('app.base_template', '/') }}dist/js/jquery.tagify.js?h=000022"></script>
<script src="{{ config('app.base_template', '/') }}dist/js/tagify.js?h=000021"></script>


@if ( false )

<script src="{{ config('app.base_template', '/') }}summernote-0.8.9/dist/summernote.js?g=0001"></script>

@endif 


<script src="{{ config('app.base_assets', '/') }}js/tinymce/tinymce.min.js"></script>
<script src="{{ config('app.base_assets', '/') }}js/dataTables/jquery.dataTables.min.js"></script>
<script src="{{ config('app.base_assets', '/') }}js/dataTables/dataTables.responsive.min.js"></script>
<script src="{{ config('app.base_assets', '/') }}js/gallery/pagination.min.js"></script>
<? if ( false ) { ?>
<script src="{{ config('app.base_assets', '/') }}js/gallery/lightbox2-master/dist/js/lightbox.min.js"></script>
<? } ?>

<!-- iCheck 1.0.1 -->
<script src="{{ config('app.base_template', '/') }}plugins/iCheck/icheck.min.js"></script>
<script src="{{ config('app.base_template', '/') }}bower_components/jquery-ui/jquery-ui.min.js"></script>

<!--Colorpicker-->
<script src="{{ config('app.base_assets', '/') }}js/colorpicker/js/colorpicker.js"></script>
<script src="{{ config('app.base_assets', '/') }}js/colorpicker/js/eye.js"></script>
<script src="{{ config('app.base_assets', '/') }}js/colorpicker/js/utils.js"></script>


<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
