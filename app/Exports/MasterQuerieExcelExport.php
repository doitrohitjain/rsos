<?php
  
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
class MasterQuerieExcelExport implements FromCollection,WithHeadings
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
	  $masterquerieexcel = MasterQuerieExcel::findOrFail($this->id);
	  $masterquerieexcels = $masterquerieexcel->text;
	  $result = DB::select(DB::raw($masterquerieexcels));
	  return collect($result);
    
    }
	
	public function headings(): array
    {
		ini_set('memory_limit', '-1');
	    ini_set('max_execution_time', '0');
        $masterquerieexcel = MasterQuerieExcel::findOrFail($this->id);
        $table_column_array = array();
        $masterquerieexcels = $masterquerieexcel->text;
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