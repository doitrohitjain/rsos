@include('elements.reportlogo')
				<table class="fl-table" style="table-layout: fixed;">
                <thead>
                  <tr>
                    <th>Sr no</th>
									<th>College Name</th>
									<th>AiCode</th>
									<th>Number Of Student</th>
									<th>Registration Fees</th>
									<th>Services Fees</th>
									<th>ADD Subject Fees</th>
									<th>Forward Fees</th>
									<th>Toc Fees</th>
									<th>Practical Fees </th>
									<th>Readm Exam Fees</th>
									<th>Late Fees</th>
									<th>Total</th>
                  </tr>
                </thead>
                <tbody>
				  @php $i=1;@endphp
                  @foreach ($result as $results)
                  <tr>
                  <td>{{ @$i }}</td>
									<td>{{ @$results->college_name }}</td>
									<td>{{ @$results->ai_code }}</td>
									<td>{{ @$results->number_of_student }}</td>
									<td>{{ @$results->registration_fees }}</td>
									<td>{{ @$results->online_services_fees }}</td>
									<td>{{ @$results->add_sub_fees }}</td>
									<td>{{ @$results->forward_fees }}</td>
									<td>{{ @$results->toc_fees }}</td>
									<td>{{ @$results->practical_fees }}</td>
									<td>{{ @$results->readm_exam_fees }}</td>
									<td>{{ @$results->late_fee }}</td>
									<td>{{ @$results->total }}</td>
					
                    </tr>
					@php $i++;@endphp
                   @endforeach  
                </tbody>
              </table>
			  
			