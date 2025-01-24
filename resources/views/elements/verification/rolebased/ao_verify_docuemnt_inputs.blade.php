@php
    use App\Helper\CustomHelper;
    $currentAction = Route::getCurrentRoute()->getAction();
@endphp
@if(@$masterInputArr)
    <fieldset style="display:block;">
        <legend>छात्र दस्तावेजों का सत्यापन(Verification of student documents)</legend>
        @if(@$isCurrentacademicofficer_id)
            <span style="" width="20%">
				<span style="color:blue">Verifier Status:  
				@if($lastEnteredBy->verifier_status == 8)
                        <span class="chip lighten-5 red red-text">
						Objections
					</span>
                    @elseif($lastEnteredBy->verifier_status == 7)
                        <span class="chip lighten-5 green green-text">
						Accepted
					</span>
                    @endif Please refer to the points marked by the verifier for details.
					(विवरण के लिए कृपया सत्यापनकर्ता द्वारा चिह्नित बिंदुओं को देखें। )
				</span>
				<td style="">
					<p>
						Note : To toggle between selecting or deselecting all the checkboxes below, please use this checkbox.(सभी नीचे दिए गए चेकबॉक्स को चुनने या हटाने के लिए, कृपया इस चेकबॉक्स को टॉगल करें।)
						<label>
							<input type="checkbox" class="chkboxactionmain" id="chkboxactionmain"
                                   name="chkboxactionmain" class="filled-in"
                                   style="position:static !important;box-sizing: content-box !important"/>
						<span>
							
						</span>
						</label>
					</p>
				</td>
				
			</span>
        @endif

        <div class="card">
            <div class="card-content">
                <table class="responsive-table">
                    <tr class="header">
                        <th style="" width="50%">दस्तावेज़ का नाम</th>
                        <th style="" width="20%">दस्तावेज़ देखें</th>
                        @if(@$isCurrentacademicofficer_id)
                            <th style="" width="20%">सत्यापनकर्ता स्थिति</th>
                        @endif
                        <th style="" width="20%">दस्तावेज़ स्थिति</th>
                    </tr>
                </table>
            </div>
        </div>
        @foreach($masterInputArr as $k => $v)
            <div class="card">
                <div class="card-content">
                    <table class="responsive-table">
                        <tr class="header">
                            <th style="" width="50%">
                                @php $fld = @$v['upper_level']['field_id']; @endphp
                                <input type="hidden" name="upper_level[{{ @$fld }}]" value="{{  @$fld }}">
                                @php echo @$v['upper_level']['lbl']; @endphp
                            </th>
                            <th style="" width="20%">
                                @php
                                    $fldTemp = @$v['upper_level']['document_field_name'];
                                    if(@$v['upper_level']['field_id'] && $v['upper_level']['field_id'] == 28){
                                        $fldTemp = 'toc_subjects';
                                    }
                                    $path = public_path($studentDocumentPath . "/" . @$master->$fldTemp);
                                @endphp
                                @if(!empty(@$master->$fldTemp))
                                    @php
                                        $filePath = url(('public/'.$studentDocumentPath . "/" . @$master->$fldTemp));
                                    @endphp
                                    @if(file_exists($path))
                                        @php
                                            $modelContent = "<h3>Document</h3>";
                                            $modelContent = "<center><iframe class='dociframe' style='width:100%;border:none;overflow:hidden;' height='400px;' width='100%' src=" .   $filePath   ."></iframe></center>";
                                        @endphp
                                        @include('elements.draggable_model')
                                        <a href="{{ $filePath }} " target="_blank" class="invoice-action-view"
                                           title="Click here to view on new tab."><sup><span class="material-icons"
                                                                                             style="font-size:14px;">open_in_new</span></sup>
                                        </a>
                                    @else
                                        {{ "Not Found" }}
                                    @endif
                                @else
                                    {{ "Document detail missing" }}
                                @endif
                            </th>
                            @if(@$isCurrentacademicofficer_id)
                                <th style="" width="20%" id="">
                                    @if(@$verifier_upper_documents_verification[$k] && @$verifier_upper_documents_verification[$k] == 1 )
                                        <span class="chip lighten-5 green green-text">
									@else
                                                <span class="chip lighten-5 red red-text">
									@endif
                                                    {{ @$verifier_status_label[@$verifier_upper_documents_verification[$k]] }}
									</span>
                                </th>
                            @endif
                            <th style="" width="20%" id="mlbl{{ $fld }}" class="singleCheckboxAction">
								<span class="chip lighten-5 red red-text">
									Reject
								</span>
                            </th>
                        </tr>

                        @foreach($v['lower_level'] as $lk => $lv)
                            {{-- @dd($lv); --}}
                            {{-- @dd($verifier_upper_documents_verification); --}}
                            {{-- @dd($lv->field_id); --}}
                            <tr class="">
                                @php
                                    $field_id = @$lv->field_id;
                                    $fieldName = @$lv->form_filled_ref;
                                    $allowToc = false;
                                    if(@$studentdata->application->toc == 1){
                                        $allowToc = true;
                                    }
                                @endphp
                                @php $colspan = 1; @endphp
                                @if($allowToc == true && $fieldName == "custom_toc_subjects")
                                    @php $colspan = 2; @endphp
                                @else
                                    <td style="text-align:justify;">{!! @$lv->field_name !!}</td>
                                @endif
                                <td colspan="{{ $colspan }}">
                                    @php
                                        $fieldTblName = @$lv->form_filled_tbl;
                                        $fieldName = @$lv->form_filled_ref;
                                        if($fieldName == "custom_address"){
                                            $addressDispaly = CustomHelper::_getAdddressForDisplay($student_id);
                                            echo $addressDispaly;
                                        }elseif($fieldName == "custom_admission_subjects"){
                                            $admissionSubjectsDispaly = CustomHelper::_getAdmissionSubjectsForDisplay($student_id);
                                            echo $admissionSubjectsDispaly;
                                        }elseif($fieldName == "custom_exam_subjects"){
                                            $examSubjectsDispaly = CustomHelper::_getExamSubjectsForDisplay($student_id);
                                            echo $examSubjectsDispaly;
                                        }elseif($allowToc == true && $fieldName == "custom_toc_subjects"){
                                            $tocSubjectsDispaly = CustomHelper::_getTOCSubjectsForDisplay($student_id);
                                            echo $tocSubjectsDispaly;
                                        }elseif($fieldName == "pre_qualification"){
                                            $pre_qualification = 'pre_qualification';
                                            $pre_qualification = CustomHelper::_getPreQualifcaionForDisplay(@$masterDetails->$fieldTblName->$fieldName);
                                            echo @$pre_qualification;
                                        }else{
                                            if(@$fieldTblName && $fieldTblName != ""){
                                                if($fieldTblName == 'students'){
                                                    if($fieldName == 'dob'){
                                                        $masterDetails->$fieldName = 						date('d-m-Y', strtotime(@$masterDetails->$fieldName));
                                                    }
                                                    echo @$masterDetails->$fieldName;
                                                }else{
                                                    echo @$masterDetails->$fieldTblName->$fieldName;
                                                }
                                            }else{
                                                echo @$masterDetails->$fieldName;
                                            }
                                        }
                                    @endphp
                                </td>

                                @if(@$isCurrentacademicofficer_id)
                                    <td style="text-align:justify;">
                                        @if(isset($verifier_documents_verification[$k][$field_id]) && isset($verifier_status_label[$verifier_documents_verification[$k][$field_id]]))
                                            @if(@$verifier_documents_verification[$k][$field_id] == 1 )
                                                <span class="chip lighten-5 green green-text">
										@else
                                                        <span class="chip lighten-5 red red-text">
										@endif
                                                            {{ @$verifier_status_label[@$verifier_documents_verification[$k][$field_id]] }}
										</span>
                                            @endif
                                    </td>
                                @endif
                                <td style="">
                                    <p>
                                        <label>
                                            <input type="checkbox" class="chkboxaction chkbox uln{{ @$fld }}"
                                                   id="lln{{ @$fld }}_{{ @$field_id }}" data-uln="{{ @$fld }}"
                                                   data-lln="{{ @$field_id }}"
                                                   name="upper_level[{{ @$fld }}][{{ @$field_id }}]" class="filled-in"/>
                                            <span></span>
                                        </label>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endforeach
    </fieldset>
@endif