<?php
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Student;
use Session;
use Config;
use App\Component\CustomComponent;
use App\Models\ExamcenterDetail;
use App\Models\Subject;
use App\Exports\CenterCountExlExport;
use App\Helper\CustomHelper;


class CourseMediumWiseBookSubjectCountExlExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	function __construct($request) {
		$this->course = $request->course;
		$this->midium = $request->midium;
		
		
	 }
	 
	public function collection(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
         
		 
		$examyear = CustomHelper::_get_selected_sessions();
		$course =  $this->course;
		$medium = 	$this->midium;
		
		if($medium == 1){
			$medium ='hindi';
		}elseif($medium == 2){
			$medium ='english';
		}
		
		$custom_component_obj = new CustomComponent;
		$aiCenters = $custom_component_obj->getAiCenters();
		//@dd($aiCenters);
	  //@dd($medium."_last_year_book_stock_count");
			$output = array();
			if($course == 10){
				
				$query = "SELECT
                CONCAT(u.ai_code,'-', u.college_name) AS college_name , u.ai_code as  ai_code,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=1 and pb.subject_volume_id=1) as Sub_201_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=1 and pb.subject_volume_id=2) as Sub_201_2,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=2 and pb.subject_volume_id=1) as Sub_202_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=2 and pb.subject_volume_id=2) as Sub_202_2,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=9 and pb.subject_volume_id=1) as Sub_206_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=10 and pb.subject_volume_id=1) as Sub_207_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=41 and pb.subject_volume_id=1) as Sub_208_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=11 and pb.subject_volume_id=1) as Sub_210_1,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=6 and pb.subject_volume_id=1) as Sub_209_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=6 and pb.subject_volume_id=2) as Sub_209_2,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=4 and pb.subject_volume_id=1) as Sub_211_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=4 and pb.subject_volume_id=2) as Sub_211_2,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=4 and pb.subject_volume_id=3) as Sub_211_3,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=3 and pb.subject_volume_id=1) as Sub_212_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=3 and pb.subject_volume_id=2) as Sub_212_2,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=3 and pb.subject_volume_id=3) as Sub_212_3,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=5 and pb.subject_volume_id=1) as Sub_213_1,
				
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=12 and pb.subject_volume_id=1) as Sub_214_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=12 and pb.subject_volume_id=2) as Sub_214_2,
				
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=30 and pb.subject_volume_id=1) as Sub_215_1,
				
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=13 and pb.subject_volume_id=1) as Sub_216_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=13 and pb.subject_volume_id=2) as Sub_216_2,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=13 and pb.subject_volume_id=3) as Sub_216_3,
				
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=17 and pb.subject_volume_id=1) as Sub_222_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=17 and pb.subject_volume_id=2) as Sub_222_2,

				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=40 and pb.subject_volume_id=1) as Sub_223_1,
				
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=16 and pb.subject_volume_id=1) as Sub_225_1,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=16 and pb.subject_volume_id=2) as Sub_225_2,
				(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code and pb.subject_id=16 and pb.subject_volume_id=3) as Sub_225_3

                FROM rs_aicenter_details u
                    left JOIN rs_publication_books pb ON pb.ai_code= u.ai_code AND pb.deleted_at IS null
                    WHERE                   
					pb.exam_year = '$examyear'
					AND  pb.course= 10
                    AND u.deleted_at  IS NULL  
					GROUP BY u.ai_code          
                    ORDER BY
                    u.ai_code ASC;";
				
				$aicenterdatacode = DB::select($query);
				
				$i =1 ;
				
				foreach($aicenterdatacode as $data){
					$output[] = array(
						'id' => $i,
						'college_name' => @$data->college_name,
						'Ai_code' =>   @$data->ai_code,
						"Sub_201_1" => @$data->Sub_201_1, 
						"Sub_201_2" => @$data->Sub_201_2, 						
						"Sub_202_1" => @$data->Sub_202_1,      
						"Sub_202_2" => @$data->sub_202_2,
						"Sub_206_1" => @$data->Sub_206_1, 
						"Sub_207_1" => @$data->Sub_207_1, 
						"Sub_208_1" => @$data->Sub_208_1, 
						"Sub_210_1" => @$data->Sub_210_1, 
						"Sub_209_1" => @$data->Sub_209_1, 
						"Sub_209_2" => @$data->Sub_209_2, 
						"Sub_211_1" => @$data->Sub_211_1, 
						"Sub_211_2" => @$data->Sub_211_2, 
						"Sub_211_3" => @$data->Sub_211_3, 
						"Sub_212_1" => @$data->Sub_212_1, 
						"Sub_212_2" => @$data->Sub_212_2, 
						"Sub_212_3" => @$data->Sub_212_3, 
						"Sub_213_1" => @$data->Sub_213_1, 
						"Sub_214_1" => @$data->Sub_214_1, 
						"Sub_214_2" => @$data->Sub_214_2, 
						"Sub_215_1" => @$data->Sub_215_1, 
						"Sub_216_1" => @$data->Sub_216_1, 
						"Sub_216_2" => @$data->Sub_216_2, 
						"Sub_216_3" => @$data->Sub_216_3, 
						"Sub_222_1" => @$data->Sub_222_1, 
						"Sub_222_2" => @$data->Sub_222_2, 
						"Sub_223_1" => @$data->Sub_223_1, 
						"Sub_225_1" => @$data->Sub_225_1, 
						"Sub_225_2" => @$data->Sub_225_2, 
						"Sub_225_3" => @$data->Sub_225_3				
					);
					$i++;
				}
			}elseif($course == 12){
				$query = "SELECT
						CONCAT(u.ai_code,'-', u.college_name) AS college_name , u.ai_code as  ai_code,
					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=18 and pb.subject_volume_id=1) as Sub_301_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=18 and pb.subject_volume_id=2) as Sub_301_2,
						 
					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=19 and pb.subject_volume_id=1) as Sub_302_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=19 and pb.subject_volume_id=2) as Sub_302_2,


					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=20 and pb.subject_volume_id=1) as Sub_306_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=20 and pb.subject_volume_id=2) as Sub_306_2,


					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=21 and pb.subject_volume_id=1) as Sub_309_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=21 and pb.subject_volume_id=2) as Sub_309_2,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=22 and pb.subject_volume_id=1) as Sub_311_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=22 and pb.subject_volume_id=2) as Sub_311_2,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=23 and pb.subject_volume_id=1) as Sub_312_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=23 and pb.subject_volume_id=2) as Sub_312_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=23 and pb.subject_volume_id=3) as Sub_312_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=24 and pb.subject_volume_id=1) as Sub_313_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=24 and pb.subject_volume_id=2) as Sub_313_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=24 and pb.subject_volume_id=3) as Sub_313_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=25 and pb.subject_volume_id=1) as Sub_314_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=25 and pb.subject_volume_id=2) as Sub_314_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=25 and pb.subject_volume_id=3) as Sub_314_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=26 and pb.subject_volume_id=1) as Sub_315_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=28 and pb.subject_volume_id=1) as Sub_316_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=28 and pb.subject_volume_id=2) as Sub_316_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=27 and pb.subject_volume_id=1) as Sub_317_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=27 and pb.subject_volume_id=2) as Sub_317_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=27 and pb.subject_volume_id=3) as Sub_317_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=29 and pb.subject_volume_id=1) as Sub_318_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=29 and pb.subject_volume_id=2) as Sub_318_2,

						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=31 and pb.subject_volume_id=1) as Sub_319_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=31 and pb.subject_volume_id=2) as Sub_319_2,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=32 and pb.subject_volume_id=1) as Sub_320_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=32 and pb.subject_volume_id=2) as Sub_320_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=32 and pb.subject_volume_id=3) as Sub_320_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=33 and pb.subject_volume_id=1) as Sub_321_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=33 and pb.subject_volume_id=2) as Sub_321_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=33 and pb.subject_volume_id=3) as Sub_321_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=34 and pb.subject_volume_id=1) as Sub_328_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=34 and pb.subject_volume_id=2) as Sub_328_2,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=35 and pb.subject_volume_id=1) as Sub_330_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=36 and pb.subject_volume_id=1) as Sub_331_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=37 and pb.subject_volume_id=1) as Sub_332_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=37 and pb.subject_volume_id=2) as Sub_332_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=37 and pb.subject_volume_id=3) as Sub_332_3,


					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=38 and pb.subject_volume_id=1) as Sub_333_1,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=38 and pb.subject_volume_id=2) as Sub_333_2,
					 
						(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=38 and pb.subject_volume_id=3) as Sub_333_3,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=39 and pb.subject_volume_id=1) as Sub_336_1,
						  
					
					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=52 and pb.subject_volume_id=1) as Sub_391_1	,

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=51 and pb.subject_volume_id=1) as Sub_392_1	,
						  
					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=53 and pb.subject_volume_id=1) as Sub_393_1,
                    
					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=46 and pb.subject_volume_id=1) as Sub_394_1	,  

					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=47 and pb.subject_volume_id=1) as Sub_395_1,
						  
					(select ".$medium."_last_year_book_stock_count from rs_publication_books as pb where pb.ai_code = u.ai_code
						  and pb.subject_id=48 and pb.subject_volume_id=1) as Sub_396_1
					FROM rs_aicenter_details u            
						left JOIN rs_publication_books pb ON pb.ai_code= u.ai_code AND pb.deleted_at IS null                  
					AND pb.exam_year = '$examyear'
					AND pb.course=  12                  
					AND u.deleted_at  IS NULL  
					GROUP BY u.ai_code          
						ORDER BY
						u.ai_code ASC;
";
				 
				$aicenterdatacode = DB::select($query);
			
				$i =1 ;
				foreach(@$aicenterdatacode as $data){
					$output[] = array(
						'id' => $i,
						'college_name' => @$data->college_name,
						'Ai_code' => @$data->ai_code,
						"Sub_301_1" => @$data->Sub_301_1,
						"Sub_301_2" => @$data->Sub_301_2,
						"Sub_302_1" => @$data->Sub_302_1,
						"Sub_302_2" => @$data->Sub_302_2,
						"Sub_306_1" => @$data->Sub_306_1,
						"Sub_306_2" => @$data->Sub_306_2,
						"Sub_309_1" => @$data->Sub_309_1,
						"Sub_309_2" => @$data->Sub_309_2,
						"Sub_311_1" => @$data->Sub_311_1,
						"Sub_311_2" => @$data->Sub_311_2,
						"Sub_312_1" => @$data->Sub_312_1,
						"Sub_312_2" => @$data->Sub_312_2,
						"Sub_312_3" => @$data->Sub_312_3,
						"Sub_313_1" => @$data->Sub_313_1,
						"Sub_313_2" => @$data->Sub_313_2,
						"Sub_313_3" => @$data->Sub_313_3,
						"Sub_314_1" => @$data->Sub_314_1,
						"Sub_314_2" => @$data->Sub_314_2,
						"Sub_314_3" => @$data->Sub_314_3,
						"Sub_315_1" => @$data->Sub_315_1,
						"Sub_316_1" => @$data->Sub_316_1,
						"Sub_316_2" => @$data->Sub_316_2,
						"Sub_317_1" => @$data->Sub_317_1,
						"Sub_317_2" => @$data->Sub_317_2,
						"Sub_317_3" => @$data->Sub_317_3,
						"Sub_318_1" => @$data->Sub_318_1,
						"Sub_318_2" => @$data->Sub_318_2,
						"Sub_319_1" => @$data->Sub_319_1,
						"Sub_319_2" => @$data->Sub_319_2,
						"Sub_320_1" => @$data->Sub_320_1,
						"Sub_320_2" => @$data->Sub_320_2,
						"Sub_320_3" => @$data->Sub_320_3,
						"Sub_321_1" => @$data->Sub_321_1,
						"Sub_321_2" => @$data->Sub_321_2,
						"Sub_321_3" => @$data->Sub_321_3,
						"Sub_328_1" => @$data->Sub_328_1,
						"Sub_328_2" => @$data->Sub_328_2,
						"Sub_330_1" => @$data->Sub_330_1,
						"Sub_331_1" => @$data->Sub_331_1,
						"Sub_332_1" => @$data->Sub_332_1,
						"Sub_332_2" => @$data->Sub_332_2,
						"Sub_332_3" => @$data->Sub_332_3,
						"Sub_333_1" => @$data->Sub_333_1,
						"Sub_333_2" => @$data->Sub_333_2,
						"Sub_333_3" => @$data->Sub_333_3,
						"Sub_336_1" => @$data->Sub_336_1,
						"Sub_391_1" => @$data->Sub_391_1,
						"Sub_392_1" => @$data->Sub_392_1,
						"Sub_393_1" => @$data->Sub_393_1,
						"Sub_394_1" => @$data->Sub_394_1,
						"Sub_395_1" => @$data->Sub_395_1,
						"Sub_396_1" => @$data->Sub_396_1,
					);
					$i++;
				}
			}
		
		return collect($output);
	}
	
	public function headings(): array{

		if($this->course  == 10){
		 return ["Sr. No.","Ai Center Name","Ai_Code","Sub_201_1","Sub_201_2","Sub_202_1","Sub_202_2","Sub_206_1","Sub_207_1","Sub_208_1","Sub_210_1","Sub_209_1","Sub_209_2","Sub_211_1","Sub_211_2","Sub_211_3","Sub_212_1","Sub_212_2","Sub_212_3","Sub_213_1","Sub_214_1","Sub_214_2","Sub_215_1","Sub_216_1","Sub_216_2","Sub_216_3","Sub_222_1","Sub_222_2","Sub_223_1","Sub_225_1","Sub_225_2","Sub_225_3"];	
		}
		elseif($this->course  == 12){
		return ["Sr. No.","Ai_Center_Name","Ai_Code","Sub_301_1","Sub_301_2","Sub_302_1","Sub_302_2","Sub_306_1","Sub_306_2","Sub_309_1","Sub_309_2","Sub_311_1","Sub_311_2","Sub_312_1","Sub_312_2","Sub_312_3","Sub_313_1","Sub_313_2","Sub_313_3","Sub_314_1","Sub_314_2","Sub_314_3","Sub_315_1","Sub_316_1","Sub_316_2","Sub_317_1","Sub_317_2","Sub_317_3","Sub_318_1","Sub_318_2","Sub_319_1","Sub_319_2","Sub_320_1","Sub_320_2","Sub_320_3","Sub_321_1","Sub_321_2","Sub_321_3","Sub_328_1","Sub_328_2","Sub_330_1","Sub_331_1","Sub_332_1","Sub_332_2","Sub_332_3","Sub_333_1","Sub_333_2","Sub_333_3","Sub_336_1","Sub_391_1","Sub_392_1","Sub_393_1","Sub_394_1","Sub_395_1","Sub_396_1"];
		}
	}
}

