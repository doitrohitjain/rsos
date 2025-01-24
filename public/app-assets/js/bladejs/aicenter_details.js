$(document).ready(function() {
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxAicenterDetilasValidation";
	var ajaxUrl = ajaxBaseUrl + action; 
	formId = "#" + config.data.formId;
	$(formId).validate({
		success: function(response){
		},
		submitHandler: function(form) { 
			if($('#ajaxRequest').val()==1){
				showLoading(); 
				$.ajax({
					url: config.routes.ajaxAicenterDetilasValidation,
					type: "POST",
					dataType: "json",
					data: $(formId).serialize(),
					success: function (response) {
						hideLoading();
						if(response.isValid==false){
							message = JSON.stringify(response); 
							// var obj = JSON.parse(response);
							 
							var errStr = "";
							var counter = 1;
							$.each(response, function(index, elementOuter) {
								$.each(elementOuter, function(index, elementt) {  
									$.each(elementt, function(indexiDis, elementtiDis) { 
										errStr += "" + counter + " : " + elementtiDis  + "\n";
										counter++;
									});
								});
							});
								 
							swal({
								title: "Validation Error",
								text: errStr,
								icon: "error",
								button: "Close",
								timer: 30000
							});
							return false;
						} else {
							
							var clsName = "api";

							if($(formId).hasClass(clsName)){ 
								return true;
							}
							swal({
								title: 'Are you sure save your information.',
								text: "You won't be able to revert this!",
								icon: 'success',
								buttons: true,
							 })
							.then((willsave) => {
								if (willsave) { 
									$(formId).addClass(clsName); 
									$('#ajaxRequest').val('0');
									$(formId).submit();
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
				return true;
			} 
			
		}
	});
});

$(function(){
	$('.district_id').on("change", function(event) {
		var id = $(event.currentTarget).find("option:selected").val();
		if(id!=''){
			event.preventDefault();
			$.ajax({
				// url: "{{ route('get_tehsil_by_district_id') }}",
				url: config.routes.get_block_by_district_id,
				type: "get",
				data: {'id': id},
				success: function(data) { 
				var toAppend = '';
				$('#block').html('<option value="">Select block (चयन  ब्लॉक ):</option>');
				$.each(data, function(key,value) {
					$('#block').append('<option value="' + key + '">' + value + '</option>');
				}); 
			},
				error: function (data) {
					console.log('Error:', data); 
					hideLoading();
				}
			});
		}
	});
	  $(".sso_id").change(function(){
        if($(this).val()!=''){
            showLoading();
            $.ajax({
            url: config.routes.ajaxGetSSOIDDetials,
            type: "get",
            dataType: "json",
            data: {'sso_id':$(this).val()},
            success: function (response) {
                if(response!=null){
                    $('.name').val(response.displayName);
                    $('.mobile').val(response.mobile);
                    $('.dateOfBirth').val(response.dateOfBirth);
					$('.mailPersonal').val(response.mailPersonal);
                    hideLoading();
                    return false;
                }else{
                    swal({
                        title: 'Validation Error.',
                        text: "Please enter valid SSO ID.",
                        icon: 'error',
                         buttons: {OK: true},
                     })
                    .then((willsave) => {
                    });
                }
            },error: function (response) {
                    console.log('Error:', response);
                    hideLoading();
                    return false;
                }
            });
        } else {
            return true;
        }
    });
});