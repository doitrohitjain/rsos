                          @if(!$documentErrors) 
									<div class="card">
                                                @php $fld='locksumbitted'; @endphp 
                                                    @if(empty(@$masterrecord->$fld))
                                            <div class="card-content invoice-print-area">
                                                <div class="row">
                                                    <div class="col m12 s12">
                                                    @php $lbl=' घोषणा (Declarati)'; $fld='locksumbitted'; @endphp 
													@php $lbl1='Declaration'; $fld1='Declaration'; @endphp 
                                                        {{ Form::open(['route' => [request()->route()->getAction()['as'],$estudent_id], 'id' =>$model]) }} 
                                                        {!! Form::token() !!} 
                                                        {!! method_field('PUT') !!} 
															
                                                            <p class="mb-1">
                                                                <label>
                                                                    <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                                                    <br>
                                                                    {{ Form::checkbox($fld, null) }}
                                                                    <span>{{@$student_declaration[1]}}
																	@php echo $lbl1.Config::get('global.fresh_form_second_undertaking_msg');@endphp	
                                                                    </span>
																	
                                                                </label><br><br> 
                                                                @include('elements.field_error')
                                                            </p>
															
															
															<p class="mb-1">
                                                                <label>
                                                                    <h8>@php echo $lbl1.Config::get('global.starMark'); @endphp </h8>
                                                                    <br>
                                                                    {{ Form::checkbox($fld1, null) }}
                                                                    <span>
																	@php echo $lbl1.Config::get('global.fresh_form_undertaking_msg'); @endphp</span>
																</label>
																<br><br> 
                                                                @include('elements.field_error')
                                                            </p>
															
														<div class="col m7 s12 mb-3">
                                                            <button class="btn cyan waves-effect waves-light right btn_disabled" type="submit" name="action"> Lock & Submit </button>
                                                        </div> 
														
	
														
                                                        {{ Form::close() }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                    <div class="card card-content">
                                        <div class="card-content invoice-print-area" style="color:red;">
                                               <span style="color:green;font-size:20px;font-weight: bold;"> Pending Docuemtns </span>
                                                @foreach(@$documentErrors as $v)
                                                    <div class="row">
                                                        <div class="col m12 s12">
                                                            <table>
                                                                <tr> 
                                                                    @php $link = 'document_details'; @endphp
                                                                    <a data-target="" class="" href="{{ route($link,Crypt::encrypt($student_id)) }}">
                                                                        <span class="dropdown-title3" data-i18n="Persoanl">
                                                                            @php echo $v; @endphp
                                                                        </span>  
                                                                    </a>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
								        </div>
								    </div>