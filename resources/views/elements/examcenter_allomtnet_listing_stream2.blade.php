@php use App\Helper\CustomHelper;  @endphp
<div class="col s12"> 
    <table class="table"> 
		<thead> 
			<tr class="">
				<th rowspan="2" class="txtalign"><center>#</center></th> 
				<th rowspan="2" class="txtalign"><center>Exam Center Name &amp; Code(10th/12th)</center></th>
				<th rowspan="2" class="txtalign"><center>Student Capacity</center></th> 
				<th rowspan="2" class="txtalign"><center>AI CODE</center></th>
				<th colspan="4" class="colorEven13"><center>Stream-2 </center></th>						
				<th colspan="4" class="colorEven13"><center>Supplementary</center></th>						
				<th colspan="3" class="colorEven13"><center>Total(SUP/ST2)</center></th>	
				<th rowspan="2">
					Actions
				</th>
			</tr> 
			<tr class="txtalignLeft">
				<th class="colorOdd2">
					10th
				</th>
				<th class="colorOdd2">
					10th
				</th>
				<th class="colorOdd2">
					12th
				</th>
				<th class="colorOdd2">
					12th
				</th>
				<th class="">
					10th
				</th>
				<th class="colorOdd2">
					10th
				</th>
				<th class="colorOdd2">
					12th
				</th>
				<th class="colorOdd2">
					12th
				</th>
				<th class="colorOdd2">
					10th
				</th>
				<th class="colorOdd2">
					12th
				</th>
				<th class="colorOdd2">
					Total
				</th> 
			</tr> 
        </thead>
         
        @if(count($master) > 0)
            @php $counter = 1; @endphp
			@foreach ($master as $key => $value)
				<tbody>
                    <tr>  
						<td>@php 
						$fld="total_of_10"; $totalSec = @$value->$fld; 
						$fld="ai_code"; $$fld = @$value->$fld;
						$fld="stream"; $$fld = @$value->$fld;
						$fld="examcenter_detail_id"; $$fld = @$value->$fld;
						$fld="id"; $$fld = @$value->$fld;
						
						$fld="counter"; echo @$counter; @endphp
						</td> 
						<td>
							@php 
								$fld="cent_name"; echo @$value->$fld;
								$fld="ecenter10"; echo "(" . @$value->$fld;
								$fld="ecenter12"; echo "/" .  @$value->$fld . ")";
								// $fld="ai_code"; echo "<b>" . @$value->$fld . "</b>";
							@endphp
						</td> 
						<td>@php $fld="capacity"; echo @$value->$fld; @endphp</td> 
						<td>@php $fld="ai_code"; echo @$value->$fld; @endphp</td> 
						
						<td>@php $fld="student_strem" . $stream ."_10"; $totalSec = @$value->$fld; echo @$value->$fld; @endphp</td> 
						<td> 
							@if($totalSec > 0)
								@php 
									$course = 10;
									$supplementary = 0;
									$statusval = CustomHelper::getstudentenrollmentstatus($ai_code,$stream,$course,$supplementary,$examcenter_detail_id,$id); 
								@endphp
								@if($statusval == 1)	
									<a href='javascript:void(0);' 
										onclick=getEnrollments({{ $ai_code }},{{ $course }},{{ $stream }},{{ $supplementary }},{{ $examcenter_detail_id }},{{ $id }},0) 
										class='btn-floating center waves-effect waves-light btn gradient-45deg-red-pink'>
										View
									</a>	
								@else
									<a href="{{route('studentenrollmentallotment',['ai_code'=>$ai_code,'course'=>$course,'stream'=>$stream,'examcenterdetailid'=>$examcenter_detail_id,'centerallotmentid'=>$id,'supplementary'=>0])}}" class="btn-floating center waves-effect waves-light btn gradient-45deg-indigo-light-blue">Allot</a>
								@endif
							@endif 
						</td> 
						<td>@php $fld="student_strem" . $stream ."_12"; $totalSec = @$value->$fld;  echo @$value->$fld; @endphp</td> 
						<td>
							@if($totalSec > 0)
								@php 
									$course = 12;
									$supplementary = 0;
									$statusval = CustomHelper::getstudentenrollmentstatus($ai_code,$stream,$course,$supplementary,$examcenter_detail_id,$id); 
								@endphp
								@if($statusval == 1)	
									<a href='javascript:void(0);' 
										onclick=getEnrollments({{ $ai_code }},{{ $course }},{{ $stream }},{{ $supplementary }},{{ $examcenter_detail_id }},{{ $id }},0) 
										class='btn-floating center waves-effect waves-light btn gradient-45deg-red-pink'>
										View
									</a>	
								@else
									<a href="{{route('studentenrollmentallotment',['ai_code'=>$ai_code,'course'=>$course,'stream'=>$stream,'examcenterdetailid'=>$examcenter_detail_id,'centerallotmentid'=>$id,'supplementary'=>0])}}" class="btn-floating center waves-effect waves-light btn gradient-45deg-indigo-light-blue">Allot</a>
								@endif
							@endif   
						</td> 
						<td>@php $fld="student_supp_10"; $totalSec = @$value->$fld; echo @$value->$fld;  @endphp</td> 
						<td>
							@if($totalSec > 0)
								@php 
									$course = 10;
									$supplementary = 1;
									$statusval = CustomHelper::getstudentenrollmentstatus($ai_code,$stream,$course,$supplementary,$examcenter_detail_id,$id); 
								@endphp
								@if($statusval == 1)	
									<a href='javascript:void(0);' 
										onclick=getEnrollments({{ $ai_code }},{{ $course }},{{ $stream }},{{ $supplementary }},{{ $examcenter_detail_id }},{{ $id }},1) 
											class='btn-floating center waves-effect waves-light btn gradient-45deg-red-pink'>
											View
									</a>
								@else
									<a href="{{route('suppstudentenrollmentallotment',['ai_code'=>$ai_code,'course'=>$course,'stream'=>$stream,'examcenterdetailid'=>$examcenter_detail_id,'centerallotmentid'=>$id,'supplementary'=>1])}}" class="btn-floating center waves-effect waves-light btn gradient-45deg-indigo-light-blue">Allot</a>
								@endif
							@endif 
						</td> 
						<td>@php $fld="student_supp_12"; $totalSec = @$value->$fld; echo @$value->$fld; @endphp</td> 
						<td>
							@if($totalSec > 0)
								@php 
									$course = 12;
									$supplementary = 1;
									$statusval = CustomHelper::getstudentenrollmentstatus($ai_code,$stream,$course,$supplementary,$examcenter_detail_id,$id); 
								@endphp
								@if($statusval == 1)	
									<a href='javascript:void(0);' 
									onclick=getEnrollments({{ $ai_code }},{{ $course }},{{ $stream }},{{ $supplementary }},{{ $examcenter_detail_id }},{{ $id }},1) 
										class='btn-floating center waves-effect waves-light btn gradient-45deg-red-pink'>
										View
									</a>	 
								@else
									<a href="{{route('suppstudentenrollmentallotment',['ai_code'=>$ai_code,'course'=>$course,'stream'=>$stream,'examcenterdetailid'=>$examcenter_detail_id,'centerallotmentid'=>$id,'supplementary'=>1])}}" class="btn-floating center waves-effect waves-light btn gradient-45deg-indigo-light-blue">Allot</a>
								@endif
							@endif 
						</td> 
						<td>@php $fld="total_of_10"; echo @$value->$fld; @endphp</td> 
						<td>@php $fld="total_of_12"; echo @$value->$fld; @endphp</td> 
						<td>@php $fld="total"; echo @$value->$fld; @endphp</td>   
						<td>
                            @can('delete_allotment')
								@php 
									$btn="<a href='javascript:void(0);' data-examcenter_detail_id='$examcenter_detail_id' data-centerallotment_id='$id' class='btn-floating center waves-effect waves-light btn red deleteexamcenter'><i class='material-icons'>delete</i></a>";
									echo $btn; 
								@endphp
                            @endcan
						</td>   
					</tr>
                </tbody>  
                @php $counter++; @endphp 
            @endforeach
		@else 
			<tbody><tr><td colspan="20" class="center text-red">Data Not Found</td></tr></tbody>
		@endif
	</table>
    {{ $master->links('elements.paginater') }}
</div>
<script>
$('body').on('click', '.deleteexamcenter', function (){
	examcenter_detail_id = $(this).data("examcenter_detail_id");
	centerallotment_id = $(this).data("centerallotment_id");
	
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                       },
                    type: "post",
					data:{centerallotment_id:centerallotment_id,examcenter_detail_id:examcenter_detail_id},
					url: "{{ route('ajaxdeleteexamcenterallotment') }}",
                    success: function (data) {
						if(data.status == false){
							toastr.error(data.msg);
						}else{
							toastr.success(data.msg);
						} 
						location.reload();
                      },
                    error: function (data) {
						toastr.error(data.success);
                        console.log('Error:', data);
                    }

                });
             
        }

    });
});
</script>
<style>
	td{
		text-align:center;
	}
</style>