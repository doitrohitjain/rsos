
@include('elements.dom.apploginheader')
 
<div class="maindom">
  @yield('content')
</div> 
{{--@include('elements.dom.centerseeting')--}}
@include('elements.dom.appfooter')
 
<div class="customjs">
  @yield('customjs')
</div> 

