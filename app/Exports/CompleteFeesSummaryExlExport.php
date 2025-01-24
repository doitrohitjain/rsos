<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Student;
use Session;
use App\Component\CustomComponent;
use App\Models\User;
use App\Helper\CustomHelper;

class CompleteFeesSummaryExlExport implements FromCollection,WithHeadings,ShouldAutoSize
{ 
   
	function __construct($request) {
		$this->course = $request->course;
		$this->stream = $request->stream;
		$this->gender_id = $request->gender_id;
		$this->ai_code = $request->ai_code;
		//$this->feeType = $request->feeType;
		$this->are_you_from_rajasthan = $request->are_you_from_rajasthan;
	}
  							
	public function collection(){
		$output = array();
		$i =1 ; 
        $gender_id = $this->gender_id;
		$course=$this->course;
		$exam_month = $this->stream;
		$exam_year = CustomHelper::_get_selected_sessions();
		$are_you_from_rajasthan = $this->are_you_from_rajasthan;
		$ai_code = $this->ai_code;  
		
		$countStudentBaseQuery = $this->countStudentBaseQuery($gender_id,$exam_month,$exam_year,$are_you_from_rajasthan,$ai_code,$course);
		$feesQuery = $this->feesQuery($gender_id,$exam_month,$exam_year,$are_you_from_rajasthan,$ai_code,$course);
		
		$countStudentBaseData = DB::select($countStudentBaseQuery);
		
		$studentTotalCommon = 0;	
		$studentTotalSc = 0;	
		$studentTotalSt = 0;	
		$studentTotalGrand = 0;	

		$outputTemp = array();
		foreach($countStudentBaseData as $key => $data){
			if(in_array($data->category_a,array(1,2,3,4,5))){
				if(in_array($data->category_a,array(1,4,5))){
					$category_a = "Common";
					$varname = "studentTotal".$category_a;
					$$varname += $data->count;
				}else if(in_array($data->category_a,array(2))){
					$category_a = "Sc";
					$varname = "studentTotal".$category_a;
					$$varname += $data->count;
				}else if(in_array($data->category_a,array(3))){
					$category_a = "St";
					$varname = "studentTotal".$category_a;
					$$varname += $data->count; 
				}
				$studentTotalGrand += $data->count;
				$outputTemp[$category_a] = array(
					'course' => $data->course,
					'category_a' => $category_a,
					'exam_month' => $data->exam_month,
					'count' => $$varname
				);
				$i++;
			}
		}
		// dd($outputTemp);
		// echo ($feesQuery);die;
		$feesData = DB::select($feesQuery);
		$data = array();

		$regCountGrandTotal = 0;
		$regCollectionGrandTotal = 0;

		$tempDataSc = $tempDataSt = $tempDataCommon = array();
		$tempDataSummary = array();		
		$tempDataCommonReg = array();
		$tempDataCommonPractical = array();
		$tempDataCommonForward = array();
		
		foreach($feesData as $keyTest => $dataTest){

			if($dataTest->org_registration_fees >= 0 ){
				$tempDataCommon[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataCommon[$dataTest->org_registration_fees]['count'] = 
				$tempDataCommon[$dataTest->org_registration_fees]['collection'] =  

				$tempDataSc[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataSc[$dataTest->org_registration_fees]['count'] = 
				$tempDataSc[$dataTest->org_registration_fees]['collection'] = 

				$tempDataSt[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataSt[$dataTest->org_registration_fees]['count'] = 
				$tempDataSt[$dataTest->org_registration_fees]['collection'] = 


				$tempDataCommonPr[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataCommonPr[$dataTest->org_registration_fees]['count'] = 
				$tempDataCommonPr[$dataTest->org_registration_fees]['collection'] =  

				$tempDataScPr[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataScPr[$dataTest->org_registration_fees]['count'] = 
				$tempDataScPr[$dataTest->org_registration_fees]['collection'] = 

				$tempDataStPr[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataStPr[$dataTest->org_registration_fees]['count'] = 
				$tempDataStPr[$dataTest->org_registration_fees]['collection'] = 

				$tempDataCommonAdSub[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataCommonAdSub[$dataTest->org_registration_fees]['count'] = 
				$tempDataCommonAdSub[$dataTest->org_registration_fees]['collection'] =  

				$tempDataScAdSub[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataScAdSub[$dataTest->org_registration_fees]['count'] = 
				$tempDataScAdSub[$dataTest->org_registration_fees]['collection'] = 

				$tempDataStAdSub[$dataTest->org_registration_fees]['fee_per_student'] = 
				$tempDataStAdSub[$dataTest->org_registration_fees]['count'] = 
				$tempDataStAdSub[$dataTest->org_registration_fees]['collection'] = 0;
	
			}
			

			if(@$dataTest->registration_fees && $dataTest->registration_fees == 'registration_fees' && @$dataTest->org_registration_fees){
				$tempDataCommonReg[$dataTest->org_registration_fees] = $dataTest->org_registration_fees;
			}
			if(@$dataTest->registration_fees && $dataTest->registration_fees == 'practical_fees' && @$dataTest->org_registration_fees){
				$tempDataCommonPractical[$dataTest->org_registration_fees] = $dataTest->org_registration_fees;
			}
			if(@$dataTest->registration_fees && $dataTest->registration_fees == 'add_sub_fees' && @$dataTest->org_registration_fees){
				$tempDataCommonForward[$dataTest->org_registration_fees] = $dataTest->org_registration_fees;
			} 
		} 
		
		// dd($feesData);
		foreach($feesData as $key => $data){
			// start1 start
				$feeType = "registration_fees";
				if(@$data->registration_fees && $data->registration_fees == $feeType){
					if(@$data->category_a){
						$org_registration_fees_cat_wise_Common = 
						$count_cat_wise_Common = 
						$collection_cat_wise_Common = 
						$org_registration_fees_cat_wise_Sc =  
						$count_cat_wise_Sc = 
						$collection_cat_wise_Sc =  
						$org_registration_fees_cat_wise_St = 
						$count_cat_wise_St =  
						$collection_cat_wise_St = 0;

						if(in_array($data->category_a,array(1,4,5))){
							$tempDataCommon[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataCommon[$data->org_registration_fees]['count'] += $data->count;
							$tempDataCommon[$data->org_registration_fees]['collection'] += $data->collection;
							$org_registration_fees_cat_wise_Common += $data->org_registration_fees;
							$count_cat_wise_Common += $data->count;
							$collection_cat_wise_Common += $data->collection;
							
							
						}else if(in_array($data->category_a,array(2))){
							$tempDataSc[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataSc[$data->org_registration_fees]['count'] += $data->count;
							$tempDataSc[$data->org_registration_fees]['collection'] += $data->collection;
							
							$org_registration_fees_cat_wise_Sc += $data->org_registration_fees;
							$count_cat_wise_Sc += $data->count;
							$collection_cat_wise_Sc += $data->collection;

						}else if(in_array($data->category_a,array(3))){
							$tempDataSt[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataSt[$data->org_registration_fees]['count'] += $data->count;
							$tempDataSt[$data->org_registration_fees]['collection'] += $data->collection;

							$org_registration_fees_cat_wise_St += $data->org_registration_fees;
							$count_cat_wise_St += $data->count;
							$collection_cat_wise_St += $data->collection;
						}

						$regCountGrandTotal += $data->count;
						$regCollectionGrandTotal += $data->collection;
					} 
					foreach($tempDataCommon as $org_registration_fees => $vv){
						if($tempDataCommon[$org_registration_fees]['count'] > 0){
							$outputTemp['Common']['registrion_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['Common']['registrion_fees'][$org_registration_fees]['count'] = $tempDataCommon[$org_registration_fees]['count'];
							$outputTemp['Common']['registrion_fees'][$org_registration_fees]['collection'] = $tempDataCommon[$org_registration_fees]['collection'];
						}
					} 
					foreach($tempDataSc as $org_registration_fees => $vv){
						if($tempDataSc[$org_registration_fees]['count'] > 0){
							$outputTemp['Sc']['registrion_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['Sc']['registrion_fees'][$org_registration_fees]['count'] = $tempDataSc[$org_registration_fees]['count'];
							$outputTemp['Sc']['registrion_fees'][$org_registration_fees]['collection'] = $tempDataSc[$org_registration_fees]['collection'];
						}
					} 
					foreach($tempDataSt as $org_registration_fees => $vv){
						if($tempDataSt[$org_registration_fees]['count'] > 0){
							$outputTemp['St']['registrion_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['St']['registrion_fees'][$org_registration_fees]['count'] = $tempDataSt[$org_registration_fees]['count'];
							$outputTemp['St']['registrion_fees'][$org_registration_fees]['collection'] = $tempDataSt[$org_registration_fees]['collection'];
						}
					}  
				}   
			// start1 end 
			
			//start2 start
				$feeType = "practical_fees";
				if(@$data->registration_fees && $data->registration_fees == $feeType){
					 
					
					if(@$data->category_a){
						$org_registration_fees_cat_wise_Common = 
						$count_cat_wise_Common = 
						$collection_cat_wise_Common = 
						$org_registration_fees_cat_wise_Sc =  
						$count_cat_wise_Sc = 
						$collection_cat_wise_Sc =  
						$org_registration_fees_cat_wise_St = 
						$count_cat_wise_St =  
						$collection_cat_wise_St = 0;
				
						

						if(in_array($data->category_a,array(1,4,5))){
							$tempDataCommonPr[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataCommonPr[$data->org_registration_fees]['count'] += $data->count;
							$tempDataCommonPr[$data->org_registration_fees]['collection'] += $data->collection;
							$org_registration_fees_cat_wise_Common += $data->org_registration_fees;
							$count_cat_wise_Common += $data->count;
							$collection_cat_wise_Common += $data->collection; 
						}else if(in_array($data->category_a,array(2))){
							$tempDataScPr[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataScPr[$data->org_registration_fees]['count'] += $data->count;
							$tempDataScPr[$data->org_registration_fees]['collection'] += $data->collection;
								
							$org_registration_fees_cat_wise_Sc += $data->org_registration_fees;
							$count_cat_wise_Sc += $data->count;
							$collection_cat_wise_Sc += $data->collection;
				
						}else if(in_array($data->category_a,array(3))){
							$tempDataStPr[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataStPr[$data->org_registration_fees]['count'] += $data->count;
							$tempDataStPr[$data->org_registration_fees]['collection'] += $data->collection;
				
							$org_registration_fees_cat_wise_St += $data->org_registration_fees;
							$count_cat_wise_St += $data->count;
							$collection_cat_wise_St += $data->collection;
						}
				
						$regCountGrandTotal += $data->count;
						$regCollectionGrandTotal += $data->collection;
					}  
					
					foreach(@$tempDataCommonPr as $org_registration_fees => $vv){
						if($tempDataCommonPr[$org_registration_fees]['count'] > 0){
							$outputTemp['Common']['practical_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['Common']['practical_fees'][$org_registration_fees]['count'] = $tempDataCommonPr[$org_registration_fees]['count'];
							$outputTemp['Common']['practical_fees'][$org_registration_fees]['collection'] = $tempDataCommonPr[$org_registration_fees]['collection'];
						}
					} 
					foreach(@$tempDataScPr as $org_registration_fees => $vv){
						if($tempDataScPr[$org_registration_fees]['count'] > 0){
							$outputTemp['Sc']['practical_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['Sc']['practical_fees'][$org_registration_fees]['count'] = $tempDataScPr[$org_registration_fees]['count'];
							$outputTemp['Sc']['practical_fees'][$org_registration_fees]['collection'] = $tempDataScPr[$org_registration_fees]['collection'];
						}
					} 
					foreach(@$tempDataStPr as $org_registration_fees => $vv){
						if($tempDataStPr[$org_registration_fees]['count'] > 0){
							$outputTemp['St']['practical_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['St']['practical_fees'][$org_registration_fees]['count'] = $tempDataStPr[$org_registration_fees]['count'];
							$outputTemp['St']['practical_fees'][$org_registration_fees]['collection'] = $tempDataStPr[$org_registration_fees]['collection'];
						}
					} 
				} 	 
			//start2 end 
 
			//start3 start
				$feeType = "add_sub_fees";
				if(@$data->registration_fees && $data->registration_fees == $feeType){
					
					
					if(@$data->category_a){
						$org_registration_fees_cat_wise_Common = 
						$count_cat_wise_Common = 
						$collection_cat_wise_Common = 
						$org_registration_fees_cat_wise_Sc =  
						$count_cat_wise_Sc = 
						$collection_cat_wise_Sc =  
						$org_registration_fees_cat_wise_St = 
						$count_cat_wise_St =  
						$collection_cat_wise_St = 0;
				
						

						if(in_array($data->category_a,array(1,4,5))){
							$tempDataCommonAdSub[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataCommonAdSub[$data->org_registration_fees]['count'] += $data->count;
							$tempDataCommonAdSub[$data->org_registration_fees]['collection'] += $data->collection;
							$org_registration_fees_cat_wise_Common += $data->org_registration_fees;
							$count_cat_wise_Common += $data->count;
							$collection_cat_wise_Common += $data->collection; 
						}else if(in_array($data->category_a,array(2))){
							$tempDataScAdSub[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataScAdSub[$data->org_registration_fees]['count'] += $data->count;
							$tempDataScAdSub[$data->org_registration_fees]['collection'] += $data->collection;
								
							$org_registration_fees_cat_wise_Sc += $data->org_registration_fees;
							$count_cat_wise_Sc += $data->count;
							$collection_cat_wise_Sc += $data->collection;
				
						}else if(in_array($data->category_a,array(3))){
							$tempDataStAdSub[$data->org_registration_fees]['fee_per_student'] += $data->org_registration_fees;
							$tempDataStAdSub[$data->org_registration_fees]['count'] += $data->count;
							$tempDataStAdSub[$data->org_registration_fees]['collection'] += $data->collection;
				
							$org_registration_fees_cat_wise_St += $data->org_registration_fees;
							$count_cat_wise_St += $data->count;
							$collection_cat_wise_St += $data->collection;
						}
				
						$regCountGrandTotal += $data->count;
						$regCollectionGrandTotal += $data->collection;
					}  
					
					foreach(@$tempDataCommonAdSub as $org_registration_fees => $vv){
						if($tempDataCommonAdSub[$org_registration_fees]['count'] > 0){
							$outputTemp['Common']['add_sub_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['Common']['add_sub_fees'][$org_registration_fees]['count'] = $tempDataCommonAdSub[$org_registration_fees]['count'];
							$outputTemp['Common']['add_sub_fees'][$org_registration_fees]['collection'] = $tempDataCommonAdSub[$org_registration_fees]['collection'];
						}
					} 
					foreach(@$tempDataScAdSub as $org_registration_fees => $vv){
						if($tempDataScAdSub[$org_registration_fees]['count'] > 0){
							$outputTemp['Sc']['add_sub_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['Sc']['add_sub_fees'][$org_registration_fees]['count'] = $tempDataScAdSub[$org_registration_fees]['count'];
							$outputTemp['Sc']['add_sub_fees'][$org_registration_fees]['collection'] = $tempDataScAdSub[$org_registration_fees]['collection'];
						}
					} 
					foreach(@$tempDataStAdSub as $org_registration_fees => $vv){
						if($tempDataStAdSub[$org_registration_fees]['count'] > 0){
							$outputTemp['St']['add_sub_fees'][$org_registration_fees]['fee_per_student'] = $org_registration_fees;
							$outputTemp['St']['add_sub_fees'][$org_registration_fees]['count'] = $tempDataStAdSub[$org_registration_fees]['count'];
							$outputTemp['St']['add_sub_fees'][$org_registration_fees]['collection'] = $tempDataStAdSub[$org_registration_fees]['collection'];
						}
					} 
				} 	 
			//start3 end 
		}  
	
		$icount = 0;
		foreach(@$outputTemp as $categoryAName => $value){
			if(@$value['registrion_fees']){
				foreach($value['registrion_fees'] as $kkey => $vvalue){ 
					$output[$icount]['type'] = 'Registration Fees';
					$output[$icount]['course'] = @$value['course'];
					$output[$icount]['category_a'] = @$categoryAName;					
					if($categoryAName == "Common"){
						$output[$icount]['category_a'] = "GEN.OBC.SOBC";	
					}
					$output[$icount]['stream'] = @$value['exam_month'];
					$output[$icount]['student_count'] = @$value['count'];
					$output[$icount]['fee_per_student'] = @$vvalue['fee_per_student'];
					$output[$icount]['count'] = @$vvalue['count'];
					$output[$icount]['collection'] = @$vvalue['collection']; 
					$icount++;
				}   
			} 
		}

		foreach(@$outputTemp as $categoryAName => $value){
			if(@$value['practical_fees']){
				foreach($value['practical_fees'] as $kkey => $vvalue){ 
					$output[$icount]['type'] = 'Practical Fees';
					$output[$icount]['course'] = @$value['course'];
					$output[$icount]['category_a'] = @$categoryAName;					
					if($categoryAName == "Common"){
						$output[$icount]['category_a'] = "GEN.OBC.SOBC";	
					}
					$output[$icount]['stream'] = @$value['exam_month'];
					$output[$icount]['student_count'] = null;
					$output[$icount]['fee_per_student'] = @$vvalue['fee_per_student'];
					$output[$icount]['count'] = @$vvalue['count'];
					$output[$icount]['collection'] = @$vvalue['collection']; 
					$icount++;
				}   
			} 
		}

	
		foreach(@$outputTemp as $categoryAName => $value){
			$output[$icount]['type'] = 'Forwarded Fees';
			$output[$icount]['course'] = @$value['course'];
			$output[$icount]['category_a'] = @$categoryAName;					
			if($categoryAName == "Common"){
				$output[$icount]['category_a'] = "GEN.OBC.SOBC";	
			}
			$output[$icount]['stream'] = @$value['exam_month'];
			$output[$icount]['student_count'] = null;
			$output[$icount]['fee_per_student'] = 50;
			$output[$icount]['count'] = @$value['count'];
			$output[$icount]['collection'] = (@$value['count'] * 50); 
			$icount++;
		}

		foreach(@$outputTemp as $categoryAName => $value){
			if(@$value['add_sub_fees']){
				foreach($value['add_sub_fees'] as $kkey => $vvalue){ 
					$output[$icount]['type'] = 'Additional Fees';
					$output[$icount]['course'] = @$value['course'];
					$output[$icount]['category_a'] = @$categoryAName;					
					if($categoryAName == "Common"){
						$output[$icount]['category_a'] = "GEN.OBC.SOBC";	
					}
					$output[$icount]['stream'] = @$value['exam_month'];
					$output[$icount]['student_count'] = null;
					$output[$icount]['fee_per_student'] = @$vvalue['fee_per_student'];
					$output[$icount]['count'] = @$vvalue['count'];
					$output[$icount]['collection'] = @$vvalue['collection']; 
					$icount++;
				}   
			} 
		}

		
		
		return collect($output);
    
    }
	
	public function countStudentBaseQuery($gender_id = null,$exam_month = null,$exam_year = null,$are_you_from_rajasthan = null,$ai_code = null,$course=null){
		$countStudentBaseQuery = "select s.exam_month,s.course,a.category_a,s.exam_month,count(s.id) as count
			from rs_students s 
			inner join rs_applications a on a.student_id = s.id and a.deleted_at is null 
			where   s.is_eligible = 1 and s.deleted_at is null ";
		if(@$gender_id){
			$countStudentBaseQuery .= " and s.gender_id = " . $gender_id;
		}
		if(@$exam_year){
			$countStudentBaseQuery .= " and s.exam_year = " . $exam_year;
		}
		if(@$course){
			$countStudentBaseQuery .= " and s.course = " . $course;
		}
		if(@$exam_month){
			$countStudentBaseQuery .= " and s.exam_month = " . $exam_month;
		}
		if(@$are_you_from_rajasthan){
			$countStudentBaseQuery .= " and s.are_you_from_rajasthan = " . $are_you_from_rajasthan;
		}
		if(@$ai_code){
			$countStudentBaseQuery .= " and s.ai_code = " . $ai_code;
		} 
		$countStudentBaseQuery .= " group by a.category_a order by a.category_a,s.exam_month;";
		return $countStudentBaseQuery;

	}
	
	public function feesQuery($gender_id = null,$exam_month = null,$exam_year = null,$are_you_from_rajasthan = null,$ai_code = null,$course=null){ 
		$gender_idQuery = $exam_yearQuery = $courseQuery = $exam_monthQuery = $are_you_from_rajasthanQuery = $ai_codeQuery = null;
		if(@$gender_id){
			$gender_idQuery .= " and s.gender_id = " . $gender_id;
		}
		if(@$exam_year){
			$exam_yearQuery .= " and s.exam_year = " . $exam_year;
		}
		if(@$course){
			$courseQuery .= " and s.course = " . $course;
		}
		if(@$exam_month){
			$exam_monthQuery .= " and s.exam_month = " . $exam_month;
		}
		if(@$are_you_from_rajasthan){
			$are_you_from_rajasthanQuery .= " and s.are_you_from_rajasthan = " . $are_you_from_rajasthan;
		}
		if(@$ai_code){
			$ai_codeQuery .= " and s.ai_code = " . $ai_code;
		} 
	
		
		
		$feesQuery = "select
			'registration_fees',
			a.category_a,
			s.course,
			count( s.id ) AS count,
			s.exam_month,
			sf.org_registration_fees,
			(
			sf.org_registration_fees * count( s.id )) AS collection 
			FROM
			rs_students s
			INNER JOIN rs_applications a ON a.student_id = s.id 
			AND a.deleted_at
			IS NULL INNER JOIN rs_student_org_fees sf ON sf.student_id = s.id 
			AND sf.deleted_at IS NULL 
			WHERE
			s.is_eligible = 1 
			" .$exam_yearQuery . " 
			" .$gender_idQuery . "
			" .$courseQuery . " 
			" .$exam_monthQuery . " 
			" .$are_you_from_rajasthanQuery . " 
			" .$ai_codeQuery . " 
			AND s.deleted_at IS NULL 
			GROUP BY
			a.category_a,
			s.course,
			s.exam_month,
			sf.org_registration_fees UNION
			SELECT
			'practical_fees',
			a.category_a,
			s.course,
			count( s.id ) AS count,
			s.exam_month,
			sf.org_practical_fees,
			(
			sf.org_practical_fees * count( s.id )) AS collection 
			FROM
			rs_students s
			INNER JOIN rs_applications a ON a.student_id = s.id 
			AND a.deleted_at
			IS NULL INNER JOIN rs_student_org_fees sf ON sf.student_id = s.id 
			AND sf.deleted_at IS NULL 
			WHERE
			s.is_eligible = 1 
			" .$exam_yearQuery . " 
			" .$gender_idQuery . "
			" .$courseQuery . " 
			" .$exam_monthQuery . " 
			" .$are_you_from_rajasthanQuery . " 
			" .$ai_codeQuery . " 
			AND s.deleted_at IS NULL 
			GROUP BY
			a.category_a,
			s.course,
			s.exam_month,
			sf.org_practical_fees 
			UNION
			SELECT
			'forward_fees',
			a.category_a,
			s.course,
			count( s.id ) AS count,
			s.exam_month,
			sf.org_forward_fees,
			(
			sf.org_forward_fees * count( s.id )) AS collection 
			FROM
			rs_students s
			INNER JOIN rs_applications a ON a.student_id = s.id 
			AND a.deleted_at
			IS NULL INNER JOIN rs_student_org_fees sf ON sf.student_id = s.id 
			AND sf.deleted_at IS NULL 
			WHERE
			s.is_eligible = 1 
			" .$exam_yearQuery . " 
			" .$gender_idQuery . "
			" .$courseQuery . " 
			" .$exam_monthQuery . " 
			" .$are_you_from_rajasthanQuery . " 
			" .$ai_codeQuery . " 
			AND s.deleted_at IS NULL 
			GROUP BY
			a.category_a,
			s.course,
			s.exam_month,
			sf.org_forward_fees UNION
			SELECT
			'add_sub_fees',
			a.category_a,
			s.course,
			count( s.id ) AS count,
			s.exam_month,
			( sf.org_readm_exam_fees   ) as `org_add_sub_fees`,
			(( sf.org_readm_exam_fees )  * count( s.id )) AS collection
			FROM
			rs_students s
			INNER JOIN rs_applications a ON a.student_id = s.id 
			AND a.deleted_at
			IS NULL INNER JOIN rs_student_org_fees sf ON sf.student_id = s.id 
			AND sf.deleted_at IS NULL 
			WHERE
			s.is_eligible = 1 
			" .$exam_yearQuery . " 
			" .$gender_idQuery . "
			" .$courseQuery . " 
			" .$exam_monthQuery . " 
			" .$are_you_from_rajasthanQuery . " 
			" .$ai_codeQuery . " 
			AND s.deleted_at IS NULL 
			GROUP BY
			a.category_a,
			s.course,
			s.exam_month,
			( sf.org_readm_exam_fees);";
			// echo $feesQuery;die;
		return $feesQuery;
	} 
	
	public function headings(): array{
        return ["Fee Type","Course","Category","Stream","Students Total",
		"Fees Per Student (Fee Wise)","Count (Fee Wise)","Fees Collection (Fee Wise)"
		];
    }
}

