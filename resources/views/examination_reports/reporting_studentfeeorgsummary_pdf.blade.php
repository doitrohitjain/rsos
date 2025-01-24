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
									<td>{{ @$user->college_name }}</td>
									<td>{{ @$user->ai_code }}</td>
									<td>{{ @$user->number_of_student }}</td>
									<td>{{ @$user->org_registration_fees }}</td>
									<td>{{ @$user->org_online_services_fees }}</td>
									<td>{{ @$user->org_add_sub_fees }}</td>
									<td>{{ @$user->org_forward_fees }}</td>
									<td>{{ @$user->org_toc_fees }}</td>
									<td>{{ @$user->org_practical_fees }}</td>
									<td>{{ @$user->org_readm_exam_fees }}</td>
									<td>{{ @$user->org_late_fee }}</td>
									<td>{{ @$user->org_total }}</td>
					
                    </tr>
					@php $i++;@endphp
                   @endforeach  
                </tbody>
              </table>
			  
			