
   <fieldset>
		<legend><span style="">Student Details(विद्यार्थी विवरण)</span></legend>
		<div class="row">
		<div class="col m3 s12">
			@php 
			$star = Config::get('global.starMark');
			$lbl='एसएसओआईडी(SSOID)'; $fld='enrollment11'; @endphp 
			<span class="extraCss">@php echo $lbl @endphp </span>
			{!!Form::text($fld,@$studentdata->ssoid,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
			@include('elements.field_error')
			</div>
			<div class="col m3 s12">
				@php 
				$star = Config::get('global.starMark');
				$lbl='नाम(Name)'; $fld='enrollment'; @endphp 
				<span class="extraCss">@php echo $lbl @endphp </span>
				{!!Form::text($fld,@$studentdata->name,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
			</div>
			<div class="col m3 s12">
				@php 
				$star = Config::get('global.starMark');
				$lbl='पिता का नाम(Father name)'; $fld='enrollment'; @endphp 
				<span class="extraCss">@php echo $lbl @endphp </span>
				{!!Form::text($fld,@$studentdata->father_name,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
			</div>
			<div class="col m3 s12">
				@php 
				$star = Config::get('global.starMark');
				$lbl='माँ का नाम(Mother Name)'; $fld='enrollment'; @endphp 
				<span class="extraCss">@php echo $lbl @endphp </span>
				{!!Form::text($fld,@$studentdata->mother_name,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
			</div>
			<div class="col m3 s12">
				@php 
				$star = Config::get('global.starMark');
				 $dob= date("m-d-Y", strtotime($studentdata->dob) );
				$lbl='जन्म की तारीख (Date of Birth)(MM-DD-YYYY)'; $fld='enrollment'; @endphp 
				<span class="extraCss">@php echo $lbl @endphp </span>
				{!!Form::text($fld,@$dob,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
				
			</div>
			<div class="col m3 s12">
				@php 
				$star = Config::get('global.starMark');
				$lbl='मोबाइल(Mobile)'; $fld='enrollment'; @endphp 
				<span class="extraCss">@php echo $lbl @endphp </span>
				{!!Form::text($fld,@$studentdata->mobile,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
				
			</div>
			<div class="col m3 s12">
				@php 
				$star = Config::get('global.starMark');
				$lbl='पाठ्यक्रम ((Course)'; $fld='enrollment'; @endphp 
				<span class="extraCss">@php echo $lbl @endphp </span>
				{!!Form::text($fld,@$studentdata->course,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
				
			</div>
		</div>
	
	</fieldset>


