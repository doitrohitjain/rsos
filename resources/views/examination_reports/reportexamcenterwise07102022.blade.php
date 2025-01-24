@extends('layouts.default')
@section('content')

<div id="main">
	<div class="row">
	  <div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
						</ol>
					</div>
				</div>
			</div>
        </div>
		<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									@include('elements.filters.search_filter')
								</div>
							</div>
						</div>
					</div>
				</div>
	<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row"> 
									
  <table >
    <thead>
      <tr class="">
        <th rowspan="2" class="txtalign">#</th>
        <th rowspan="2" class="txtalign">ExamCenter <br>Sr.No. </th>
        <th rowspan="2" class="txtalign">Exam Center Name & Code(10th/12th)</th>
        <th rowspan="2" class="txtalign">Student Capacity</th>
        <th rowspan="2" class="txtalign" title='Exam Center of AI center wise Sr.No.'>Sr.No.</th>
        <th rowspan="2" class="txtalign">AI CODE</th>
        <th colspan="2" class="colorEven1">Stream- {{ $stream }}</th>
        <th colspan="2" class="colorEven1">Supplementary</th>
        <th colspan="3" class="colorEven1">Total(SUP/Stream)</th>
      </tr>
      <tr>
        <th> 10th </th>
        <th> 12th </th>
        <th> 10th </th>
        <th> 12th </th>
        <th> 10th </th>
        <th> 12th </th>
        <th> Total </th>
      </tr>
    </thead>
    <tbody>
	<tr>
     
	@php $i=1;@endphp
	@foreach ($master as $value)
	
		@php 
			$rowSpanCount = count($value->examcenterallotments);
		@endphp
		<tr>
        <td>{{@$i}}</td>
        <td >{{@$i}}</td>
		
        <!-- <td rowspan="{{ $rowSpanCount; }}" style="vertical-align:top !important;"> -->
		<td>
			<div>{{@$value->cent_name}}</div>
			<div style='font-weight:bold;'>
			( {{ ($value->ecenter10) }}
			/
			{{ ($value->ecenter12) }} )
			</div>
        </td>
        <td > {{@$value->capacity}} </td>
		@php $j= 1; @endphp 
		@php 
			$stream_10 = 'student_strem'.$stream.'_10';
			$stream_12 = 'student_strem'.$stream.'_12';
			$x=$value->$stream_10;
			$y=$value->student_supp_10;  
			$z=$x+$y;
			$x1=$value->$stream_12;  
			$y2=$value->student_supp_12;  
			$z2=$x1+$y2;
			$z3=$z2+$z;
			
			if($j==1){ @endphp 
				<td>{{ @$j; }}</td>
				<td>{{ @$value->ai_code }}</td>
				<td>{{ @$value->$stream_10 }} </td>
				<td>{{ @$value->$stream_12 }} </td>
				<td>{{ @$value->student_supp_10 }} </td>
				<td>{{ @$value->student_supp_12 }} </td>
				<td>{{ @$z }}</td>
				<td>{{ @$z2 }}</td>
				<td>{{ @$z3 }} </td>
				</tr>
			@php } else {  @endphp 
			
					<tr>
					<td ></td>
					<td></td>
					<td>
						<div></div>
						<div style='font-weight:bold;'></div>
					</td>
					<td></td>
					<td>{{ $j; }}</td> 
					<td>{{@$value->ai_code}}</td>
					<td>{{ @$value->$stream_10 }} </td>
					<td>{{ @$value->$stream_12 }} </td>
					<td>{{@$value->student_supp_10}} </td>
					<td>{{@$value->student_supp_12}} </td>
					<td>{{@$z}}</td>
					<td>{{@$z2}}</td>
					<td>{{@$z3}} </td>
				</tr>
				
			@php }
			 $j++;  @endphp

			
		{{--@foreach ($value->examcenterallotments as $examcenterallotments1)
			@php 
			$stream_10 = 'student_strem'.$stream.'_10';
			$stream_12 = 'student_strem'.$stream.'_12';
			$x=$examcenterallotments1->$stream_10;
			$y=$examcenterallotments1->student_supp_10;  
			$z=$x+$y;
			$x1=$examcenterallotments1->$stream_12;  
			$y2=$examcenterallotments1->student_supp_12;  
			$z2=$x1+$y2;
			$z3=$z2+$z;
			
			if($j==1){ @endphp 
				<td>{{ @$j; }}</td>
				<td>{{ @$examcenterallotments1->ai_code }}</td>
				<td>{{ @$examcenterallotments1->$stream_10 }} </td>
				<td>{{ @$examcenterallotments1->$stream_12 }} </td>
				<td>{{ @$examcenterallotments1->student_supp_10 }} </td>
				<td>{{ @$examcenterallotments1->student_supp_12 }} </td>
				<td>{{ @$z }}</td>
				<td>{{ @$z2 }}</td>
				<td>{{ @$z3 }} </td>
				</tr>
			@php } else {  @endphp 
			
				  <tr>
					<td ></td>
					<td></td>
					<td>
					  <div></div>
					  <div style='font-weight:bold;'></div>
					</td>
					<td></td>
					<td>{{ $j; }}</td>
					
					<td>{{@$examcenterallotments1->ai_code}}</td>
					<td>{{ @$examcenterallotments1->$stream_10 }} </td>
					<td>{{ @$examcenterallotments1->$stream_12 }} </td>
					<td>{{@$examcenterallotments1->student_supp_10}} </td>
					<td>{{@$examcenterallotments1->student_supp_12}} </td>
					<td>{{@$z}}</td>
					<td>{{@$z2}}</td>
					<td>{{@$z3}} </td>
				</tr>
				
			@php } $j++;  @endphp
		@endforeach 
		--}}
	  @php $i++;
	  @endphp
	   @endforeach
	  </table>
	  {{ $master->links('elements.paginater') }}
</div>
</div>
</div>
</div>
</div>
</div></div>
</div>
@endsection
