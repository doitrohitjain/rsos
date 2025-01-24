<style>
  .blink_me {
    animation: blinker 1s linear infinite;
    color: red;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
  }

  .navbar-brand {
    position: absolute;
    width: 90%;
    left: 2%;
    top: 0;
    text-align: center;
    margin: auto;
  }

  @media (max-width: 767px) {
    .navbar-brand {
      top: -20% !important;
      left: 5%;
      width: 90%;
      font-size: medium;
    }
  }

  .skin-blue-light .treeview-menu>li.active>a {
    background-color: #30bbbb !important;
    background: #30bbbb !important;
    color: #fff !important;
  }

  .skin-blue-light .sidebar-menu>li.active>.treeview-menu {
    background-color: #00c0ef !important;
    background: #00c0ef !important;
    color: #fff !important;
  }

  .skin-blue-light.sidebar-mini.sidebar-collapse .sidebar-menu>li.active>.treeview-menu {
    border-left: 1px solid #d2d6de;
    background-color: #00c0ef !important;
    background: #00c0ef !important;
    color: #fff !important;
  }

  ul.sidebar-menu li.active a {
    background-color: #00c0ef !important;
    background: #00c0ef !important;
    color: #fff !important;
  }

  .active {
    color: #000;
  }

  .list-group-item.active,
  .list-group-item.active:focus,
  .list-group-item.active:hover {
    z-index: 2;
    color: #FFFFFF !important;
    background-color: #337ab7;
    border-color: #337ab7;
  }

  .scroll {
    max-height: 500px;
    overflow-y: auto;
  }
</style>
<div class="box-body table-responsive">
  <table class="table" border="2px">
    <thead>
      <tr class="">
        <th rowspan="2" class="txtalign">#</th>
        <th rowspan="2" class="txtalign">ExamCenter <br>Sr.No. </th>
        <th rowspan="2" class="txtalign">Exam Center Name & Code(10th/12th)</th>
        <th rowspan="2" class="txtalign">Student Capacity</th>
        <th rowspan="2" class="txtalign" title='Exam Center of AI center wise Sr.No.'>Sr.No.</th>
        <th rowspan="2" class="txtalign">AI CODE</th>
        <th colspan="2" class="colorEven1">Stream-1</th>
        <th colspan="2" class="colorEven1">Supplementary</th>
        <th colspan="3" class="colorEven1">Total(SUP/ST1)</th>
      </tr>
      <tr class="txtalignLeft">
        <th class="colorOdd"> 10th </th>
        <th class="colorOdd"> 12th </th>
        <th class="colorOdd"> 10th </th>
        <th class="colorOdd"> 12th </th>
        <th class="colorOdd"> 10th </th>
        <th class="colorOdd"> 12th </th>
        <th class="colorOdd"> Total </th>
      </tr>
    </thead>
    <tbody>
	@php $i=1;@endphp
	@foreach ($master as $value)
		<tr class="txtalignLeft colorOdd">
        <td class="txtalignLeft">{{@$i}}</td>
        <td class="txtalignLeft" style="vertical-align:top;">{{@$value->id}}</td>
        <td class="txtalignLeft" style="vertical-align:top;">
          <div>{{@$value->cent_name}}</div>
          <div style='font-weight:bold;'>{{ date('Y') }}</div>
        </td>
        <td class="txtalignLeft" style="vertical-align:top;"> {{@$value->capacity}} </td>
		@php $j= 1; @endphp
		@foreach ($value->examcenterallotments as $examcenterallotments1)
			@php 
			
			$x=$examcenterallotments1->student_strem1_10;  
			$y=$examcenterallotments1->student_supp_10;  
			$z=$x+$y;
			$x1=$examcenterallotments1->student_strem1_12;  
			$y2=$examcenterallotments1->student_supp_12;  
			$z2=$x1+$y2;
			$z3=$z2+$z;
			
			if($j==1){ @endphp 
				<td>{{ @$j; }}</td>
				<td>{{@$examcenterallotments1->ai_code}}</td>
				<td>{{@$examcenterallotments1->student_strem1_10}} </td>
				<td>{{@$examcenterallotments1->student_strem1_12}} </td>
				<td>{{@$examcenterallotments1->student_supp_10}} </td>
				<td>{{@$examcenterallotments1->student_supp_12}} </td>
				<td>{{@$z}}</td>
				<td>{{@$z2}}</td>
				<td>{{@$z3}} </td>
				</tr>
			@php } else {  @endphp 
			
				<tr class="txtalignLeft colorOdd">
					<td class="txtalignLeft"></td>
					<td class="txtalignLeft" style="vertical-align:top;"></td>
					<td class="txtalignLeft" style="vertical-align:top;">
					  <div></div>
					  <div style='font-weight:bold;'></div>
					</td>
					<td class="txtalignLeft" style="vertical-align:top;"></td>
					<td>{{ $j; }}</td>
					
					<td>{{@$examcenterallotments1->ai_code}}</td>
					<td>{{@$examcenterallotments1->student_strem1_10}} </td>
					<td>{{@$examcenterallotments1->student_strem1_12}} </td>
					<td>{{@$examcenterallotments1->student_supp_10}} </td>
					<td>{{@$examcenterallotments1->student_supp_12}} </td>
					<td>{{@$z}}</td>
					<td>{{@$z2}}</td>
					<td>{{@$z3}} </td>
				</tr>
				
			@php } $j++;  @endphp
		@endforeach
	  @php $i++;
	  @endphp
	   @endforeach
	  
  
  </table>
</div>
<style>
  .actionSecton {
    max-width: 100px;
    margin-right: 5%;
    float: left;
  }

  .input-sm {
    height: 22px;
    padding: 5px 10px;
    font-size: 12px;
    line-height: 1.5;
    max-width: 100px;
    border-radius: 3px;
  }

  .txtalign {
    vertical-align: middle !important;
    height: 22px;
  }

  .colorEven {
    background-color: #f3f5fb;
  }

  .colorOdd {
    background-color: #87cefa99;
  }

  .txtalignCenter {
    text-align: center;
  }

  .txtalignLeft {
    text-align: left;
  }
</style>