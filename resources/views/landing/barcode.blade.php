<html>
	<head>
		<style>
			body
			{
			 margin:0 auto;
			 padding:0px;
			 text-align:center;
			 width:100%;
			 font-family: "Myriad Pro","Helvetica Neue",Helvetica,Arial,Sans-Serif;
			 background-color:#F5EEF8;
			}
			#wrapper
			{
			 margin:0 auto;
			 padding:0px;
			 text-align:center;
			 width:995px;
			}
			#wrapper h1
			{
			 margin-top:50px;
			 font-size:45px;
			 color:#884EA0;
			}
			#wrapper h1 p
			{
			 font-size:18px;
			}
			#barcode_div input[type="text"]
			{
			 width:300px;
			 height:35px;
			 border:none;
			 padding-left:10px;
			 font-size:17px;
			}
			#barcode_div input[type="submit"]
			{
			 background-color:#884EA0;
			 border:none;
			 height:35px;
			 color:white;
			}
		</style>
	</head>
<body>
<div id="wrapper">

<div id="barcode_div">

		<div id="html-view-validations">
                {!! Form::open(array('route' => 'rahul','method'=>'POST','id' => 'barcodeID')) !!}
				<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
              <div class="col m4 s12">
				@php $lbl="बारकोड (Barcode)"; $fld='generate_barcode'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
					@include('elements.field_error')	
				</div>
			</div>
               <div class="row">
					<div class="col m2 s12 mb-3">
						<button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
							<i class="material-icons right">send</i>
						</button>
					</div>
              </div>
		  </div>
          {!! Form::close() !!}
</div>
