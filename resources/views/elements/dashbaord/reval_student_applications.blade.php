@php
    $role_id = @Session::get('role_id');
    // echo Config::get("global.examination_department");
    // dd($role_id);
    $showStatus = true;

    if($showStatus){
@endphp
@if($role_id == Config::get("global.aicenter_id"))
    <div id="main">
        @elseif($role_id == Config::get("global.examination_department"))
            <div id="main2">
                @elseif($role_id == Config::get("global.evaluation_department"))
                    <div id="main">
                        @elseif($role_id == Config::get("global.developer_admin"))
                            <div id="main">
                                @else
                                    <div id="main">
                                        @endif


                                        <div class="col s12">
                                            <div class="container">
                                                <div class="seaction">
                                                    @php
                                                        if(!empty($reval)){
                                                            foreach($reval as $k => $detail){
                                                    @endphp
                                                    <div class="cardold">
                                                        <div class="card-content">

                                                            <ul class="collapsible">
                                                                <li>
                                                                    <div class="collapsible-header">
                                                                        <h5>Revaluation Application
                                                                            @if($k == 1 )
                                                                                ({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$k] }}
                                                                                )

                                                                            @else
                                                                                ({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$k] }})
                                                                            @endif
                                                                        </h5>


                                                                    <span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
                                                                    <div class="collapsible-body"><span>

                                                                            <div class="row">
                                                                <div id="card-stats" class="pt-0">
                                                                    <div class="row">
                                                                        <div class="col s12 m6 l5 xl4">
                                                                            <div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">

                                                                                <div class="padding-4">
                                                                                    <div class="row">
                                                                                        <div class="col s7 m7">
                                                                                            <i class="material-icons background-round mt-5">perm_identity</i>
                                                                                            <p style="font-size: 17px;">
                                                                                                Total Generated <br>
                                                                                                Reval Application </p>
                                                                                        </div>
                                                                                        <div class="col s5 m5 right-align">
                                                                                            <h5 class="mb-0 white-text">{{ (@$detail['reval_total_registered_student'])}}</h5>
                                                                                            <p class="no-margin"><a
                                                                                                        href="{{ route('reval_student_applications',['exam_month' => $k]) }}"
                                                                                                        class="white-text">Click
                                                                                                    Here</a></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col s12 m6 l5 xl4">
                                                                            <div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
                                                                                <div class="padding-4">
                                                                                    <div class="row">
                                                                                        <div class="col s7 m7">
                                                                                            <i class="material-icons background-round mt-5 mb-5">attach_money</i>
                                                                                            <p style="font-size: 16px;">
                                                                                                Lock & Submitted Reval
                                                                                                Application </p>
                                                                                        </div>
                                                                                        <div class="col s5 m5 right-align">
                                                                                            <h5 class="mb-0 white-text">{{ (@$detail['reval_total_lock_Submit_student'])}}</h5>
                                                                                            <p class="no-margin"><a
                                                                                                        href="{{ route('reval_student_applications',['exam_month' => $k,'locksumbitted' => 1])}}"
                                                                                                        class="white-text">Click
                                                                                                    Here</a></p>
                                                                                            <!--<p>{{@$total_lock_Submit_student}}</p>-->
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col s12 m6 l5 xl4">
                                                                            <div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
                                                                                <a href="{{ route('reval_student_applications',['exam_month' => $k,'locksumbitted' => 1,'challan_tid2' => 0])}}">
                                                                                    <div class="padding-4">
                                                                                        <div class="row">
                                                                                            <div class="col s7 m7">
                                                                                                <i class="material-icons background-round mt-5 mb-5">attach_money</i>
                                                                                                <p style="font-size: 16px;">
                                                                                                    Not Fee Paid Reval
                                                                                                    Application </p>
                                                                                            </div>
                                                                                            <div class="col s5 m5 right-align">
                                                                                                <h5 class="mb-0 white-text">{{ (@$detail['reval_get_Student_payment_not_pay_Count'])}}</h5>
                                                                                                <p class="no-margin">
                                                                                                    Click Here</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col s12 m6 l5 xl4">
                                                                            <div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
                                                                                <a href="{{ route('reval_student_applications',['exam_month' => $k,'locksumbitted' => 1,'challan_tid2' => 1,'is_eligible'=> 1]) }}">
                                                                                    <div class="padding-4">
                                                                                        <div class="row">
                                                                                            <div class="col s7 m7">
                                                                                                <i class="material-icons background-round mt-5 mb-5">attach_money</i>
                                                                                                <p style="font-size: 16px;">
                                                                                                    Fee Payment Paid
                                                                                                    Reval
                                                                                                    Application</p>
                                                                                            </div>
                                                                                            <div class="col s5 m5 right-align">
                                                                                                <h5 class="mb-0 white-text">{{ (@$detail['reval_get_Student_payment_Count'])}} </h5>
                                                                                                <p class="no-margin">
                                                                                                    Click Here</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col s12 m6 l5 xl4">
                                                                            <div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box"
                                                                                 style="background: linear-gradient(45deg,#12e471,#219243)!important;">
                                                                                <a href="{{ route('reval_student_applications',['exam_month' => $k,'is_eligible'=> 1]) }}">
                                                                                    <div class="padding-4">
                                                                                        <div class="row">
                                                                                            <div class="col s7 m7">
                                                                                                <i class="material-icons background-round mt-5 mb-5">attach_money</i>
                                                                                                <p style="font-size: 16px;">
                                                                                                    Eligible Students
                                                                                                    Reval
                                                                                                    Application</p>
                                                                                            </div>
                                                                                            <div class="col s5 m5 right-align">
                                                                                                <h5 class="mb-0 white-text">{{ (@$detail['reval_get_Eligiable_Students'])}} </h5>
                                                                                                <p class="no-margin">
                                                                                                    Click Here</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        @can('reval_marks_listing')
                                                                            <!-- <div class="col s12 m6 l5 xl4">
											<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
												<a href="{{ route('reval_marks_listing') }}" >
													<div class="padding-4">
														<div class="row">
															<div class="col s7 m7">
																<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																<p style="font-size: 16px;">Reval <br> Marks Entries</p>
															</div>
															<div class="col s5 m5 right-align">
																<h5 class="mb-0 white-text">{{ (@$detail['reval_get_Eligiable_Students'])}} </h5>
																<p class="no-margin">Click Here</p>
															</div>
														</div>
													</div>
												</a>
											</div>
										</div> -->
                                                                        @endcan



                                                                        @php
                                                                            $deleteVal = false;
                                                                            $alllowips=config('global.whiteListMasterIps');
                                                                            $CURRENT_IP=config('global.CURRENT_IP');
                                                                            if(in_array($CURRENT_IP,$alllowips)){
                                                                                $deleteVal = true;
                                                                            }
                                                                        @endphp
                                                                        @if($deleteVal == true)

                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                                        </span>
                                                                    </div>
                                                                </li>
                                                            </ul>


                                                        </div>
                                                    </div>
                                                    @php }
					}else{ @endphp
                                                    <div class="card">
                                                        <div class="card-content"><h5>
                                                                @php echo "Reval Details Not Found"; @endphp
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    @php  }
                                                    @endphp
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    @php } @endphp