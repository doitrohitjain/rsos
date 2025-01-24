@include('elements.dom.appheader')
<div class="maindom">
  @yield('content')
</div> 
@include('elements.dom.appfooter')
<div class="customjs">
  @yield('customjs')
</div>