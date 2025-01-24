@extends('layouts.default')
@section('content')
    <div id="main">
      <div class="row">
	  <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Form Layouts
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
		<div class="col s12">
        <div class="container">
        <div class="seaction">
	<div class="card">
    <div class="card-content">
      <p class="caption mb-0"><h6>Form Layouts <span style="margin-left: 85%;">
	  <a href="{{ route('schoollisting') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
<h6></p>
    </div>
  </div>
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <h4 class="card-title">Form Schools</h4>
          {{ Form::open(['route' => 'add', 'method' => 'post']) }}
          {!! Form::token() !!}
            <div class="row">
              
               @php 
                  $cols = array(
                     "NICCode" => "NIC कोड (NIC Code)",
                     "UDISE" => "यूडीआईएसई (UDISE)",
                     "BoardAffiliationCode" => "बोर्ड संबद्धता कोड (Board Affiliation Code)",
                     "District" => "जिला(District)",
                     "Block" => "खंड (Block)",
                     "BLKCD" => "बीएलकेसीडी (BLKCD)",
                     'Panchayat' =>  "पंचायत(Panchayat)",
                     'PANCD' =>  "पैन डी (PANCD)",
                     'Village' =>  "गांव (Village)",
                     'VILCD' =>  "वीआईएलसीडी (VILCD)",
                     'AssembalyName' =>  "विधानसभा का नाम(Assembaly Name)",
                     'Loksabha' =>  "लोकसभा(Loksabha)",
                     'School' => "विद्यालय (School)",
                     'PrincipalName' =>  "प्राचार्य नाम (Principal Name)",
                     'MobileNo'=>  "मोबाइल नंबर (Mobile Number)",
                     'PrincipalOrHeadmasterEmail' => "प्रधानाचार्य या प्रधानाध्यापक ईमेल (Principal Or Headmaster Email)",
                     'SchoolEmailID' =>  "स्कूल ईमेल आईडी (School Email ID)",
                     'Is_ElectricityConnection' =>  "क्या बिजली कनेक्शन है (Is Electricity Connection)",
                     'Is_InternetConnection' =>  "क्या इंटरनेट कनेक्शन (Is Internet Connection)",
                     'Category' =>  "श्रेणी (Category)",
                     'Urban_Rural' =>  "शहरी ग्रामीण (Urban Rural)",
                     'IFMS_ID' =>  "आईएफएमएस आईडी (IFMS ID)",
                     'IS_PEEO' =>  "क्या पीईईओ है (IS PEEO)",
                     'TSPArea' =>  "टीएसपी क्षेत्र (TSP Area)",
                     'ICT_Phase' =>  "आईसीटी चरण (ICT Phase)",
                     'School_Type' => "स्कूल का प्रकार (School Type)",
                     'School_Category' =>  "स्कूल श्रेणी (School Category)",
                     'Vocational' =>  "व्यवसायिक (Vocational)",
                     'AdarshPhase' =>  "आदर्श चरण (Adarsh Phase)",
                     'Is_Uthakrasth' =>  "इस उतक्रष्ट (Is Uthakrasth)",
                     'ICT_Phase2' =>  "आईसीटी चरण 2 (ICT Phase 2)",
                     'Is_Uthakrasth2' =>  "इस उतक्रष्ट 2 (Is Uthakrasth 2)",
                     'School_Management' => "स्कूल प्रबंधन (School Management)",
                     'PEEO_NIC_code' =>  "पीईईओ एनआईसी कोड (PEEO NIC Code)",
                     'PEEO_Code' => "पीईईओ कोड (PEEO Code)",
                     'SchoolEstablishmentYear' => "स्कूल स्थापना वर्ष (School Establishment Year)",
                     'PStoUPSYear' => "पीएस से यूपीएस वर्ष (PS to UPS Year)",
                     'UPStoSecYear1' => "यूपीएस से सेकेंड ईयर 1 (UPS to Sec Year 1)",
                     'SecToSrSecYear' => "सेक टू सीनियर से वर्ष (Sec To Sr Se cYear)",
                  ); 
               @endphp
               @foreach ($cols as $fld => $lbl)  
                  <div class="input-field col m4 s12">
                     <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                     {!!Form::text('NICCode',old('NICCode'),['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
                     @error('NICCode')
                        <span class="invalid-feedback" role="alert" style="color:red;">
                              <strong>{{ $message }}</strong>
                           </span>
                     @enderror
                  </div> 
               @endforeach 
			  
			   
			
			 <div class="row">
			   <div class="col m10 s12 mb-3">
                      <button class="btn cyan waves-effect waves-light right" type="reset">
                        <i class="material-icons right">clear</i>Reset
                      </button>
                    </div>
                <div class="col m2 s12 mb-3">
				  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
			  {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection