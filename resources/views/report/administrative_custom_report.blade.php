@extends('layouts.default')
@section('content')
@php                       
	$route = Route::current()->getActionName();						
@endphp

<div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="public/app-assets/images/gallery/breadcrumb-bg.jpg">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{$title}}</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                  <li class="breadcrumb-item"><a href="{{ action($route)}}">Home</a>
                  </li>
                  <li class="breadcrumb-item active">{{$title}}
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <div class="col s12">
          <div class="container">
            <div class="section section-data-tables">
			 

  
  <!-- Page Length Options -->
  	<div class="card">
			<div class="card-content">
		<table border="0" cellspacing="4" cellpadding="4">
            <tbody>  
				<tr>
				  <td>Serial No.</td>
				  <td><input type="text" id="serialnumber" name="serialnumber"></td>
				 <td>Title</td>
                 <td><input type="text" id="title" name="title"></td>
				</tr> 
            </tbody>
        </table>
	
	<div>
		<a href="{{ action($route) }}" class="btn btn-xs btn-info right" style="margin-right: 10px;">Reset</a>
	</div>
	</div>
	 
  </div>
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <div class="row">
                <table id="reportsdatatable">
                <thead>
                  <tr>
                    <th>Serial No.</th>
					<th>Title</th>
					<th>excel</th>
                  </tr>
                </thead>
                <tbody>
              
                  @foreach ($master as $masters)
                  <tr>
				    <td>{{@$masters->serial_number}}</td>
                    <td>{{@$masters->title}}</td>
					<td>
					  <div class="invoice-action">
					  @if(@$masters->excel == 1)
					  <a href="{{ route('exportr',Crypt::encrypt(@$masters->id)) }}" class="invoice-action-view mr-4" title="Click here to Download Excel.">
					  <img height="30px" width="30px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAACcklEQVR4nGNgGAWjYBSMgmEJDOsNpfRqDX11aw0bdGsNN+vWGj5jGIzAvt6eRb9GX1uvxiBOt8Zwol6t0W69WsM3erVG/9HxQLuVQb3Uilev2thGt9YoDeRY3RrDI3o1ht+xOXbAPaBboSsIdmyNUb5ujdEi3Rqjq3o1Rn+JdeyAe0CPAofiwj7rA8jC3uv9jwxpD/isDyA99kY9UDsaA/8HXRJCBjCH4QOjHtBDCr3Obd0ooVO+qgpF3q7d6f+XH1/g8j07+gZXDBjWm/6/9eI23PC7r+7+N6gzgcsvPb4cLnfn5Z3/Rg1mg68YTZmfjhJCuUsKwOIevT7/f/7+CRb79+/f/+R5aYO3Hth7bT/cA5ceXwaLbbmwFS62+cKWwZ2Jvfp8//+AhjYIlKws///3318w+9P3T/8dO10Gtwf0ao3+zzowB27Jx28f4ez2LZ1Doxg1b7b+//LjSxTLrj+78d+gHpGpB20e0Ks1+m/VaofhgWfvn/03a7IaGh5YfXot3OGvP7+Bs2funz34k1Di3BRwUQkC3399/5+3tBBuIago9Z0QOHg9YNJo8f/uq3twC+YcnAcWP3r7GFzs2J0Tg9cDMw/MRil9bNscwOKhUyPgRSkIlK6sGHx5IGhy6P9ff37hbOvsvLwLJV+AMvqg8YBBncn/8w8vwB0IKoFMmyxRHTch4P+fv3/gahYfWzr4kpAehXjUAwMdA4OmItMb9UDAaAwMqSTkvd7/MMOIG9zFNbyuW2tkDJsLGNTD6+RMcOjVGnWAZmMG7QQHCYBRq95QRbfOOFS31rBNr8Zom16N4XNSDBgFo2AUjAKGIQEAkqNB3aFhJ4wAAAAASUVORK5CYII=">
					
					</a>
					  @elseif(@$masters->pdf == 1)
					   <a href="{{ route('reporting_pdf',Crypt::encrypt(@$masters->id)) }}" class="invoice-action-view mr-4" title="Click here to Download PDF.">>
						<img height="30px" width="30px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAACXBIWXMAAAsTAAALEwEAmpwYAAABz0lEQVR4nO3WTUsCURQG4PlvCpUwKEooFIZkCyly1S4IhBAMXIu0tJ0rwaCFYCRN6oxmDZZ9YEVFVhNORl9D88bcVm1C8V67i3nhwDDM4uGce4YrCHYYRXI4QKvK4+MyEyCNSA4H6n6/WXa5ytwCPxUF9UDALE9MKFwC0WrRR9IGgjaSBRA0kayAoIVkCQQNJGsghkXSBEp91L8B+4kNtHKWSEAJBPDaboO7Dhq6jqrHg8t0GteZDKdAtxtX6+t8Aq1Y423MzkLb2QGXwJNY7Ofmoml8Ah8KBSh+/8C4kQEvUilITiefWwzTJGfwPJnE8fIyf0CtVMLhwgJMw0Btehp6rcYXUI1G8VgskudutUq6OciyMAU+qyrp2tfHB3rNJjr5PPZDIdSDQTSXlki1VlbI//G90xkt0NB1HM7PQ/Z6URFFNObmcLa2huuNDZzG4+TdbTYLbXubLFF9ZmY0wLebG5yurmJvbAyNcBhPu7swer3fH5km6aY8OYmDSIScUXVxkT2wK8uQfT5yOaiIIl6sy+cfsRand3RExm89MwfWpqbQ2dwk47rf2gKNUAVao7XGdpfLUcFRB7KIZAOHjN3BYWN38N86OMoS7Ahs8g3oOdksI4FLUwAAAABJRU5ErkJggg=="></a>
					  </div>
					  @endif
					  </td>
                    </tr>
                   
                   @endforeach  
                   
                </tbody>
              </table>
            </div>
          </div>
      </div>
    </div>
  </div>
<!-- END RIGHT SIDEBAR NAV -->

          </div>
        </div>
      </div>
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/reporting_student_application.js') !!}"></script> 
@endsection 


