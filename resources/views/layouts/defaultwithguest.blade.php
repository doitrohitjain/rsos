@include('elements.dom.appheader')
@php 
	use App\Helper\CustomHelper;
    $isStudent = CustomHelper::_getIsStudentLogin();
@endphp

@if(@$isStudent || !empty(@$id = Auth::user()->id))
 
@endif


<div class="maindom"> 
  @yield('content')
</div>
@if(!empty(@$id = Auth::user()->id))

@endif
@include('elements.dom.appfooter')

 
<div class="customjs">
  <script src="{!! asset('public/app-assets/js/custom/custom.js') !!}"></script> 
  @yield('customjs')
</div> 
