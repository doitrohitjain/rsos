@include('elements.reportlogo')

				<table class="fl-table">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
					<th>Enrollment Number</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Course Type</th>
                    <th>Stream Type</th>
					<th>Admission Type</th>
					<th>Lock & Submit</th>
					<th>Challan Number</th>
					<th>Submitted </th>
					<th>Aumont</th>
                  </tr>
                </thead>
                <tbody>
				  @php $i=1;@endphp
                  @foreach ($result as $results)
                  <tr>
                    <td>{{@$i}}</td>
					<td>{{@$results->enrollment}}</td>
					<td>{{@$results->name}}</td>
                    <td>{{@$gender_id[$results->gender_id]}}</td>
                    <td>{{@$course[$results->course]}}</td>
                    <td>{{@$stream_id[$results->stream]}}</td>
                    <td>{{@$adm_types[$results->adm_type]}}</td>
					<td>{{ (@$results->locksumbitted == 1) ? 'yes' : 'No'}}</td>
					<td>{{@$results->challan_tid}}</td>
					<td>{{$results->submitted}}</td>
				    <td>{{@$results->total_fees}}</td>
                    </tr>
					@php $i++;@endphp
                   @endforeach  
                </tbody>
              </table>