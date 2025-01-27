@php use App\Helper\CustomHelper;  @endphp
<div > 
    <table class="responsive-table"> 
        <thead>
            <tr>
				@php
					$isCustomFieldWidth = true;
					if($isCustomFieldWidth){
						$width = "20%";
					}
				@endphp
				
                @foreach($tableData as $key => $tableTh)  
                    <th class="" style="white-space: nowrap;width: 1%;">{{ $tableTh['lbl'] }}</th> 
                @endforeach 
            </tr>
        </thead>
        @if(count($master) > 0)
            @php $counter = 0; @endphp
            @foreach ($master as $key => $value)
				<tbody>
                @php $colorCss = ""; @endphp
                @if(@$value->deleted_at)
                    @php $colorCss = "red"; @endphp
                @endif
                <tr style="color: {{  $colorCss }}">
                    @foreach($tableData as $key => $tableTh)
                        @php 
                            $fld=$tableTh['fld'];
                        @endphp
                        @if($fld != 'action')
                            <td class="word-break2"> 
                                @php 
                                    if(@$tableTh['report_type'] == 'sessional' && @$tableTh['vertical_type'] == true) {
                                        $exam_subjects = explode(",",$value['subjects']);
                                        $exam_subjects_marks = explode(",",$value['subject_marks']);
                                        $subjectDetails = "-";
                                        if(@$tableTh['options'][@$exam_subjects[@$tableTh['subject_key']]]){
                                            $subjectDetails = @$tableTh['options'][@$exam_subjects[@$tableTh['subject_key']]]. "(" . @$exam_subjects_marks[@$tableTh['subject_key']] . ")";
                                        }
                                        echo $subjectDetails;
                                    }else if($fld == 'srno'){
                                    echo $master->firstItem() + $counter;
                                    }else{
                                        $textLabel = null;
                                        if(@$tableTh['fld_url']){
                                            //$tableTh['fld_url'] = strrchr( $tableTh['fld_url'], '?');
                                            $tempUrlFinal = $tempUrl  = explode("#", $tableTh['fld_url']);
                                            foreach($tempUrl as $k =>  $v){
                                                if($k >0 && $k%2 != 0){
                                                    $tempUrlFinal[$k] =  $value[$v];
                                                }
                                            }
                                            $tempUrlFinal = implode("",$tempUrlFinal); 
                                            $textLabel .=  "<a href='" . $tempUrlFinal . "' class='btn  btn-success'>";
                                        }
                                        
                                        
                                        if(@$tableTh['input_type'] == 'select'){
                                            if(@$tableTh['options'][$value[$fld]]){
                                                $textLabel .=  $tableTh['options'][$value[$fld]];
                                            }
                                        }else{ 
                                            if(@$value[$fld]){
                                                $textLabel .=  $value[$fld];
                                            }else if($value[$fld] == 0){
                                                $textLabel .=  $value[$fld];
                                            } 
                                        }
                                        if(@$tableTh['fld_url']){
                                            $textLabel .= "</a>";
                                        }
                                        echo $textLabel;
                                    } 
                                @endphp  
                            </td> 
                        @else
                            <td>
                                @php
                                    if(@$value->deleted_at){
                                        @endphp
                                        <a href="{{ route('mark_active',Crypt::encrypt($value->id)) }}" class="invoice-action-delete delete-confirm mark-confirm">
                                            <span class="">
                                                <i class="material-icons tooltipped" data-position="top" data-tooltip="Click here to mark active.">
                                                visibility_off</i> 
                                            </span>
                                        </a>
                                        @php
                                    }else{
                                        @endphp
                                        <div class="invoice-action"> 
                                            <a href="{{ route('examcenter_update',Crypt::encrypt($value->id)) }}" class="invoice-action-edit">
                                                <i class="material-icons" title="Edit Exam Center.">edit</i>
                                            </a>
                                            <a href="{{ route('preview_centerallotment_stream'.$stream,Crypt::encrypt($value->id)) }}" class="invoice-action-view">
                                                <i class="material-icons"title='View Allotment Data'>remove_red_eye</i>
                                            </a> 
											<a href="{{ route('examcenter_aicenter_unmaapeduserid',Crypt::encrypt($value->id)) }}" class="invoice-action-view delete-confirm">
                                                <i class="material-icons" title='Remove SSO'>block</i>
											</a> 
                                            @can('allotment_button')
                                            <a href="{{ route('examcenter_aicenter_mapping_stream',Crypt::encrypt($value->id)) }}" class=""><span class="btn cyan waves-effect waves-light border-round  gradient-45deg-deep-orange-orange">AllotCenter</span></a>
                                            @endcan
											
                                        </div>
                                        @php
                                    }
                            
                                    
                                @endphp
                            </td>
                        @endif
                        
                    @endforeach

                    
                </tr>
            
                </tbody>  
                @php $counter++; @endphp 
            @endforeach
		@else 
			<tbody><tr><td colspan="20" class="center text-red">Data Not Found</td></tr></tbody>
		@endif
	</table>
    {{ $master->withQueryString()->links('elements.paginater') }}
</div>

<script>

$('.mark-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'You want to active the Exam Center',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});

</script>
