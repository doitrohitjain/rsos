@php $currentURL = URL::current();; @endphp
@extends('layouts.logindefault')
@section('content')
   <div class="row">
    <div class="col s12">
		<div class="container"><div id="login-page" class="row">
		<div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
		  <div class="row">
			<div class="input-field col s12">
				<h5 class="card-title">{{ $page_title; }} </h5>
			 </div>
			 </div>
		@include('elements.ajax_validation_block')
		{{ Form::open(['route' => [request()->route()->getAction()['as'], 'method' => 'post'], 'id' => $model]) }}
		{!! Form::hidden('total_member',null,['type'=>'hidden','id'=>'total_member','value'=>null]); !!}
		{!! Form::hidden('jan_id',null,['type'=>'hidden','id'=>'jan_id','value'=>null]); !!}
		{!! Form::hidden('member_number',null,['type'=>'hidden','id'=>'member_number','value'=>null]); !!}		
		<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
		<input type="hidden" value="{{ $selectedAiCode}}" name ="ai_code"/>
		<div class="row margin">
			<div class="row">
			    <div class="input-field col s12">
					@php $lbl='Selected AI Centre Detail(चयनित एआई केंद्र विवरण)'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
                  	<h8>@php echo $lbl.Config::get('global.starMark') ; @endphp </h8>
					@php
						$modelContent = '<center><h4>You are select the AI Center.<br>(एआई केंद्र का चयन किया है।)<br>' .  $selectedAiCenter . '</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

					<div class="form-control" style="font-size:20px;font-weight:bold;color:blue;">
						<span id="selectedAiCenterId">{{ $selectedAiCenter }}</span>
					</div> 
				</div>	
			</div>
		   	<div class="row">
			    <div class="input-field col s12">
				    @if(!empty($ssoidget))
				    <input type="hidden" name="ssoid" value="{{$ssoidget}}"/>
					@endif
					<input type="hidden" name="is_self_filled" value="{{@$is_self_filled}}"/>
					@php $lbl='Are student from Rajasthan state?(क्या छात्र राजस्थान राज्य से हैं?)'; $placeholder = "Select ". $lbl; $fld='are_you_from_rajasthan'; @endphp
					@php $toolTipInfo= '<span  class="tooltipped customTooltip" data-position="right" data-tooltip="Are you from Rajasthan state and have jan aadhar then select Rajasthan otherwise Out of Rajasthan?(कृपया चुनें कि क्या आप राजस्थान राज्य से हैं और आपके पास जन आधार है तो राजस्थान चुनें अन्यथा राजस्थान से बाहर का विकल्प चुनें?)"><i class="material-icons mr-2"> info_outline </i></span>'; @endphp
                  	<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
					@php
						$modelContent = '<center><h4>Are you from Rajasthan state and have jan aadhar then select Rajasthan otherwise Out of Rajasthan?<br>(कृपया चुनें कि क्या आप राजस्थान राज्य से हैं और आपके पास जन आधार है तो राजस्थान चुनें अन्यथा राजस्थान से बाहर का विकल्प चुनें?)</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>


					<div class="input-field">
					{!! Form::select($fld,@$are_you_from_rajasthan,null, ['id' => $fld,'class' => 'select2 browser-default are_you_from_rajasthan form-control center-align','placeholder' => $placeholder]) !!}
					{{-- {!!  Form::label($fld, $lbl) !!} --}}
					@include('elements.field_error')
					</div>
				</div>		
			</div>
			<div class="row jan_aadhar_number_cls">
				<div class="input-field col s10">
					@php $lbl='Jan Aadhar Number or Acknowledgment Receipt Number(जन आधार नंबर या रसीद संख्या )'; $placeholder = "Select ". $lbl; $fld='jan_aadhar_number'; @endphp
					 
                  	<h8>@php echo $lbl.Config::get('global.starMark') ; @endphp </h8>

					@php
						$modelContent = '<center><h4>Please enter Janaadhar Number or Acknowledgment Receipt Number and then select your member and confirm.<br>(कृपया जनाधार संख्या या जनाधार रसीद संख्या दर्ज करें और फिर अपने सदस्य का चयन करें और पुष्टि करें।)</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

					{!!Form::text($fld,old('jan_aadhar_number'),['id'=>$fld,"onkeyupold"=>"if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')",'maxlength'=>15,'type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $placeholder]); !!}
					@include('elements.field_error')
				</div>
				<div class="input-field col s2">
					<br>
					<span class="right btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12 mb-0 " id="btnSearchJanAadharBtn2">
						<i class="material-icons">search</i>
					</span>
				</div> 
			</div>
			<div class="row jan_aadhar_number_cls">
				<div class="input-field col s12"> 
					<span style="color:green;font-size:10px"><b>Note : Enter Jan Aadhar Number, Auto fill the requird details.</b> </span>
					<br><span id="janaadharfeatchedname">&nbsp;</span>
				</div>
			</div>

			<div class="row">
				<div class="col s12">
					@php $lbl='Stream(स्ट्रीम )'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp 

                  	<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
					@php
						$modelContent = '<center><h4>Please select course Stream-I or Stream-II.<br>(कृपया पाठ्यक्रम स्ट्रीम-I या स्ट्रीम-II चुनें.)</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

					<div class="input-field">
					{!! Form::select($fld,@$stream_id,null, ['class' => 'select2 browser-default stream form-control center-align','placeholder' => $placeholder]) !!}
					{{-- {!!  Form::label($fld, $lbl) !!} --}}
					@include('elements.field_error')
					</div>
				</div>	
			</div>
			
			<div class="row">
				<div class="col s12">
					@php $lbl='Admission Type(प्रवेश प्रकार)'; $placeholder = "Select ". $lbl; $fld='adm_type'; @endphp
					 
                  	<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
					  @php
						$modelContent = '<ul>
    <li>
        <strong>Part Admission</strong>
    </li>
</ul>
<p style="margin-left:40px;">
    अभ्यर्थी जो पूर्व में किसी मान्यता प्राप्त बोर्ड/विश्वविद्यालय से माध्यमिक/उच्च माध्यमिक या कोई अन्य समकक्ष परीक्षा उत्तीर्ण कर चुका है, वह शैक्षिक योग्यता बढ़ाने के लिए अन्य विषयों में अपनी पंसद के अनुसार एक या एक से अधिक विषयो (अधिकतम चार विषय) में पंजीयन करवा सकता है। अभ्यर्थी को उत्तीर्ण होने पर केवल अंकतालिका ही दी जाती है उन्हें प्रमाण-पत्र नहीं दिया जाता है। आंशिक प्रवेश में भी अभ्यर्थी को 5 वर्ष तक 9 अवसर दिए जाते है।
</p>
<ul>
    <li>
        <strong>Improvement</strong>
    </li>
</ul>
<p style="margin-left:40px;">
    राजस्थान स्टेट ओपन स्कूल, जयपुर एवं माध्यमिक शिक्षा बोर्ड राजस्थान, अजमेर के सत्र 2022-23 में उत्तीर्ण अभ्यर्थियों को परीक्षा उत्तीर्ण होने के आगामी 1 वर्ष (सत्र 2023-24) में एक अवसर उन्नयन (प्उचतवअमउमदज) हेतु दिया जाता हैं। इसके लिए अभ्यर्थी पूर्व में उत्तीर्ण की मूल अंकतालिका तथा मूल माइग्रेशन/टी.सी. आवेदन पत्र के साथ संलग्न करेंगे। अभ्यर्थी पूर्व में उत्तीर्ण परीक्षा के उन्हीं विषयों में पंजीयन करवा सकता है, जो राजस्थान स्टेट ओपन स्कूल विषय योजना में सम्मिलित है। अपनी पंसद के अनुसार एक या एक से अधिक विषयो (अधिकतम सात विषय) में परीक्षा दे सकता है।
</p>
<ul>
    <li>
        <strong>ITI</strong>
    </li>
</ul>
<p style="margin-left:40px;">
    नेशनल कौसिल फॉर वोकेशनल टेनिंग (ITI) से मान्यता प्राप्त पाठ्यक्रम का प्रथम वर्ष (प्रथम व द्वितीय सेमेस्टर पूर्ण रूप से उत्तीर्ण) उत्तीर्ण पश्चात् अभ्यर्थी राजस्थान स्टेट ओपन स्कूल से कक्षा 10वीं के अंग्रेजी एवं हिन्दी विषय तथा कक्षा 12वीं के केवल अंग्रेजी विषय की परीक्षा उत्तीर्ण करने पर ही समकक्ष कक्षा में उत्तीर्ण माना जायेगा। समकक्षता उसी स्थिति में देय है जब हिन्दी व अंग्रेजी की परीक्षा कक्षा 10वीं व कक्षा 12वीं में तथा आई.टी.आई. के उत्तीर्ण वर्ष के साथ अथवा उसके बाद उत्तीर्ण की हो। आई.टी.आई. वाले अभ्यर्थियों को केवल अंकतालिका ही दी जाएगी यदि आई.टी.आई. का माइग्रेशन जमा कराते है तो उस स्थिति में माइग्रेशन भी दिया जा सकेगा। सत्र 2020-21 से आई.टी.आई. वाले अभ्यर्थियों के परिणाम वाले कॉलम में PASS/उत्तीर्ण शब्द अंकित किया जाना है व अंकतालिका के साथ माइग्रेशन प्रमाण-पत्र भी दिया जा सकेगा।
</p>
<ul>
    <li>
        <strong>Re Admission</strong>
    </li>
</ul>
<p style="margin-left:40px;">
    राजस्थान स्टेट ओपन स्कूल के पंजीकृत अभ्यर्थी जो निर्धारित पाँच वर्ष पूर्ण हो जाने पर भी पाठ्यक्रम उत्तीर्ण नहीं कर पाए हैं वे सत्र् 2023-24 में पुनः प्रवेश लेकर (तालिका-2) नियमानुसार पंजीकरण करवा सकते हैं।
</p>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

					<div class="input-field">
					{!! Form::select($fld,@array($lbl),null, ['class' => 'select2 browser-default form-control adm_type center-align','placeholder' => $placeholder]) !!}
					{{-- {!!  Form::label($fld, $lbl) !!} --}}
					@include('elements.field_error')
					</div>
                </div>	
			 </div>
			 
			<div class="row">
				<div class="col s12">
					@php $lbl='Course(पाठ्यक्रम)'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
					 
					<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
					  @php
						$modelContent = '<center><h4>Those students who want to be admission to the 10th and 12th class.(जो छात्र 10वीं और 12वीं कक्षा में प्रवेश लेना चाहते हैं।)</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

					<div class="input-field">
					{!! Form::select($fld,@$course,null, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
					{{-- {!!  Form::label($fld, $lbl) !!} --}}
					@include('elements.field_error')
					</div>
				</div>	
			</div>
		</div>
		
		  
		<div class="row">
		 <div class="col m9 s12 mb-1">
				<button class="disabled submitBtn right btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange"type="submit" >Save
					<i class="material-icons right">send</i>& Continue
				</button>
			</div>
			<div class="input-field col s12">
				<center><a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text" style="">Cancel</a></center>
			</div>
			<div class="content-overlay"></div>
		<div id="modal1" class="modal large modal-fullscreen modal-dialog">
			<div class="modal-header">
			</div>
			<div class="modalRegistraion modal-content">
			</div>
			<div class="modalFooter model-footer">
			</div>
		</div>
		<style> 
        .modal {
				overflow-y: auto;
				width: 100%;
				max-height: 85%;
				border-radius: 2px;
				background-color: #fafafa;
				will-change: top, opacity;
			} 
			.modal-content {
			  height: auto;
			  min-height: 100%;
			  border-radius: 0;
			}
		</style>
		 
		
		</div>
	</div>  
	{{ Form::close() }}
	</div>
	</div>
</div>
</div>
		

<script>
    var config = {
        data: {
			formId: "{{ $model; }}",
		},
    };
    var url = '<?php echo $currentURL; ?>';
</script>

@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/registration_details.js') !!}"></script>
	<script type="text/javascript">
	function preventBack() {
				window.history.forward(); 
				}

				setTimeout("preventBack()", 0);

				window.onunload = function () { null };
</script>
@endsection 








