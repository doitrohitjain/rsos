<?php
  
namespace App\Exports;
use App\Models\ReportMasterQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ReportMasterQueryExcelExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
	protected $id;
	function __construct($id) {
        $this->id = $id;
    }
    public function collection()
    {
       
	  $masterquerieexcel = ReportMasterQuery::findOrFail($this->id);
      
	  $masterquerieexcels = $masterquerieexcel->sql;
	  $result = DB::select(DB::raw($masterquerieexcels));
	  return collect($result);
    
    }
	
	public function headings(): array
    {
        $masterquerieexcel = ReportMasterQuery::findOrFail($this->id);
        
        $table_column_array = array();
      
        $masterquerieexcels = $masterquerieexcel->sql;
        $result = DB::select(DB::raw($masterquerieexcels)); 
        foreach ($result as $res){
            $tables[] =  $res;
        }
        foreach ($result as $record) {
            $record = (array)$record;
            $table_column_array = array_keys($record);
            foreach ($table_column_array as $key => $name) {
                $table_column_array[$key];
            }
        }
        return $table_column_array;
    }
	 
}