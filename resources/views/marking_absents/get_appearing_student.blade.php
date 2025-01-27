@if(isset($data) && !empty($data))
	<div class="section section-data-tables"> 
		<div class="row">
			<div class="col s12">
				<div class="card">
					<div class="card-content">
						<div class="row"> 
							<table class="responsive-table">
								<thead>
									<tr>
										<th>S/R</th>
										<th>Enrollment</th>
										<th>Absent</th>
										<th>NR</th>
									</tr>
								</thead>
								<tbody> 
								   @php $count=1; 
								   @endphp
									@if($data->count(0))
										@foreach($data as $data)
											<tr>
												<td  align="left">{{@$count}}</td>
												
												<!--<td  align="left">{{@$data->fixcode}}</td>-->
												<td  align="left">{{@$data->enrollment}}</td>
												<td>
													@php $fld='Absent[]'; @endphp
													@if(@$data->theory_absent==1)
													<label>
													    {{ Form::checkbox('Absent[]',@$data->fixcode,false,array('type'=>'checkbox','class'=>'filled-in fictitious_code nr_absent_fields nr_absent_fields_'.@$data->fixcode,'data-fixcode'=>@$data->fixcode,'id'=>'absent_field_'.@$data->fixcode,'checked')) }}
													    <span></span>
													</label>
													@else
													<label>
													    {{ Form::checkbox('Absent[]',@$data->fixcode,false,array('type'=>'checkbox','class'=>'filled-in fictitious_code nr_absent_fields nr_absent_fields_'.@$data->fixcode,'data-fixcode'=>@$data->fixcode,'id'=>'absent_field_'.@$data->fixcode)) }}
													    <span></span>
													</label>
													@endif
												</td>	
												<td >
													@php $fld='NR[]'; @endphp
													@if(@$data->theory_absent==2)
													<label>
														{{ Form::checkbox('NR[]',@$data->fixcode,false,array('type'=>'checkbox','class'=>'filled-in  NR nr_absent_fields nr_absent_fields_'.@$data->fixcode,'data-fixcode'=>@$data->fixcode,'id'=>'nr_field_'.@$data->fixcode,'checked')) }}
														<span></span>
													</label>
													@else
													<label>
														{{ Form::checkbox('NR[]',@$data->fixcode,false,array('type'=>'checkbox','class'=>'filled-in  NR nr_absent_fields nr_absent_fields_'.@$data->fixcode,'data-fixcode'=>@$data->fixcode,'id'=>'nr_field_'.@$data->fixcode)) }}
														<span></span>
													</label>
													@endif
												</td>			
											</tr>
										    @php  $count++; @endphp
										@endforeach  
									@else
										<tr>
											<td colspan="10"><h6 style="text-align:center; color:rgba(34, 188, 199, 0.918)">There are no data.</h6></td>
										</tr>
									@endif         
								</tbody>
							</table>		
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif

<script src="{!! asset('public/app-assets/js/bladejs/marking_student_absent.js') !!}"></script> 

