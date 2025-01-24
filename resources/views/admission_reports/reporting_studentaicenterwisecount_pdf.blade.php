@include('elements.reportlogo')
@php 
use App\Helper\CustomHelper;
@endphp
				<table class="fl-table">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
					<th>Ai Code </th>
                          <th>Center Name</th>
                          <th>Total Generated Applications</th>
                          <th>Lock & Submitted Applications</th>
                          <th>NOT Lock & Submitted Applications</th>
                          <th>Fee Paid</th>
                          <th>Locked But Not Fee Paid</th>
                  </tr>
                </thead>
                <tbody>
				  @php $i=1;@endphp

               @foreach ($master as $ai_code => $item) 
                    <tr>
                       <td>{{@$i}}</td>
                      <td>{{ $item->ai_code }}</td>
                      <td>{{ $item->college_name }}</td>
                      <td>
                        @php 
                          $custom_helper_obj = new CustomHelper;
                          $total = $custom_helper_obj->_getStudentAiCodeWise($item->ai_code);
                          echo $total;
                        @endphp 
                      </td>
                      <td>
                        @php
                          $totalLocked = $custom_helper_obj->_getStudentLockSubmttedAiCodeWise($item->ai_code);
                          echo $totalLocked;
                        @endphp 
                      </td>
                      <td>
                        @php 
                          $totalNotLocked = ($total - $totalLocked);
                          echo $totalNotLocked;
                        @endphp 
                      </td>
                      <td>
                        @php
                          $totalFeePaid = $custom_helper_obj->_getStudentFeePaidAiCodeWise($item->ai_code);
                          echo $totalFeePaid;
                        @endphp 
                      </td>
                      <td>
                        @php 
                          $totalLockedButNotFeePaid = ($totalLocked - $totalFeePaid);
                          echo $totalLockedButNotFeePaid;
                        @endphp 
                      </td>
                       
                    </tr>
          @php $i++;@endphp
                   @endforeach  
                </tbody>
              </table>