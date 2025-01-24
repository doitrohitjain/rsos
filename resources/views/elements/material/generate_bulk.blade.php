<div id="tap-target" class="card card-tabs">
    <div class="card-content">
        @php $current_material_genertion_stream = Config::get("global.current_material_genertion_stream"); @endphp  
        <div class="row">
            <div class="col s12" style="color:red;">
                <h4 class="header" style="color:blue;">Generate All PDF (Current Stream {{ $current_material_genertion_stream }})</h4>
            </div>
        </div>
        <div class="row">  
            <div class="col s12 center">
                <a href="{{ route($action,[10,$current_material_genertion_stream,0]) }}" class="btn mt-2"> exam center attendance roll  10 stream-{{ $current_material_genertion_stream }} Generate</a>
                &nbsp;
                <a href="{{ route($action,[12,$current_material_genertion_stream,0]) }}" class="btn mt-2">exam center attendance roll 12 stream- {{ $current_material_genertion_stream }} Generate</a>
            </div> 
        </div>
    </div>
</div>