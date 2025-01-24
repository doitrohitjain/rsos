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
  table {
  width: 100%;
  }
  table, th, td {
  border: 1px solid grey;
  border-collapse: collapse;
  border-color: Gray Gray LightGray;
  font-family: Arial, Serif;
  padding: 10px;

  }
  .txtalign {
    vertical-align: middle !important;
    height: 22px;
  }

  .colorOdd {
    background-color: lightgray;
  }

  .txtalignCenter {
    text-align: center;
  }

  .txtalignLeft {
    text-align: left;
  }




</style>
<div class="row" style="margin-left:13px;margin-right:13px;">
  <div class="row"> 
    <div class="col-xs-12 page-header">
     
      <div class="col-xs-10">
              <h2 style=" font-family: Arial, Serif;">
          <center>CENTER ALLOTMENT REPORT </center>
        </h2>
      </div>
    </div>  
  </div>
</div>
<div class="box-body">							
  <table class="table"  >
    <thead>
      <tr class="">
        <!-- <th rowspan="2" class="txtalign txtalignCenter">#</th> -->
        <th rowspan="2" class="txtalign txtalignCenter" style="width: 12%;">Exam Center Sr.No. </th>
        <th rowspan="2" class="txtalign txtalignCenter" style="width: 50%;">Exam Center Name & Code(10th/12th)</th>
        <th rowspan="2" class="txtalign txtalignCenter" style="width: 5%;">Student Capacity</th>
        <th rowspan="2" class="txtalign txtalignCenter" style="width: 1%;" title='Exam Center of AI center wise Sr.No.'>Sr.No.</th>
        <th rowspan="2" class="txtalign txtalignCenter" >AI CODE</th>
        <th colspan="2" class="colorEven1 txtalignCenter">
        @php 
        $streams = 'Stream -'.$stream;
        @endphp
        {{@$streams}}</th>
        <th colspan="2" class="colorEven1 txtalignCenter">Supplementary</th>
        <th colspan="3" class="colorEven1 txtalignCenter">Total(ST2/SUPP.)</th>
      </tr>
      <tr class="txtalignCenter">
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
	@foreach($master as $value)
        @php
        $outerColor = null; 
          if($i % 2 == 0 ){
            $outerColor = "Lightgray";
          }
            $rowspanCounter = count($value->examcenterallotments);
          
        @endphp  
    <tr>

            <!-- <td  class="txtalignLeft" style="background-color:<?php echo $outerColor;?>;text-transform:uppercase;vertical-align:top;" rowspan="<?php echo $rowspanCounter;?>">&nbsp;{{@$i}}</td> -->

            <td  class="txtalignCenter" style="background-color:<?php echo $outerColor;?>;text-transform:uppercase;vertical-align:top;" rowspan="<?php echo $rowspanCounter;?>">&nbsp;{{@$i}}</td>

            <td class="txtalignLeft" style="background-color:<?php echo $outerColor;?>;text-transform:uppercase;vertical-align:top; font-size:12px;" rowspan="<?php echo $rowspanCounter;?>">{{@$value->cent_name}}( {{@$value->ecenter10}} / {{@$value->ecenter12}} )  </br>
              @if(!empty($value->ai_code))
               <b>{{$value->ai_code}}</b>
             @else
               <b> No AiCode;</b>
             
            @endif

            <td  class="txtalignCenter" style="background-color:<?php echo $outerColor;?>;text-transform:uppercase;vertical-align:top;" rowspan="<?php echo $rowspanCounter;?>">&nbsp;{{@$value->capacity}}</td>
		@php $j= 1; 
			$total_student_strem2_10= 0;
			$total_student_strem2_12= 0;
			$total_student_supp_10 = 0;
			$total_student_supp_12 = 0;
			$total_total_10 = 0;
			$total_total_12 = 0;
			$total_bothtotal = 0;
		@endphp

   @foreach ($value['examcenterallotments'] as $examcenterallotments1)

            @php  $color=null;  if($j%2==0){
              $color = "Lightgray";
            } @endphp
    @if($stream == 2)
	
			@php  

        if($examcenterallotments1->is_student_strem2_10 == 1){
    		$student_strem2_10 = $examcenterallotments1->student_strem2_10;
        }elseif($examcenterallotments1->is_student_strem2_10 == 0){
        	$student_strem2_10 = 0;
        } 
		
		
        if($examcenterallotments1->is_student_strem2_12 == 1){
        $student_strem2_12=$examcenterallotments1->student_strem2_12;  
        }elseif($examcenterallotments1->is_student_strem2_12 == 0){
        $student_strem2_12=0;  
        }

        if($examcenterallotments1->is_student_supp_10 == 1){
        $student_supp_10=$examcenterallotments1->student_supp_10;  
        }elseif($examcenterallotments1->is_student_supp_10 ==0){
        $student_supp_10=0;  
        }

         if($examcenterallotments1->is_student_supp_12 == 1){
        $student_supp_12=$examcenterallotments1->student_supp_12;  
        }elseif($examcenterallotments1->is_student_supp_12 ==0){
        $student_supp_12=0;  
        }
        $total_10= $student_strem2_10+$student_supp_10;
    		$total_12=$student_strem2_12+$student_supp_12;
        $bothtotal =$total_10+$total_12; 


		$total_student_strem2_10 = $total_student_strem2_10 + $student_strem2_10;
		$total_student_strem2_12 = $total_student_strem2_12 + $student_strem2_12;
		$total_student_supp_10 = $total_student_supp_10 + $student_supp_10;
		$total_student_supp_12 = $total_student_supp_12 + $student_supp_12;
		$total_total_10 = $total_total_10 + $total_10;
		$total_total_12 = $total_total_12 + $total_12;
		$total_bothtotal = $total_bothtotal + $bothtotal;

        @endphp 
        @elseif($stream == 1)       
          @php  
      if($examcenterallotments1->is_student_strem1_10 == 1){
        $student_strem1_10 = $examcenterallotments1->student_strem1_10;
        }elseif($examcenterallotments1->is_student_strem2_10 == 0){
        $student_strem1_10 = 0;
        }

        if($examcenterallotments1->is_student_strem1_12 == 1){
        $student_strem1_12=$examcenterallotments1->student_strem1_12;  
        }elseif($examcenterallotments1->is_student_strem2_12 == 0){
        $student_strem1_12=0;  
        }

        if($examcenterallotments1->is_student_supp_10 == 1){
        $student_supp_10=$examcenterallotments1->student_supp_10;  
        }elseif($examcenterallotments1->is_student_supp_10 ==0){
        $student_supp_10=0;  
        }

        if($examcenterallotments1->is_student_supp_12 == 1){
        $student_supp_12=$examcenterallotments1->student_supp_12;  
        }elseif($examcenterallotments1->is_student_supp_12 ==0){
        $student_supp_12=0;  
        }
        @$total_10= $student_strem1_10+$student_supp_10;
        @$total_12=$student_strem1_12+$student_supp_12;
        @$bothtotal =$total_10+$total_12; 
        @endphp 

    

     @endif

       @if($stream == 2)

				
				<td class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{ @$j; }}</td>
                <td class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$examcenterallotments1->ai_code}}</td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_strem2_10}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_strem2_12}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_supp_10}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_supp_12}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$total_10}}</td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$total_12}}</td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$bothtotal}}
				</td> 

     @elseif($stream == 1) 
             <td class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{ @$j; }}</td>
                <td class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$examcenterallotments1->ai_code}}</td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_strem1_10}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_strem1_12}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_supp_10}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$student_supp_12}} </td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$total_10}}</td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$total_12}}</td>
                <td  class="txtalignCenter" style="background-color:<?php echo $color;?>;">&nbsp;{{@$bothtotal}} </td>
                
       @endif


	   
	</tr>
	
				
			@php   $j++;  @endphp
		@endforeach


		<tr> 

			@if($stream == 2)
				<th></th>
				<th></th>
				<th>{{@$value->capacity}}</th>
				<th></th>
				<th></th>
				<th>{{ @$total_student_strem2_10 }}</th>
				<th>{{ @$total_student_strem2_12 }}</th>
				<th>{{ @$total_student_supp_10 }}</th>
				<th>{{ @$total_student_supp_12 }}</th>
				<th>{{ @$total_total_10 }}</th>
				<th>{{ @$total_total_12 }}</th>
				<th>{{ @$total_bothtotal }}</th>
			@endif
		</tr>

    <tr> 

      @if($stream == 1)
        <th></th>
        <th></th>
        <th>{{@$value->capacity}}</th>
        <th></th>
        <th></th>
        <th>{{ @$total_student_strem1_10 }}</th>
        <th>{{ @$total_student_strem1_12 }}</th>
        <th>{{ @$total_student_supp_10 }}</th>
        <th>{{ @$total_student_supp_12 }}</th>
        <th>{{ @$total_total_10 }}</th>
        <th>{{ @$total_total_12 }}</th>
        <th>{{ @$total_bothtotal }}</th>
      @endif
    </tr>

	  @php $i++ @endphp
   
	   @endforeach
	  </table>
	  </div>
<style>
 
</style>