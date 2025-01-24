<tr>
													<td style="margin-bottom:10px;">
														@if(!empty($aicodeStudent['photograph']))  
														<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['photograph'])}}" width="60px" />
														<?php 
															$path= asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['photograph']);
															$type = pathinfo($path, PATHINFO_EXTENSION);
															$data = file_get_contents($path);
															$base64=base64_encode($data);
														?>
														<!--  <img src="data:image/{{ $type }};base64, {{ $base64 }}" alt="Image" width="60px" height="60px" />  -->
														@else 
														<img alt="studentuser" height="60px" src="{{asset('public/app-assets/images/studentuser.png')}}" width="60px" />
														@endif  
													</td>
												</tr>
												<tr>
													<!-- <td>
														@if(!empty($aicodeStudent['signature']))
														<img alt="materialize logo" height="30px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['signature'])}}" width="60px" />
														@else 
														<img alt="materialize logo" height="30px" src="{{asset('public/app-assets/images/studentsignature.png')}}" width="60px" />
														@endif 
													</td> -->
													
													<td style="margin-bottom:10px;">
														@if(!empty($aicodeStudent['signature']))  
														<img alt="materialize logo" height="60px" src="{{asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['signature'])}}" width="60px" /> 
														<?php 
															$path= asset('public/documents/'.$aicodeStudent['student_id'].'/'.$aicodeStudent['signature']);
															$type = pathinfo($path, PATHINFO_EXTENSION);
															$data = file_get_contents($path);
															$base64=base64_encode($data);
														?>
														<!--  <img src="data:image/{{ $type }};base64, {{ $base64 }}" alt="Image" width="60px" height="30px" /> -->
														@else 
														<img alt="studentsignature" height="30px" src="{{asset('public/app-assets/images/studentsignature.png')}}" width="60px" />
														@endif 
													</td>
												</tr>