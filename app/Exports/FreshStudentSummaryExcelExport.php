<?php
  
namespace App\Exports;
  
use App\Models\MasterQuerieExcel;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
 
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FreshStudentSummaryExcelExport implements FromCollection,WithHeadings,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */ 
	protected $studentDataCols;
	protected $studentData;
   
	function __construct($studentDataCols,$studentData) {
        $this->studentDataCols = $studentDataCols; 
		$this->studentData = $studentData;

	}
    public function collection()
    {
	  $studentDataCols = $this->studentData;
	  $result = $studentDataCols;
	  return collect($result); 
    }
	
	public function headings(): array
    {
        $table_column_array = array();
        $studentDataCols = $this->studentDataCols;
        $result = $studentDataCols;
		$tables = array();
        foreach ($result as $res){
            $tables[] =  $res;
        }
        return $tables;
    }
	
	
	 public function registerEvents(): array
    {

		//border style
		$styleArray = [
				'borders' => [
					'outline' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['argb' => 'FFFF0000'],
						],
					],
				];	
		
		//font style	
		$styleArray1 = [
						'font' => [
							'bold' => true,
							]
						];
		
		//column  text alignment
		$styleArray2 = array(
			'alignment' => array(
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				 )
		);				
		
		//$styleArray3 used for vertical alignment 
		$styleArray3 = array(
			'alignment' => array(
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				 )
		);
		

		$styleArray4 = array(
		'fill' => [
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
			'startColor' => [
			'argb' => 'FFA0A0A0',
			],
			'endColor' => [
				'argb' => 'FFFFFFFF',
			]
		]
					);
		
		$styleArray5 = array(
						'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        
        'startColor' => [
            'argb' => 'E0E0E0',
        ]]);
		
		
			
				
        return [
            AfterSheet::class => function(AfterSheet $event) use ($styleArray, $styleArray1, $styleArray2, 
            $styleArray3, $styleArray4 , $styleArray5)
			{
				$cellRange = 'A1:AZ1'; // All headers
				$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(13);
				$event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray); 
							
				//Heading formatting...
			 	$event->getSheet()->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);		
				$event->getSheet()->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray1);
							
							
				//used for making bold
				$event->getSheet()->getDelegate()->getStyle('A')->applyFromArray($styleArray1);
							
							
				//column width set							
				$event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20); 
				// $event ->sheet->getStyle('A:AZ')->getAlignment()->setWrapText(true);
							
				//text center columns...
				// $event ->sheet->getStyle('A1:AZ1')->applyFromArray($styleArray2);
				
				
							
				//sums color formatting...
				$event->sheet->getStyle('A1:AZ1')->applyFromArray($styleArray4);
				$workSheet = $event->sheet->getDelegate();
                $workSheet->freezePane('A2'); // freezing here
				$workSheet->setAutoFilter('A1:AZ1');

				// $event ->sheet->getStyle('A1:G1')->applyFromArray($styleArray5);
				// $event->sheet->getStyle('A1:G1')->setBackground('#CCCCCC');  
				
				
				 // $event->sheet->getDelegate()->getStyle('A1:AZ1')
                                // ->getFont()
                                // ->getColor()
                                // ->setARGB('DD4B39');
								
				// $event->getStyle($cellRange)->setAllBorders('thin');
				// $event->sheet->freezeFirstRowAndColumn();
				// $event->sheet->setAutoFilter();
				// $headCellRange = 'A1:AZ1';
				// $event->sheet->getStyle($headCellRange)->applyFromArray([
                    // 'borders' => [
                        // 'allBorders' => [
                            // 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            // 'color' => ['argb' => '000000'],
                        // ],
                    // ],
				// ]); 
				
				
				
		    },
        ];
    }
	
 

 
	
	 
}