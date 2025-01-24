$(function(){
	var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
	var action = "ajaxexamcentercode";
	var ajaxUrl = ajaxBaseUrl + action;

	$('.district1').on('change', function(){
		var district = $(".district1").val();
		var course = $(".course1").val(); 
		var itemName = '#subjectstype1';
		if(district != '' && course != ''){
			updateTheValues(itemName,district,course);		
		}
	});

	$('.district2').on('change', function(){
		var district = $(".district2").val();
		var course = $(".course2").val(); 
		var itemName = '#subjectstype2';
		if(district != '' && course != ''){
			updateTheValues(itemName,district,course);		
		}
	});


	function updateTheValues(itemName=null,district=null,course=null){ 
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type:'post',
			url: config.routes.ajaxexamcentercode,
			data: { district:district,course:course},
			success: function(data) { 
				var toAppend = '';
				toAppend += '<option>Select (Exam Center) </option>';
				$.each(data,function(i,o){ 
				   toAppend += '<option value=' + i +'>'+o+'</option>';
				}); 
				$(itemName)
					.find('option')
					.remove()
					.end()
					.append(toAppend);  
			}
		});
		 
	}
	$('.course1 , .course2').on('change', function(){
		$(".admtype option").prop("selected", false).trigger( "change" );
		$(".district2 option").prop("selected", false).trigger( "change" );
		$(".examcenter2 option").prop("selected", false).trigger( "change" );

		$(".district1 option").prop("selected", false).trigger( "change" );
		$(".examcenter1 option").prop("selected", false).trigger( "change" );

	});
});




