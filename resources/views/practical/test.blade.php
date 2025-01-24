@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        
	
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										
										
									
									
								</div>
							</div>
							
							<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
							@php
$model="Student";
@endphp
                                <div id="html-view-validations">
                                    {!! Form::open(array('url' => url()->current(),'method'=>'POST','id' => $model)) !!}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									
                                    <div class="row">
                                        <div class="input-field col s6">
                                            @php $lbl='स्लॉट प्रारंभ समय (Slot Start Time)'; $fld='date_time_start'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            <input type="datetime-local"  name="{{ $fld }}" value="" class="form-control" autocomplete="off" id="{{ $fld }}" placeholder="Enter " min="2021-06-07T14:47:57">
                                            <input type="hidden" name ="user_examiner_map_id" value="{{encrypt(@$user_examiner_map_id)}}"  >
											
                                            @include('elements.field_error')
                                        </div>
												</tbody>
											</table>                                       
										
                                        <div class="row">
                                            <div class="col m10 s12 mb-3">
                                                <!--<button class="btn cyan waves-effect waves-light right" type="reset">
                                                <i class="material-icons right">clear</i>Reset
                                                </button>-->
												<a href="#" class="btn cyan waves-effect waves-light right">Reset</a>
                                            </div>
                                            <div class="col m2 s12 mb-3">
                                                <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
                                        </div>
									</div>
                                    {!! Form::close() !!}  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
						</div>
					</div>
				</div>
			</div>
		</div>
		

		<div class="content-overlay"></div>
		
    </div>
</div> 
@endsection
@section('customjs')
	<script>
$(function() {
        	var today = new Date().toISOString().slice(0, 16);
            document.getElementsByName("date_time_start")[0].min = today;
			document.getElementsByName("date_time_end")[0].min = today;
			  maxTime: {
           hour: 7, minute: 30
       }
        });

</script> 
@endsection 


