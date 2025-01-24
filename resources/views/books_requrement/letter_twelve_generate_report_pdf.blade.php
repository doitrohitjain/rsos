@extends('layouts.pdf')
@section('content')



<!-- 
		स्टॉक में शेष पाठ्य पुस्तकों का प्रपत्र
		प्रपत्र - 12 
		विद्यालय का नाम कोड  

		पाठ्य पुस्तकों का शुद्ध मांग प्रपत्र
		प्रपत्र - 13  
		विद्यालय का नाम कोड  

		उच्च माध्यमिक
		माध्यमिक

		स्टॉक में शेष पाठ्य पुस्तके
		गत सत्र में प्राप्त पाठ्य पुस्तके
		कुल पाठ्य पुस्तके
		वितरित की गई पाठ्य पुस्तके
		वर्तमान में स्टॉक में मोजूद शेष पाठ्य पुस्तके
		वर्तमान में संभावित नामांकन 
		गत सत्र में वितरण पश्चात स्टॉक गत सत्र में  
		वर्तमान सत्र में शुद्ध मांग   -->
 

 

        


        @if(@$finalResult)
            @php $counter = 0; 
           
        @endphp
            @foreach ($finalResult as $ai_code => $result)
            <table style="text-align: center;" class='page_break maintable' style="" width="100%">
                <tr>
                    <td><span>स्टॉक में शेष पाठ्य पुस्तकों का प्रपत्र</span> <span> प्रपत्र - 12</span>   </td> 
                </tr>
                <tr>
                    <td>विद्यालय का नाम {{ substr(@$aiCenters[@$ai_code], 7, 500) }} कोड  {{ @$ai_code }}</td> 
                </tr>
                
            </table>
                        @foreach ($result as $course => $items) 
                        <table style="text-align: center;"  style="" width="100%">
                            <tr>
                                <td><b>{{ @$hindi_course_lable[$course] }}</b></td> 
                            </tr>
                        </table>
                        
                        <table class="mainTable" width="100%"> 
                            <thead>
                                <tr>
                                    <th rowspan="2">SR.NO</th> 
                                    <th rowspan="2">Code No.</th> 
                                    <th rowspan="2">Name Of Book</th> 
                                    <th rowspan="2">Volume</th> 
                                    <th rowspan="2">हिन्दी नामांकन<br> छात्र संख्या</th>
                                    <th rowspan="2">अंग्रेजी नामांकन <br> छात्र संख्या</th>
                                    <th rowspan="2">हिंदी पिछले <br>वर्ष की पुस्तक स्टॉक संख्या</th> 
                                    <th rowspan="2">अंग्रेजी पिछले <br>वर्ष की पुस्तक स्टॉक संख्या </th> 
                                    <th rowspan="2">हिंदी वर्तमान <br>सत्र में शुद्ध मांग</th> 
                                    <th rowspan="2">अंग्रेजी वर्तमान<br> सत्र में शुद्ध मांग</th> 
                                </tr>
                            </thead>  
                            <tbody style="text-align: center;">
                                <tr>
                                    <td>1</td> 
                                    <td>2</td> 
                                    <td>3</td> 
                                    <td>4</td> 
                                    <td>5</td> 
                                    <td>6</td> 
                                    <td>7</td> 
                                    <td>8</td> 
                                    <td>9</td> 
                                    <td>10</td> 
                                </tr>
                                @php $counter = 0; @endphp
                                @php  
                                    $sum_hindi_auto_student_count = 0;
                                    $sum_english_auto_student_count =0;
                                    $sum_hindi_last_year_book_stock_count=0;
                                    $sum_english_last_year_book_stock_count=0;
                                    $sum_hindi_required_book_count=0;
                                    $sum_english_required_book_count=0;
                                @endphp
                                   
                                    @if(@$items)
                                        @foreach (@$items as $item) 
                                            @if(@$item->ai_ai_code)
                                                @php
                                                $counter++;
                                                $sum_hindi_auto_student_count = $sum_hindi_auto_student_count + @$item->hindi_auto_student_count;
                                                $sum_english_auto_student_count =$sum_english_auto_student_count + @$item->english_auto_student_count;
                                                $sum_hindi_last_year_book_stock_count=$sum_hindi_last_year_book_stock_count + @$item->hindi_last_year_book_stock_count;
                                                $sum_english_last_year_book_stock_count=$sum_english_last_year_book_stock_count + @$item->english_last_year_book_stock_count;
                                                $sum_hindi_required_book_count=$sum_hindi_required_book_count + @$item->hindi_required_book_count;
                                                $sum_english_required_book_count=$sum_english_required_book_count + @$item->english_required_book_count;
                                                @endphp
                                                <tr>
                                                    <td>{{ @$counter }}</td> 
                                                    <td>{{ @$item->sub_subject_code}}</td> 
                                                    <td>{{ @$item->subject_name}}</td> 
                                                    <td>{{ @$book_publication_volumes[$item->subject_volume_id]}}</td> 
                                                    <td>{{ @$item->hindi_auto_student_count}}</td> 
                                                    <td>{{ @$item->english_auto_student_count}}</td> 
                                                    <td>{{ @$item->hindi_last_year_book_stock_count}}</td> 
                                                    <td>{{ @$item->english_last_year_book_stock_count}}</td> 
                                                    <td>{{ @$item->hindi_required_book_count}}</td> 
                                                    <td>{{ @$item->english_required_book_count}}</td> 
                                                </tr> 
                                            @else
                                            @endif 
                                        @endforeach  
                                        </tbody>
                                        <tfoot style="text-align: center;">
                                            <tr>
                                                <td>&nbsp;</td> 
                                                <td>&nbsp;</td> 
                                                <td><b>Total</b></td> 
                                                <td>&nbsp;</td> 
                                                <td><b>{{ @$sum_hindi_auto_student_count }} </b></td> 
                                                <td><b>{{ @$sum_english_auto_student_count }} </b></td> 
                                                <td><b>{{ @$sum_hindi_last_year_book_stock_count}} </b></td> 
                                                <td><b>{{ @$sum_english_last_year_book_stock_count}}</b></td> 
                                                <td><b>{{ @$sum_hindi_required_book_count}}</b></td> 
                                                <td><b>{{ @$sum_english_required_book_count}}</b></td> 
                                            </tr>
                                        </tfoot>
                                    @else
                                        <tr>
                                            <td colspan="11">No Record Found</td> 
                                        </tr>
                                    </tbody>
                                    @endif
                        @endforeach 
                    
                    
                </table>
            @endforeach
        @endif 
@endsection
<style>
    table, td, th {
  border: 1px solid;
}
table {
  width: 100%;
  border-collapse: collapse;
}

    .page_break {
        page-break-before:always;
    }
</style>