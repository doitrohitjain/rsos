
@extends('layouts.default')
@section('content') 

<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="seaction">
                    
                    <div id="cards-extended">
                        <div class="card">
                            <div class="card-content">
                                <div class="row">
                                    <div class="col s12 m6 l3">
                                    <div class="card animate fadeLeft">
                                        <div class="card-content cyan white-text">
                                            <p class="card-stats-title"><i class="material-icons">person_outline</i> New Clients</p>
                                            <h4 class="card-stats-number white-text">566</h4>
                                            
                                        </div>
                                        
                                    </div>
                                    </div>
                                    <div class="col s12 m6 l3">
                                    <div class="card animate fadeLeft">
                                        <div class="card-content red accent-2 white-text">
                                            <p class="card-stats-title"><i class="material-icons">attach_money</i>Total Sales</p>
                                            <h4 class="card-stats-number white-text">$8990.63</h4>
                                        </div> 
                                    </div>
                                    </div>
                                    <div class="col s12 m6 l3">
                                    <div class="card animate fadeRight">
                                        <div class="card-content orange lighten-1 white-text">
                                            <p class="card-stats-title"><i class="material-icons">trending_up</i> Today Profit</p>
                                            <h4 class="card-stats-number white-text">$806.52</h4> 
                                        </div> 
                                    </div>
                                    </div>
                                    <div class="col s12 m6 l3">
                                    <div class="card animate fadeRight">
                                        <div class="card-content green lighten-1 white-text">
                                            <p class="card-stats-title"><i class="material-icons">content_copy</i> New Invoice</p>
                                            <h4 class="card-stats-number white-text">1806</h4> 
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
</div>
@endsection 


@section('customjs')
	 
@endsection 

