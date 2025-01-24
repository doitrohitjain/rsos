
$(document).ready(function() {  
	
	var course = $("#course").val(); 
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	formId = "#" + config.data.formId;
	var action = "ajaxSuppSubjectValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	
	var actionDocValidation = "ajaxSuppSubjectdocumentValidation";
	var ajaxUrlDocValidation = ajaxBaseUrl + actionDocValidation; 
 
	$("#size_sec_marksheet_doc_hidden").val(0);
	$("#size_marksheet_doc_hidden").val(0);
	
	$(formId).validate({
		rules: {
			 // marksheet_doc: { required: true},
		},
		messages: {
			// marksheet_doc: { 
				// required: "Marksheet Document is required", 
			// },
		},
		success: function(response){
		
		},
	/*
	submitHandler: function(form) {
			var clsName = "api";
			$('.subject_list').prop("disabled", false);
	
			if($(formId).hasClass(clsName)){
				return true;
			} else {
				event.preventDefault();
				swal({
					title: 'Are you sure save your information.',
					text: "You won't be able to revert this!",
					icon: 'success',
					buttons: true,
				})
				.then((willsave) => {
					if (willsave) {
						$(formId).addClass(clsName); 
						$(formId).submit();
					}
				});
			}
		}
	*/	
	
	submitHandler: function(form) {
			if($('#ajaxRequest').val()==1){   
				showLoading();
				$(".subjectitem").prop("disabled", false );
				
				$.ajax({
					url: config.routes.ajaxSuppSubjectValidation,
					type: "POST",
					data: $(formId).serialize(),
					success: function (response) { 
						hideLoading();
						if(response.isValid==false){ 
							$(".subjectitem").prop("disabled", true );
							var message = response.errors;
							swal({
								title: "मान्यता त्रुटि(Validation Error)",
								text: message,
								icon: "error",
								button: "Close",
								timer: 30000
							});
							return false;
						} else {   
							var element = "";
								 element = "#marksheet_doc_hidden"; var marksheet_doc_hidden = $(element).val();
								 element = "#size_marksheet_doc_hidden"; var size_marksheet_doc_hidden = $(element).val();
								 element = "#marksheet_doc"; var marksheet_doc = $(element).val();				
								var inputJson = null;
								if(course == 10){ 
									inputJson = {
										'marksheet_doc_hidden':marksheet_doc_hidden,
										'size_marksheet_doc_hidden':size_marksheet_doc_hidden,
										'marksheet_doc':marksheet_doc, 
										'course':course, 
									};  
								}
								
								if(course == 12){ 	
									 element = "#sec_marksheet_doc_hidden"; var sec_marksheet_doc_hidden = $(element).val();
									 element = "#size_sec_marksheet_doc_hidden"; var size_sec_marksheet_doc_hidden = $(element).val();
									 element = "#sec_marksheet_doc"; var sec_marksheet_doc = $(element).val(); 
									
									inputJson = {
										'sec_marksheet_doc_hidden':sec_marksheet_doc_hidden,
										'size_sec_marksheet_doc_hidden':size_sec_marksheet_doc_hidden,
										'sec_marksheet_doc':sec_marksheet_doc, 
										'marksheet_doc_hidden':sec_marksheet_doc_hidden,
										'size_marksheet_doc_hidden':size_sec_marksheet_doc_hidden,
										'marksheet_doc':marksheet_doc, 
										'course':course, 
									};  
								}

								$.ajaxSetup({
									headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
									}
								});
								$.ajax({
									url: config.routes.ajaxSuppSubjectdocumentValidation,
									method:"POST",
									data: inputJson,
									success: function (response) {
										hideLoading();
										if(response.isValid==false){
											var message = response.errors; 
											swal({
												title: "मान्यता त्रुटि(Validation Error)",
												text: message,
												icon: "error",
												button: "Close",
												timer: 30000
											});
											return false;
										}else{ 
											$(".subjectitem").prop("disabled", false );
											var clsName = "api";

											if($(formId).hasClass(clsName)){
												return true;
											}
											swal({
												title: 'Are you sure save your information.',
												text: "You won't be able to revert this!",
												//text: form_edit_msg,
												icon: 'success',
												buttons: true,
											 })
											.then((willsave) => {
												if (willsave) {
													$(formId).addClass(clsName); 
													$('#ajaxRequest').val('0');
													$(formId).submit();
												} else {
													$(".subjectitem").prop("disabled", true );
												}
											}); 
										}
									}									
								}); 
						} 
					},
					error: function (data) {
						console.log('Error:', data); 
						hideLoading();
					}
				});
			} else {
				$(".subjectitem").prop("disabled", false );
				return true;
			} 
			
		}
		
});

$('.edit_supp_btn').on("click",function(event){
	var edit_supp_btn_id = $(this).attr('id');
	if(edit_supp_btn_id!=''){
		$('.'+edit_supp_btn_id).prop( "disabled", false);
	}
});




$('.form_doc_input').on("change",function(event){ 
	
	var sizeInKb = $(this).prop('files')[0].size  / 1024;
	
	var id = "#"+$(this).attr('id');
	var hiddenSize = "#size_" + $(this).attr('id') +"_hidden";
	  
	$(hiddenSize).val(sizeInKb);
		
	if(sizeInKb >= 10 && sizeInKb <= 100 ){
		
	}else {
		$(id).val(null);
		swal({
			title: "मान्यता त्रुटि(Validation Error)",
			text: "कृपया अपलोड किए गए दस्तावेज़ का आकार 10 केबी से 100 केबी होना चाहिए।(Please upload document size should be 10 kb to 100 kb.)",
			icon: "error",
			button: "Close",
			timer: 30000
		});	
		return false; 
	}
	
});

	
$('.supp_form_submit').on("click",function(event){ 
	var course = $("#course").val();
	 
	if($('.marksheet_doc').val() == '' &&  $('#marksheet_doc_hidden').val() == '') { 
		swal({
			html:true,
			title: "मान्यता त्रुटि(Validation Error)",
			text: "कृपया मार्कशीट दस्तावेज अपलोड करें। ( Please upload the marksheet document! )",
			icon: "error",
			button: "Close",
			timer: 30000
		});
		return false;
	}
 
	if(course == 12){
		if($('.sec_marksheet_doc').val() == '' &&  $('#sec_marksheet_doc_hidden').val() == '') { 
			swal({
				html:true,
				title: "मान्यता त्रुटि(Validation Error)",
				text: "कृपया मार्कशीट दस्तावेज अपलोड करें। ( Please upload the marksheet document! )",
				icon: "error",
				button: "Close",
				timer: 30000
			});
			return false;
		}
	}
	
	
	
	
	var validExtensions = ["jpg","png","jpeg","gif","pdf","svg"];
	var filename = $(".marksheet_doc").val();
	var fileNameExt = filename.replace(/^.*\./, '');
	fileNameExt = fileNameExt.toLowerCase();
	if ($.inArray(fileNameExt, validExtensions) == -1 && $('.marksheet_doc').val() != ''){  
		swal({
			title: "मान्यता त्रुटि(Validation Error)",
			text: "कृपया एक मान्य मार्कशीट प्रारूप अपलोड करें। अनुमत प्रारूप (जेपीजी, पीएनजी, जेपीईजी, जीआईएफ, पीडीएफ, एसवीजी)।(Please upload a valid marksheet format.  Allowed Formats (jpg,png,jpeg,gif,pdf,svg).)",
			icon: "error",
			button: "Close",
			timer: 30000
		});
		return false;
	} 

	// var sizeInKb = $('.marksheet_doc').files[0].size / 1024;
	// alert(sizeInKb);return false;

	if(course == 12){
		var validExtensions = ["jpg","png","jpeg","gif","pdf","svg"];
		var filename = $(".sec_marksheet_doc").val();
		var fileNameExt = filename.replace(/^.*\./, '');
		fileNameExt = fileNameExt.toLowerCase();
		if ($.inArray(fileNameExt, validExtensions) == -1 && $('.sec_marksheet_doc').val() != ''){  
			swal({
				title: "मान्यता त्रुटि(Validation Error)",
				text: "कृपया एक मान्य मार्कशीट प्रारूप अपलोड करें। अनुमत प्रारूप (जेपीजी, पीएनजी, जेपीईजी, जीआईएफ, पीडीएफ, एसवीजी)।(Please upload a valid marksheet format.  Allowed Formats (jpg,png,jpeg,gif,pdf,svg).)",
				icon: "error",
				button: "Close",
				timer: 30000
			});
			return false;
		} 
	}  
	$('#Supplementary').submit();
});

$('.form_reset').on("click",function(event){
	location.reload(true);
});

$(".supp_form_document_field").on("change", function(e){

   //$('.supp_form_document_value').html(($(this).val().replace(/.*(\/|\\)/, '')));
   var mainId = $(this).attr('id');
   var clsId = "#div_cls_"+mainId;
   $(clsId).html(($(this).val().replace(/.*(\/|\\)/, '')));
});


$('.edit').click(function(){
	$(this).addClass('editMode');
});

var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
var action = "livetableupdate";
var ajaxUrl = ajaxBaseUrl + action;
$(".edit").focusout(function(){
	$(this).removeClass("editMode");
	var id = this.id;
	var data_val =  value = $.trim($(this).text());
	 
	var old_val = $.trim($(this).attr('data-old_val'));
	var name = $(this).attr("name"); 
	var moblength = data_val.length;  
	if(data_val != old_val){  
		if(moblength !== 10){	  
			swal({
				title: 'Mobile number should be 10 digit',
				icon: 'error',
				showConfirmButton: false,
			});
			$(this).text(old_val);
			return false;
		}else{
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				type: 'post',
				url: config.routes.livetableupdate,
				data: { id:id, value:value, name:name },
				success:function(response){
					if(response == 1){
						swal({
							title: 'Mobile number has been successfully updated.',
							icon: 'success',
							showConfirmButton: false,
							timer: 1200
						});
					}else{
						swal({
							title: 'Mobile number not saved! Something is wrong.',
							icon: 'error',
							showConfirmButton: false,
						});
						$(this).text(old_val);
						return false;
					}
				}
			});
		} 
	} 
});

}); 



   