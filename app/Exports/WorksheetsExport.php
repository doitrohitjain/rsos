<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorksheetsExport implements FromCollection, WithHeadings
{
    
	function __construct($request) {
		$this->start_date = $request['start_date'];
		$this->end_date = $request['end_date'];
		$this->name = $request['name'];
		$this->email = $request['email'];
	}
	 
	 
    public function collection()
    { 
		$start_date =  $this->start_date;
		$end_date =  $this->end_date;
		$name =  $this->name;
		$email =  $this->email;
		$conditions=null;
		if(@$name){
			$conditions['user_id'] = User::where('name',$name)->pluck('id');
		} 
		if(@$email){
			$conditions['user_id'] = User::where('email',$email)->pluck('id');
		}
		
		if(auth()->user()->role == 'admin'){
			
		}else{
			$conditions['user_id'] = auth()->user()->id;
		}
	
		$worksheets = Worksheet::where($conditions)->whereBetween('date', [$start_date, $end_date])->get(); 
		$i=0;
		
		$userNameList = User::pluck('name','id'); 
		$userEmailList = User::pluck('email','id'); 
		$output= [];
		foreach($worksheets as $k => $worksheet){
			$i++;
			$output[$i][] = $i;
			$output[$i][] = @$userNameList[$worksheet->user_id] . " (" . @$userEmailList[$worksheet->user_id] . ")";
			$output[$i][] = $worksheet->date;
			$output[$i][] = $worksheet->task;
			$output[$i][] = $worksheet->description;
			$output[$i][] = ucfirst($worksheet->status);
			$output[$i][] = date(@$worksheet->created_at);
			$output[$i][] = date(@$worksheet->updated_at);
		} 
		
		return collect($output);
		
    }

    /**
     * Add headings to the export file
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Sr.No.', 'Staff', 'Date',  'Title', 'Description', 'Status','Created At', 'Updated At'
        ];
    }
}
