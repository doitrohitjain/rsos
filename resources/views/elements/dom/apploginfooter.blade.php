 <style>
  .dataTable > tbody > tr > td{
      white-space:normal !important;
      max-width:225px;
  }
.toast-top-full-width {
  font-size: 10px;
  width: 25%;
}
</style>
       <footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow">
      <div class="footer-copyright">
        <div class="container"><span>&copy; 2022 <a href="https://rsosapp.rajasthan.gov.in/" target="_blank">RSOS</a> All rights reserved.</span><span class="right hide-on-small-only">Design and Developed by <a href="#">DOIT&C</a></span></div>
      </div>
    </footer>

<div class="spinner-container mainCls" style="display:none;">
	<div class="loader-main">
		<div class="cssload-loader"><div class="cssload-inner cssload-one"></div>
			<div class="cssload-inner cssload-two"></div><div class="cssload-inner cssload-three"></div>
		</div>
		<p>Please wait ...</p>
	</div>
</div>
	
<div id='loader' style='display: none;'>
	<img src="{!! asset('public/app-assets/images/loading-bar.png') !!}" width='32px' height='32px'>
</div>


    <!-- END: Footer-->
    <!-- BEGIN VENDOR JS-->
   
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.96.1/js/materialize.min.js"></script>
	<script src="{!! asset('public/app-assets//js/scripts/form-select2.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets//vendors/select2/select2.full.min.js') !!}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
	<script src="{!! asset('public/app-assets/js/custom/custom-script.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/custom/custom.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/scripts/ui-alerts.min.js') !!}"></script>

  </body>
  
 

<script type="text/javascript">

$( document ).ready(function() {
	$( ".txtOnly" ).keypress(function(e) {
		var key = e.keyCode;
		if (key >= 48 && key <= 57) {
			e.preventDefault();
		}
	});
});
			
function showLoading() { 
  $('.mainCls').css('display', 'block');
} 
function hideLoading() {
	$('.mainCls').css('display', 'none');
}

@if (Session::has('message'))
  toastr.options =
  {
    "progressBar" : true,
	"positionClass": "toast-top-full-width",
  }
        toastr.success("{{ session('message') }}");
  @endif

  @if(Session::has('error'))
  toastr.options =
  {
    "progressBar" : true,
	"positionClass": "toast-top-full-width",
  }
   toastr.error("{{ session('error') }}");
  @endif

  @if(Session::has('info'))
  toastr.options =
  {
    "progressBar" : true
  }
        toastr.info("{{ session('info') }}");
  @endif

  @if(Session::has('warning'))
  toastr.options =
  {
    "progressBar" : true
  }
        toastr.warning("{{ session('warning') }}");
  @endif
  
     // $('.show_confirm').click(function(event) {
          // var form =  $(this).closest("form");
          // event.preventDefault();
          // swal({
             // title: 'Are you sure save records',
			 // text: "You won't be able to revert this!",
			 // icon: 'success',
             // buttons: true,
          // })
          // .then((willsave) => {
            // if (willsave) {
              // form.submit();
            // }
          // });
      // });
	  
	  
	  
</script>

<!-- Mirrored from pixinvent.com/materialize-material-design-admin-template/html/ltr/vertical-dark-menu-template/table-data-table.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 26 Aug 2021 06:31:53 GMT -->
</html>


