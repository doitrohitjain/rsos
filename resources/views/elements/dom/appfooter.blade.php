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
@include('elements.modal_popup')
@include('elements.modal_dragable_popup')
	   <footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow">
      <div class="footer-copyright">
        <div class="container"><span>&copy; {{ now()->year }} <a href="https://rsosadmission.rajasthan.gov.in/" target="_blank">RSOS</a> All rights reserved.</span><span class="right hide-on-small-only">Design and Developed by <a href="#">DOIT&C</a></span></div>
      </div>

      @php 
  $ip = NULL;
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty(@$_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ip = $_SERVER['REMOTE_ADDR'];
  } 
@endphp



    </footer>

    <!-- END: Footer-->
    <!-- BEGIN VENDOR JS-->
    
	<script src="{!! asset('public/app-assets/js/vendors.min.js') !!}"></script>
	
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{!! asset('public/app-assets/vendors/data-tables/js/jquery.dataTables.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/vendors/data-tables/js/dataTables.select.min.js') !!}"></script>


  <script src="{!! asset('public/app-assets//js/scripts/form-select2.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets//vendors/select2/select2.full.min.js') !!}"></script>



    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN THEME  JS-->
	<script src="{!! asset('public/app-assets/js/plugins.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/search.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/custom/custom-script.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/scripts/customizer.min.js') !!}"></script>
    <!-- END THEME  JS-->
    <!-- BEGIN PAGE LEVEL JS--> 
    <script src="{!! asset('public/app-assets/js/scripts/data-tables.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/dataTables.buttons.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/jszip.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/buttons.html5.min.js') !!}"></script> 
	<script src="{!! asset('public/app-assets/js/toastr.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/sweetalert.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/moment.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/scripts/advance-ui-modals.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/dataTables.dateTime.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/jquery.validate.min.js') !!}"></script>
	
	
	<script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/super-build/ckeditor.js"></script> 
	
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.js.map"></script> 
	<script src="{!! asset('public/app-assets/js/custom/custom.js') !!}"></script>
<script> 


	$( document ).ready(function() {
 
	   
        var ckeditorCount = $('.ckeditor').length;

        if(ckeditorCount > 0){
            var ckeditorId = $('.ckeditor').attr('id');
            $('.ckeditor').each(function(i, e){
                $(this).attr('id', "id_" + i+1);
                ckeditorId = $(this).attr('id');
                CKEDITOR.ClassicEditor.create(document.getElementById(ckeditorId), {
                    toolbar: {
                        items: [
                            'exportPDF','exportWord', '|',
                            'findAndReplace', 'selectAll', '|',
                            'heading', '|',
                            'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                            'bulletedList', 'numberedList', 'todoList', '|',
                            'outdent', 'indent', '|',
                            'undo', 'redo',
                            '-',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                            'alignment', '|',
                            'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                            'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                            'textPartLanguage', '|',
                            'sourceEditing'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    list: {
                        properties: {
                            styles: true,
                            startIndex: true,
                            reversed: true
                        }
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                            { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                        ]
                    },
                    
                    placeholder: 'Welcome to RSOS',
                    
                    fontFamily: {
                        options: [
                            'default',
                            'Arial, Helvetica, sans-serif',
                            'Courier New, Courier, monospace',
                            'Georgia, serif',
                            'Lucida Sans Unicode, Lucida Grande, sans-serif',
                            'Tahoma, Geneva, sans-serif',
                            'Times New Roman, Times, serif',
                            'Trebuchet MS, Helvetica, sans-serif',
                            'Verdana, Geneva, sans-serif'
                        ],
                        supportAllValues: true
                    },
                    
                    fontSize: {
                        options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                        supportAllValues: true
                    },
                    
                    htmlSupport: {
                        allow: [
                            {
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true
                            }
                        ]
                    },
                    
                    htmlEmbed: {
                        showPreviews: true
                    },
                    
                    link: {
                        decorators: {
                            addTargetToExternalLinks: true,
                            defaultProtocol: 'https://',
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    },
                    
                    mention: {
                        feeds: [
                            {
                                marker: '@',
                                feed: [
                                    '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                                    '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                                    '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                                    '@sugar', '@sweet', '@topping', '@wafer'
                                ],
                                minimumCharacters: 1
                            }
                        ]
                    },
                    
                    removePlugins: [
                        
                        'CKBox',
                        'CKFinder',
                        'EasyImage',
                        'RealTimeCollaborativeComments',
                        'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory',
                        'PresenceList',
                        'Comments',
                        'TrackChanges',
                        'TrackChangesData',
                        'RevisionHistory',
                        'Pagination',
                        'WProofreader',
                        'MathType'
                    ]
                });
            });
        }
		$( ".txtOnly" ).keypress(function(e) {
			var key = e.keyCode;
			if (key >= 48 && key <= 57) {
				e.preventDefault();
			}
		});
	});
	
	
	/*
	$(document).ready(function() {  
		var ip = "@php echo $ip;  @endphp";
		if(ip != "10.68.181.236" || ip != "10.68.181.213" || ip != "10.68.181.229"){
			window.history.forward();
			window.onload = function(){
				window.history.forward();
			}; 
			window.onunload = function() {
				null;
			};
		}
  });
  */
  
</script>

    <!-- END PAGE LEVEL JS-->
  </body>
@include('elements.apploader')
@include('elements.dom.appga')
<!-- Mirrored from pixinvent.com/materialize-material-design-admin-template/html/ltr/vertical-dark-menu-template/table-data-table.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 26 Aug 2021 06:31:53 GMT -->
</html>

@include("elements.php_var_js_set")

