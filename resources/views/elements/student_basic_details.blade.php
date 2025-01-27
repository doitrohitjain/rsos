@php use App\Helper\CustomHelper; @endphp
<fieldset style="display:block;">
    <legend>छात्र का विवरण  (Student's Details)</legend>
    <table>
        <tr>
            <th>Name</th>
            <th>SSO</th>
            <th>AI Centre</th>
            <!-- <th rowspan="2"> 
                <span class="font"> 
                    @php  
                        $dump_image='users1.png';	
                        $image_path=public_path($studentDocumentPath . "/" . @$masterDetails->document->photograph);
                        if(file_exists($image_path)){
                            @$image_path =url('public/'.$studentDocumentPath . "/" . @$masterDetails->document->photograph);
                        }else{
                            $image_path=url('public/app-assets/images/'.$dump_image);
                        }
                    @endphp
                    <img alt="Photo" height="60px" src="{{ $image_path}}" width="50px" height="50px;" />
                </span>
            </th> -->
        </tr>
		 
		
        <tr>  
            <td>{{ @$masterDetails->name }}</td>
            <td>{{ @$masterDetails->ssoid }}</td>
            <td>
                @php 
                    $detail = CustomHelper::getAICenterDetailsByAiCode(@$masterDetails->ai_code);
                @endphp
                {{ @$detail }}
            </td>
        </tr>
    </table>
	
</fieldset>



@php  	
	$category_c =null;
	$category_d =null;
	
	
	if(@$masterDetails->document->category_c != null){
		$category_c=public_path($studentDocumentPathTemp . @$masterDetails->document->category_c);
	}
	if(@$masterDetails->document->category_d != null){
		$category_d=public_path($studentDocumentPathTemp .  @$masterDetails->document->category_d);
	}
	
@endphp
@if(($category_c != null ||$category_d != null) && (file_exists($category_c) || file_exists($category_d)))
	<fieldset>
		<legend>छात्र के अन्य दस्तावेज़ (Student's Other documents)</legend>
		<table>
		@if($category_c != null && file_exists($category_c))
		 
			<tr><th style="width:50%">Other Document 1</th>
				 <th style="width:50%"> 
					<span class="font"> 
					
					<a target="_blank" title="Click here to verify Other Document 1" href="{{ asset('public'.'/'.$studentDocumentPathTemp . '/' . @$masterDetails->document->category_c)}}">
						<span class="material-icons" style="font-size:20px;">open_in_new</span>
					</a>
						
					</span>
				</th> 
			</tr>
		@endif
		
		@if($category_d != null && file_exists($category_d))
		
			<tr>
			    <th style="width:50%">Other Document 2</th>
				 <th style="width:50%"> 
					<span class="font"> 
					<a target="_blank" title="Click here to verify Other Document 2" href="{{ asset('public'.'/'.$studentDocumentPathTemp . '/' . @$masterDetails->document->category_d)}}">
					<span class="material-icons" style="font-size:20px;">open_in_new</span>
					</a>
						
					</span>
				</th>
				 <!--Other-I,Other-II Document_category_c Document_category_d --> 
			</tr>
		@endif
	</table>	

	</fieldset>
@endif