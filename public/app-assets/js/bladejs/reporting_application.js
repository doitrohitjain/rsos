$(function () { 
    let  fianlOptions = [];
    var fullFianlOptions = [];
    for (let k in options) { 
        fianlOptions[k] = options[k].replaceAll('&quot;','"'); 
        arr = $.parseJSON(fianlOptions[k]); //convert to javascript array
        fullFianlOptions[k] = arr; 
    }  
    // console.log(fullFianlOptions['stream'][1]);return false;
    
    
    var tableId = "#Admission_Report";
	var submitFilterBtn = "#submitFilterBtn";
     
    var table = $(tableId).DataTable({
       "bAutoWidth": false,
       "destroy": true,
       "responsive": true,
       "serverSide":true,
       "bProcessing": true,
       "bServerSide": true,
       "language": {processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Please wait loading...</span> '},
       "orderCellsTop": true,  
       "deferRender": true,
       "info": true,
       // "scrollY": 500,
       // "scrollX": true,
       "bFilter": false,
        lengthMenu: [
            [ 10, 25, 50,100, 500,1000],
            [ '10 rows', '25 rows', '50 rows','100 rows','500 rows','1000 rows']
        ],
		ajax: {
			url: config.routes.applications_report,
			type: 'GET',
            data: function (d) {
				d.enrollment = $('#enrollment').val();					
				d.name = $('#name').val();  				
				d.gender_id = $('#gender_id').val();
				d.course = $('#course').val();
				d.stream = $('#stream').val();
				d.course = $('#course').val();				
				d.adm_type = $('#adm_type').val(); 
                d.medium = $('#medium').val(); 				
				d.locksumbitted = $('#locksumbitted').val(); 
            }
		}, 
        columns: [
            {data: 'DT_RowIndex', 'defaultContent':'','orderable': false, 'searchable': false}, 
            {data: 'name', name: 'name','defaultContent':''},
			{data: 'enrollment',enrollment : 'enrollment','defaultContent':''},
            {data: 'gender_id', name: 'gender_id','defaultContent':'','className': 'text-center',render: function (gender_id){
                return fullFianlOptions['gender_id'][gender_id];}},
			{data: 'medium', name: 'medium','defaultContent':'','className': 'text-center',render: function (medium){
                return fullFianlOptions['medium'][medium];}},
			{data: 'adm_type', name: 'adm_type','defaultContent':'','className': 'text-center',render: function (adm_type){
                return fullFianlOptions['adm_type'][adm_type]; }},
			{data: 'course', name: 'course','defaultContent':'','className': 'text-center',render: function (course){
                return fullFianlOptions['course'][course];}},
			{data: 'stream', name: 'stream','defaultContent':'','className': 'text-center',render: function (stream){
                return fullFianlOptions['stream'][stream]; }},
            {data: 'locksumbitted', name: 'locksumbitted','defaultContent':'',render: function (locksumbitted){
                return fullFianlOptions['locksumbitted'][locksumbitted];
            }},				
		],
	});
 });
    
$(submitFilterBtn).click(function(){  
    var table =  $("#Admission_Report").DataTable().draw(true); 
});