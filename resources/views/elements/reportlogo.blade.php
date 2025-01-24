<style>
*{
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}
body{
    font-family: Helvetica;
    -webkit-font-smoothing: antialiased;
);
}
.table-wrapper{
    box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 );
}

.fl-table {
    border-radius: 5px;
    font-size: 12px;
    font-weight: normal;
    border: none;
    width: 100%;
    max-width: 100%;
    background-color: white;
}

.fl-table td, .fl-table th {
    text-align: center;
    padding: 2px;
}

.fl-table td {
    border-right: 1px solid #f8f8f8;
    font-size: 12px;
    word-break:break-all;
}

.fl-table thead th {
    color: #ffffff;
    background: #4FC3A1;
    word-break:break-all;
}


.fl-table thead th:nth-child(odd) {
    color: #ffffff;
    background: #324960;
}

.fl-table tr:nth-child(even) {
    background: #F8F8F8;
}







</style> 
<!-- Page Length Options -->

<table style="width:100%;">
	<tbody>
		<tr>
			<td style="width:30%"><img alt="" src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 60px; height: 49px; float: right;border-radius: 10px" /></span></td>
			<td style="text-align: left;width:40%;"><span style="font-size:15px;"><strong>Rajasthan State Open School {{ now()->year }}-{{ now()->year+1 }}</strong></span></td>
			<td style="width:30%">&nbsp;</td>
		</tr>
	</tbody>
</table>
<table style="width:100%;">
	<tbody>
		<tr>
			<td style="text-align: center; width: 35%;">&nbsp;</td>
			<td style="vertical-align: top;"><span style="font-size:15px;"><strong>{{@$reportname}}</strong></span></td>
		</tr>
		<tr>
			<td style="text-align: center; width: 35%;">&nbsp;</td>
			<td style="vertical-align: top;"><span style="font-size:15px;"><strong>{{@$subreportname}}</strong></span></td>
		</tr>
	</tbody>
</table>
<br>

