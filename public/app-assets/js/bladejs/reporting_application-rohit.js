$(function () { 
    const tbl = tables; 
    var tableFinal = tbl.replaceAll("&quot;", '"' );
    var tableFinalArr = JSON.parse(tableFinal);
    var formId = "#Admission_Report";
     
    var table = $(formId).DataTable({
       "bAutoWidth": false,
       "destroy": true,
       "responsive": true,
       "serverSide":true,
       "bProcessing": true,
       "bServerSide": true,
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
                $.each(tableFinalArr, function(i, item) {
                    if(item.fld != 'srno'){
                        itemName = item.fld;  
                        itemName = $('#'+itemName).val();
                        alert(itemName);
                        return false;
                    } 
                });
                d.name = $("#name").val();
                alert(d.name);
			}
		},

        columns: [
            {data: 'DT_RowIndex', 'defaultContent':'','orderable': false, 'searchable': false}, 
            {data: 'name', name: 'name','defaultContent':''},
            {data: 'gender_id', name: 'gender_id','defaultContent':'','className': 'text-center',render: function (gender_id){
			 return (gender_id == 1) ? "<div class='text-success'>Male</div>" : "<div class='text-info'>Female</div>";}},
			{data: 'medium', name: 'medium','defaultContent':'','className': 'text-center',render: function (medium){
			 return (medium == 1) ? "<div class='text-success'>Hindi</div>" : "<div class='text-info'>English</div>";}},
			{data: 'locksumbitted', name: 'locksumbitted','defaultContent':'',render: function (locksumbitted){
			 return (locksumbitted == 1) ? "<div class='text-success'>Yes</div>" : "<div class='text-info'>No</div>";
			 }}
		],
	});
 });
    
$('#submitFilterBtn').click(function(){ 
    $(formId).DataTable().draw(true);
});