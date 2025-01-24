
@extends('layouts.guest')
@section('content')
<br /><br /><br /><br /><br /><br /><br />
             <label>
                 <input type="checkbox" name="checkbox" id="multiselect-drop" value="scheckbox" />
                <span></span>
              </label>
			  
			  <label>
                 <input type="checkbox" name="checkbox2" id="checkbox2" value="scheckbox" />
                <span></span>
              </label>
        

         <br />
          <input id="showthis" name="showthis" size="50" type="text" value="text here" />
 <br /><br /><br /><br /><br /><br /><br /><br />
       
@endsection 
@section('customjs')
<script>
 $(function () {
        $('input[name="showthis"]').hide();

        //show it when the checkbox is clicked
		var $inputs = $('#multiselect-drop');
		var $inputss = $('#multiselect-drop');
        $($inputs).on('click', function () {
            if ($(this).prop('checked')) {
                $('input[name="showthis"]').fadeIn();
            } else {
                $('input[name="showthis"]').hide();
            }
        });
		$($inputss).on('click', function () {
            if ($(this).prop('checked')) {
                $('input[name="checkbox"]').fadeIn();
            } else {
                $('input[name="showthis"]').hide();
            }
        });
    });
	</script>
@endsection 




