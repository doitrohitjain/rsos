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
							  
                              @if(in_array("all_reports",$permissions)  || in_array("examination_reportexamcenterwise",$permissions))
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
                                            @if(isset($master) && !empty($master))
                                              <div class="col s12">
                                                <ul style="background-color: #5b6364;" class="tabs tab-demo-active z-depth-1 cyanold" >
                                                  @foreach ($master as $key => $data)
                                                    {{-- Check permissions start --}}
                                                      @if($data['status']) 
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
                                                        <a style="font-size:14px;font-weight:900;font-family: 'Muli';" class="white-text waves-effect waves-light {{ $activeCls }}" href="#{{ @$data['report_category_id'] }}">{{ @$data['title'] }}</a>
                                                    </li> 
                                                  @endforeach
                                                </ul>
                                              </div>
                                              <div class="col s12 "> 
                                                  @foreach ($master as $key => $data) 
                                                    {{-- Check permissions start --}}
                                                    @if($data['status']) 
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
                                                    <div id="{{ @$data['report_category_id'] }}" class="col s12  cyanold lighten-4" style="background-color: #d6dfe0">
                                                        <p class="mt-2 mb-2">
                                                            
                                                            @if(isset($data['content']) && !empty($data['content']))
                                                            <table class="table display table table-striped table-bordered" id="table_{{ @$data['report_category_id'] }}_table">
                                                                <thead>
                                                                  <tr style="color:black;font-size:16px;margin-bottom:10px;">
                                                                    <th width="15%">Report No.</th>
                                                                    <th width="85%" class="ddd">Title Link<span style="color:blue;">(Click on title)</span></th>
                                                                  </tr>
                                                                </thead>
                                                                <tbody>
                                                                  @php $tempCount = 0; @endphp
                                                                  @foreach ($data['content'] as $iKey => $iData)
                                                                  
                                                                  @php
                                                                    if(!empty($iData['permissions'])){
                                                                      $iData['allowedPer'] = explode(",",$iData['permissions']);
                                                                    }
                                                                  @endphp
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
                                                                  @if($iData['status']) @php $tempCount++; @endphp @else @php continue; @endphp @endif 
                                                                    
                                                                      @php 
                                                                        $icon=null;
                                                                        if($iData['is_sql'] == 1){
                                                                          $url = Config::get('global.APP_URL') . 'report_master/export/' . Crypt::encrypt($iData['id']);
                                                                          $icon = "excel";
                                                                        }else{
                                                                          $url = Config::get('global.APP_URL') . $iData['url'];
                                                                          //$url = $iData['url'];
                                                                        }
																		
                                                                      @endphp
                                                                      
                                                                          <tr>
                                                                            <td width="15%">{{ $iData['serial_number'] }}</td>
                                                                            <td width="85%">
                                                                              <span style="color:black;font-size:16px;margin-bottom:10px;">
                                                                                <a target="_blank" href="{{ $url }}" style="    font-family: Muli,sans-serif!important;
                                                                                font-weight: 400;
                                                                                line-height: 1.5;
                                                                                color: #6b6f82;"> {{ $iData['title'] }} 
                                                                                @if($icon == 'excel')
                                                                                <img height="4%" width="4%" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAACcklEQVR4nGNgGAWjYBSMgmEJDOsNpfRqDX11aw0bdGsNN+vWGj5jGIzAvt6eRb9GX1uvxiBOt8Zwol6t0W69WsM3erVG/9HxQLuVQb3Uilev2thGt9YoDeRY3RrDI3o1ht+xOXbAPaBboSsIdmyNUb5ujdEi3Rqjq3o1Rn+JdeyAe0CPAofiwj7rA8jC3uv9jwxpD/isDyA99kY9UDsaA/8HXRJCBjCH4QOjHtBDCr3Obd0ooVO+qgpF3q7d6f+XH1/g8j07+gZXDBjWm/6/9eI23PC7r+7+N6gzgcsvPb4cLnfn5Z3/Rg1mg68YTZmfjhJCuUsKwOIevT7/f/7+CRb79+/f/+R5aYO3Hth7bT/cA5ceXwaLbbmwFS62+cKWwZ2Jvfp8//+AhjYIlKws///3318w+9P3T/8dO10Gtwf0ao3+zzowB27Jx28f4ez2LZ1Doxg1b7b+//LjSxTLrj+78d+gHpGpB20e0Ks1+m/VaofhgWfvn/03a7IaGh5YfXot3OGvP7+Bs2funz34k1Di3BRwUQkC3399/5+3tBBuIago9Z0QOHg9YNJo8f/uq3twC+YcnAcWP3r7GFzs2J0Tg9cDMw/MRil9bNscwOKhUyPgRSkIlK6sGHx5IGhy6P9ff37hbOvsvLwLJV+AMvqg8YBBncn/8w8vwB0IKoFMmyxRHTch4P+fv3/gahYfWzr4kpAehXjUAwMdA4OmItMb9UDAaAwMqSTkvd7/MMOIG9zFNbyuW2tkDJsLGNTD6+RMcOjVGnWAZmMG7QQHCYBRq95QRbfOOFS31rBNr8Zom16N4XNSDBgFo2AUjAKGIQEAkqNB3aFhJ4wAAAAASUVORK5CYII=">
                                                                                @endif
                                                                                <span class="material-icons" style="font-size:14px;">open_in_new</span>
                                                                                </a>
                                                                              </span>
                                                                            </td>
                                                                          </tr>
                                                                     
                                                                  @endforeach
                                                                  </tbody>
                                                                </table>


                                                                @if($tempCount === 0)
                                                                  <div style="color:black;font-size:24px;margin-bottom:10px;">No Report in {{ $data['title'] }}</div>
                                                                @endif
                                                            @else 
                                                            @endif  
                                                        </p>
                                                    </div> 
                                                @endforeach
                                            @endif
                                          </div> 
                                          {{-- Templeate Start --}}
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
                                          {{-- Templeate End --}}
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
	<script src="{!! asset('public/app-assets/js/bladejs/front_view.js') !!}"></script> 
@endsection 

