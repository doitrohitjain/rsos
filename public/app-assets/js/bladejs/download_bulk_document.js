
$(document).ready(function() { 
	 $("#downloadBulkDocumentForm").submit(function () {
		showLoading(); 
		$('.submit_disabled').attr('disabled',true);
		$('.reset').attr('disabled',true);
		$('.enrollment').attr('readonly',true);
		$('.ai_code').attr('readonly',true);
		$('.course').attr('readonly',true);
		$('.stream').attr('readonly',true);
		$('.document_type').attr('readonly',true);
		$('.type').attr('readonly',true);	
	});
});