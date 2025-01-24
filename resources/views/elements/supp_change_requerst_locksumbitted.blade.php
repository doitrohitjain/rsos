								@if(!$documentErrors) 
                                                @php $fld='locksumbitted';   @endphp 
                                                    @if(empty(@$masterrecord->$fld))
                                                 @php $lbl=''; $fld='locksumbitted'; @endphp 
                                                        {{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }} 
                                                        {!! Form::token() !!} 
                                                        {!! method_field('PUT') !!}  
                                                            <p class="mb-1">
                                                                <label>
																{{ Form::checkbox($fld, null) }}
                                                                    <span>@php echo Config::get('global.supp_undertaking_msg'); @endphp
                                                                    </span>
                                                                </label><br><br> 
								
                                                                @include('elements.field_error')
                                                            </p> 
                                                        <div class="col m7 s12 mb-3">
                                                            <button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Lock & Submit </button>
                                                        </div> 
                                                        {{ Form::close() }}
														@endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                    <div class="card">
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
							  