@extends('layouts.default')
@section('content')
<div id="main">
    <div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
            <!-- Search for small screen-->
            <div class="container">
                <div class="row">
                    <div class="col s12 m6 l6">
                        <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
                    </div>
                    <div class="col s12 m6 l6 right-align-md">
                        <ol class="breadcrumbs mb-0"> 
                            @foreach($breadcrumbs as $v)
                                <li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
                            @endforeach 
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col s12">
            <div class="container"> 
                <div class="section section-data-tables"> 
                    @if(!empty($filters))
                        <div class="row">
                                <div class="col s12">
                                    <div class="card">
                                        <div class="card-content">
                                            @include('elements.filters.search_filter')
                                        </div>
                                    </div>
                                </div>
                            </div>
                       
                    @endif
                     </div>
                <div class="section section-data-tables"> 
                    <div class="row">
                        <div class="col s12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="row"> 
                                    <table class="responsive-table">
                                    <thead>
                                    <tr>
                                    <th>Sr.No</th>
                                    <th>Combo Name</th>
                                    <!-- <th>Current Status</th> -->
                                    <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                       
                                    @foreach ($getmasterdata as $key => $user)
                                    <tr>
                                    <td>
                                    {{ $key + 1 }}
                                    </td>
                                    <td >{{ strtoupper(str_replace("_", " ", $user->combo_name))}}</td>
                                    <!-- <td> 
                                        @if(@$user->option_val && $user->status == '1')
                                            <span style='color:green;font-size:20px;'>
                                            @php echo "Published"; @endphp
                                        @else
                                            <span style='color:red;font-size:20px;'>
                                            @php echo "Not Published"; @endphp
                                        @endif
                                        </span>
                                    </td> -->
                                    <td>
                                        <div class="invoice-action"> 
                                            <a href="{{ route('getpublishaicentermaterialedit',$user->id) }}" class="invoice-action-edit" title="Click here to Edit	.">
                                                <i class="material-icons">edit</i>
                                            </a>
                                        </div>
                                    </td>
                                    </tr>
                                    @endforeach  
                                    </tfoot>
                                    </table>
                                    {{ $getmasterdata->withQueryString()->links('elements.paginater') }}
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
<script>
$('.delete-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
</script>
@endsection 



