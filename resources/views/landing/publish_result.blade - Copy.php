$html = "<table border='1'>";
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
		$html .= "<td>" . $studentDetails[$stk]['name'] . "</td>";
			$html .= "<td>";
				$html .= "<table border='1'>"; 
					$html .= "<tr>";
					$html .= "<th>Sr. No.</th>";
					$html .= "<th>Document Name</th>";
					$html .= "<th>Rejected Reason</th>"; 
					$html .= "</tr>";
					foreach($list as $k => $v){ 
						$html .= "<tr >"; 
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