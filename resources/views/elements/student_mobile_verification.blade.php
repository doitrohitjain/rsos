<div class="row">
    @php 
        $fld='mobile'; 
        $oldOtp = @$studentdata->otp;
    @endphp
	<input type="hidden" name='tempsid' value='{{ $estudent_id }}' class='tempsid' id='tempsid'>
    @if(@$studentdata->is_otp_verified)
        <span style="color:green;" >
            <i class="material-icons">check</i> OTP has been verified mobile nubmer : {{ @$studentdata->$fld }}
        </span>
    @else
		
        @if(@$studentdata->mobile)
            <div class="col m3 s12">
                @php $lbl='मोबाइल (Mobile)'; $lbl; $fld='mobile'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'mobilenumberinput form-control num','autocomplete'=>'off','disabled' => 'disabled','maxlength' => 10,'placeholder' => $lbl]); !!}
                @include('elements.field_error')	
                </div>
            </div>
            @if(@$studentdata->otp)
                @if(@$studentdata->is_otp_verified)
                @else
                    <div class="col m3 s12">
                        @php $lbl='ओ.टी.पी(OTP)'; $lbl; $fld='otp'; @endphp
                        <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                        <div class="input-field">
                        {!!Form::text($fld,null,['type'=>'text','class'=>'otpinput form-control num','autocomplete'=>'off','maxlength'=>6,'placeholder' => 'XXXXXX','minLength'=>6]); !!}
                        @include('elements.field_error')	
                        </div>
                    </div> 
                    <div class="col s3 s12 l3">
                        <br><br>
                        <button class="tooltipped btn btn-sm btn_disable validateotp card-panel waves-effect waves-dark teal" type="button" name="validateotp" data-position="top" data-tooltip="To verifiy otp click on validate OTP button">
                            Validate OTP
                        </button>
                    </div>
                    <div class="input-field col s12">
                        @if(@$oldOtp) 
                            Didn't get the code <a href="javascript:void(0);" data-link="{{ route('resend_student_otp_personal',[@$estudent_id]) }}" class="tooltipped disabledCustom" data-position="top" data-tooltip="Resend OTP">Resend OTP</a>
                            <span>Click on resend OTP to get OTP on mobile number {{ $studentdata->mobile }} </span>
                            <span id="div_timer" style="color:red;"></span>
                        @endif
                    </div>
                    
                @endif
            @else
                <div class="col m3 s12">
                    <br><br>
                    <button class="btn btn-sm sendotp btn_disable" type="button" name="sendotp" style="background:linear-gradient(45deg,#0c2abf,#00c6f5)!important;">
                        Send OTP
                    </button>
                </div>
            @endif
        @else
            <div class="col m3 s12">
                @php $lbl='मोबाइल (Mobile)'; $lbl; $fld='mobile'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'mobilenumberinput form-control num','autocomplete'=>'off','maxlength' => 10,'placeholder' => $lbl]); !!}
                @include('elements.field_error')	
                </div>
            </div>
            @if(@$studentdata->otp)
            @else
                <div class="col m3 s12">
                    <br><br>
                    <button class="btn btn-sm btn_disable sendotp" type="button" name="sendotp" style="background:linear-gradient(45deg,#0c2abf,#00c6f5)!important;">
                        Send OTP
                    </button>
                </div>
            @endif
        @endif
    @endif

    

</div> 