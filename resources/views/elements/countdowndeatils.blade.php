@php 
use App\Helper\CustomHelper;
$login = CustomHelper::_getCountDownTimerDetails(); 
$currentIP = CustomHelper::_getMyCurrentIp(); 
 
$macAddress = CustomHelper::_getMyMacAddress(); 
@endphp
@if(@$login)
	@php $login = json_decode($login,true); @endphp
@endif  

@can('show_ip_mac_address')
	<span class="m-0 sidebar-title m-0 blue-grey-text text-darken-4 font-weight-700" id="">
		@php
			$modelContent = '<center><h4>IP Address : <br>'. @$currentIP . ' </h4></center>';
		@endphp
		<span class="waves-effect waves-light  modal-trigger modalCls" style="color:blue;" data-content="{{ $modelContent }}">IP</span>
		&nbsp;&nbsp;
		@php
			$modelContent = '<center><h4>Mac Address : <br>'. @$macAddress . '</h4></center>';
		@endphp
		<span class="waves-effect waves-light  modal-trigger modalCls" style="color:blue;" data-content="{{ $modelContent }}">Mac</span>
		&nbsp;&nbsp;
	</span>
@endcan

<span id="count_down" class="teal-text tooltipped customTooltip" data-position="right" data-tooltip="Your login session is about to expire.">
	<span>Your login session will expire in </span>
	<span style="color:red"><span id="count_down_min" class="count_down_min"></span> Min. 
	<span id="count_down_sec" class="count_down_sec"></span> Sec.</span>
</span> 


<script>
var endtime = '<?php if(@$login['end']){ echo $login['end']; }?>';
var currentwithaddingtenmin = '<?php if(@$login['currentwithaddingtenmin']){ echo $login['currentwithaddingtenmin']; }?>';
</script>


<script src="{!! asset('public/app-assets/js/bladejs/countdowndetails.js') !!}"></script>
