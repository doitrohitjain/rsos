@extends('layouts.default')
@section('content')
@php 
	use App\Helper\CustomHelper;
	$permissions = CustomHelper::roleandpermission();
	$getdatamaster1 = CustomHelper::getdatamaster();
	$getdatamastersexam = CustomHelper::getdatamastersexam();
	$selected_session = CustomHelper::_get_selected_sessions();
	$role_id = Session::get('role_id');
	$changerole = CustomHelper::_changerole();
	$resultCount = 0;
	if(@$changerole){
		$resultCount = count($changerole); 
	}
@endphp
 
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="seaction">
                    <div class="col s12 m12 l12">   
                      <div class="row">
                          <div class="col s12 m12 l12">
                            <div id="preselecting" class="card card card-default scrollspy">
                              <div class="card-content"> 
                                @if(in_array("all_Material",$permissions))
                                  <div class="card-title">
                                    <div class="row">
                                      <div class="col s12 m6 l10">
                                          <h4 class="card-title">{{ @$pageTitle }}</h4>
                                      </div> 
                                    </div>
                                  </div>
                                  <div class="row"> 
                                      <div class="col s12">
                                          <div class="row" id="main-view-tab"> 
                                              @if(isset($tableData) && !empty($tableData))
                                                <div class="col s12">
                                                  <ul style="background-color: #5b6364;" class="tabs tab-demo-active z-depth-1 cyanold" >
                                                    @foreach ($tableData as $key => $data) 
                                                      {{-- Check permissions start --}}
                                                        @php $allowedShow = false; @endphp
                                                        @if(isset($data['allowedPer']) && !empty($data['allowedPer']))
                                                          @foreach($data['allowedPer'] as $kk => $vv)
                                                            @if(in_array($vv, $permissions))
                                                              @php $allowedShow = true; @endphp
                                                            @endif
                                                          @endforeach
                                                        @endif
                                                        
                                                        @if($data['status'] && $allowedShow ) 
                                                          @php $allowedShow = true; @endphp
                                                        @else
                                                          @php $allowedShow = false; @endphp
                                                        @endif
                                                        @if(!$allowedShow) 
                                                          @php continue; @endphp 
                                                        @endif 
                                                      {{-- Check permissions end --}}

                                                      @php  
                                                        $activeCls = "";   
                                                      @endphp
                                                      @if($key == 0)
                                                          @php $activeCls = "active"; @endphp
                                                      @endif 
                                                      <li class="tab">
                                                          <a style="font-size:14px;font-weight:900;font-family: 'Muli';" class="white-text waves-effect waves-light {{ $activeCls }}" href="#{{ @$data['fld'] }}">{{ @$data['lbl'] }}</a>
                                                      </li> 
                                                    @endforeach
                                                  </ul>
                                                </div>
                                                <div class="col s12 "> 
                                                    @foreach ($tableData as $key => $data) 
                                                      {{-- Check permissions start --}}
                                                        @php $allowedShow = false; @endphp
                                                        @if(isset($data['allowedPer']) && !empty($data['allowedPer']))
                                                          @foreach($data['allowedPer'] as $kk => $vv)
                                                            @if(in_array($vv, $permissions))
                                                              @php $allowedShow = true; @endphp
                                                            @endif
                                                          @endforeach
                                                        @endif 
                                                        @if($data['status'] && $allowedShow ) 
                                                            @php $allowedShow = true; @endphp
                                                        @else
                                                          @php $allowedShow = false; @endphp
                                                        @endif
                                                        @if(!$allowedShow) 
                                                          @php continue; @endphp 
                                                        @endif  
                                                      {{-- Check permissions end --}}
                                                      @php $activeCls = "";   @endphp
                                                      @if($key == 0)
                                                          @php $activeCls = "active"; @endphp
                                                      @endif 
                                                      <div id="{{ @$data['fld'] }}" class="col s12  cyanold lighten-4" style="background-color: #d6dfe0">
                                                          <p class="mt-2 mb-2">
                                                              @if(isset($data['content']) && !empty($data['content']))
                                                                  <ol>
                                                                    @foreach ($data['content'] as $iKey => $iData)
                                                                    {{-- Inner Check permissions start --}}
                                                                      @php $innerAllowedShow = false; @endphp
                                                                      @if(isset($iData['allowedPer']) && !empty($iData['allowedPer']))
                                                                        @foreach($iData['allowedPer'] as $kk => $vv)
                                                                          @if(in_array($vv, $permissions))
                                                                            @php $innerAllowedShow = true; @endphp
                                                                          @endif
                                                                        @endforeach
                                                                      @endif
                                                                      @if($iData['status']  && $innerAllowedShow ) 
                                                                        @php $innerAllowedShow = true; @endphp
                                                                      @else
                                                                        @php $innerAllowedShow = false; @endphp
                                                                      @endif 
                                                                      @if(!$innerAllowedShow) 
                                                                        @php continue; @endphp 
                                                                      @endif
                                                                    {{-- Inner Check permissions end --}} 

                                                                    @if($iData['status']) @else @php continue; @endphp @endif 
                                                                      <span >
                                                                        <li style="color:black;font-size:24px;margin-bottom:10px;">
                                                                          @php 
                                                                          $isRoutePresentExists = CustomHelper::_checkIsRouteExists($iData['url']);
                                                                          $routeName = "javascript:vodi(0);";
                                                                          $target = "";
                                                                          if($isRoutePresentExists){
                                                                            $routeName = route($iData['url']);
                                                                            $target = "_blank";
                                                                          }
                                                                          @endphp
                                                                          
                                                                          <a target="{{ $target }}" href="{{ $routeName }}" style="font-family: Muli,sans-serif!important;font-weight: 400;line-height: 1.5;color: #6b6f82;}"> {{ $iData['lbl'] }} 
                                                                          @if($target != "")
                                                                            <span class="material-icons" style="font-size:14px;">open_in_new</span>
                                                                          @endif
                                                                        </a>
                                                                        </li>
                                                                      </span>
                                                                    @endforeach
                                                                  </ol>
                                                              @endif  
                                                          </p>
                                                      </div> 
                                                  @endforeach
                                              @endif
                                            </div> 
                                              <!-- <div class="col s12">
                                                  <ul class="tabs tab-demo-active z-depth-1 cyan">
                                                      <li class="tab col m4">
                                                          <a class="white-text waves-effect waves-light" href="#sapien">Sapien</a>
                                                      </li>
                                                      <li class="tab col m4">
                                                          <a class="white-text waves-effect waves-light active" href="#activeone">Active One</a>
                                                      </li>
                                                      <li class="tab col m4">
                                                          <a class="white-text waves-effect waves-light" href="#vestibulum">Vestibulum</a>
                                                      </li>
                                                  </ul>
                                              </div> 
                                                <div class="col s12">
                                                  <div id="sapien" class="col s12  cyan lighten-4">
                                                      <p class="mt-2 mb-2">
                                                      Cupcake ipsum dolor sit. Amet gummi bears chupa chups. Tart cotton candy fruitcake cupcake
                                                      croissant sweet biscuit candy candy.
                                                      </p>
                                                  </div>
                                                  <div id="activeone" class="col s12  cyan lighten-4">
                                                      <p class="mt-2 mb-2">
                                                      Icing tart toffee brownie carrot cake. Brownie jelly soufflé. Ice cream bear claw macaroon
                                                      pastry. Bonbon jelly cookie gummies sweet roll muffin pie.
                                                      </p>
                                                  </div>
                                                  <div id="vestibulum" class="col s12  cyan lighten-4">
                                                      <p class="mt-2 mb-2">
                                                      Cupcake ipsum dolor sit amet candy canes cake. Marshmallow brownie gummi bears jelly beans
                                                      sugar plum macaroon donut. Liquorice liquorice lollipop.
                                                      </p>
                                                  </div>
                                              </div> -->
                                          </div>
                                      </div> 
                                  </div>
                                @endif
                              </div> 
                            </div> 
                          </div> 
                        </div> 
                      </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/timetable_details_delete.js') !!}"></script> 
@endsection 


