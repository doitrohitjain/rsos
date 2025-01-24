
<div > 
    <table class="responsive-table"> 
        <thead>
			<tr>		
				<th width="10%" >Sr. No</th>
				<th width="40%">Fictitious Number</th>
				<th width="10%">Absent</th>
				<th width="10%">NR</th>	
			</tr>
        </thead>
        @if(count($master) > 0)
            @php $counter = 0; @endphp
            @foreach ($master as $value)
				<tbody>
                    <tr></tr>
            @endforeach
		@else 
			<tbody><tr><td colspan="20" class="center text-red">Data Not Found</td></tr></tbody>
		@endif
	</table>
</div>

