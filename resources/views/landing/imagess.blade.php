<?php echo $barcode_img ?>
@extends('layouts.default')
@section('content')
<button onclick="window.print();">press</button> 
<script>
$(document).ready(function() {

$('.print').click(function(){  

window.print();

});
});

</script>
@endsection