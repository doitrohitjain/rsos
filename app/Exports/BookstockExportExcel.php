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
use App\Http\Controllers\Controller;
use App\Component\BookRequirementCustomComponent;

class BookstockExportExcel implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	/*
	protected $id;
	function __construct($id) {
       // $this->id = $id;
	}
	*/
	
    public function collection(){
		$output = array();
		$formId = "Books_Requirement";
		$custom_controller_obj = new Controller;
		$BookRequirementCustomComponent = new BookRequirementCustomComponent();	
		$subjectsList = $custom_controller_obj->subjectList();
		$combo_name = 'book_publication_volumes';$book_publication_volumes = $custom_controller_obj->master_details($combo_name);
		$result = $BookRequirementCustomComponent->getBooksRequrementData($formId,false);
		$i =1 ;
		foreach($result as $data){
            $output[] = array(
				'id' => $i,
				'ai_code' => @$data['ai_code'],
                'course' => @$data['course'],
				'subject_id'=> @$subjectsList[$data['subject_id']],
				'subject_volume_id'=> @$book_publication_volumes[$data['subject_volume_id']],
				'hindi_last_year_book_stock_count'=> @$data['hindi_last_year_book_stock_count'],
				'english_last_year_book_stock_count'=> @$data['english_last_year_book_stock_count'],
				
				
            );
			$i++;
    }
	return collect($output);
	}
	
	public function headings(): array{
        return ["Sr. No.", "Ai Code","Course","Subject","Volume","Hindi Last Year Book Stock Count","English Last Year Book Stock Count"];
    } 
	
	 
}