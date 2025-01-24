@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">

					@php 
					
					$html = "<table border='1' class='table table-hover w-100 text-center'>";
		$html .= "<tr>";
		$html .= "<th>Student Sr. No.</th>";
		$html .= "<th>Name</th>";
		$html .= "<th>Rejected Documents</th>";
		
		$html .= "</tr>";
		$maincounter = 1;
		foreach($studentDetails as $stk => $stv){
			$list = $studentDetails[$stk]['list'];
			$counter = 1;
			$html .= "<tr>";
			$html .= "<td>" . $maincounter++ . "</td>";
			$html .= "<td><b>" . $studentDetails[$stk]['name'] . "</b></td>";
				$html .= "<td>";
					$html .= "<table border='1'>"; 
						$html .= "<tr>";
						$html .= "<th>Sr. No.</th>";
						$html .= "<th>Document Name</th>";
						$html .= "<th>Rejected Reason</th>"; 
						$html .= "</tr>";
						foreach($list as $k => $v){ 
							$html .= "<tr>"; 
							$html .= "<td>" . $counter++ . "</td>";
							$html .= "<td>" . @$verificationLabels[$k]['hindi_name'] . "</td>";
							$html .= "<td>";
							$html .= "<table broder='1'>"; 
							$icoutner = 0;
							foreach($v as $ik => $iv){
								$icoutner++;
								if($iv == 2){
									$html .= "<tr>";$html .= "<td>" . $icoutner . ". ";
										$html .= @$verficationmasterdataFinal[$k][$ik] . " ";
									$html .= "</td>";
									$html .= "</tr>";
								}
							}  
							$html .= "</table>";
							$html .= "</td>";  
							$html .= "</tr>"; 
						} 
					
					$html .= "</table>"; 
				$html .= "</td>"; 
			$html .= "</tr>";
		}
		$html .= "</table>";
		echo $html;
					@endphp
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<style>

  td {

    word-wrap: break-word; /* Allows text to wrap within the cell */

    vertical-align: top; /* Aligns text to the top of the cell */

  }

</style>

@endsection 
@section('customjs')
	<script>
		var master = '<?php if(isset($master)){ echo json_encode(@$master); }?>';
	</script>
	<script src="{!! asset('public/app-assets/js/bladejs/current_address_details.js') !!}"></script> 
	<script src="{!! asset('public/app-assets/js/bladejs/address_details.js') !!}"></script>
@endsection 
 

