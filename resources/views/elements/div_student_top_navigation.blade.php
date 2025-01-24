<div class="card">
    <div class="card-content"> 
        <div id="card-stats" class="row">
        
             
        @php
            $menus =  array(
                'persoanl_details' => "Personal",
                'address_details' => "Address",
                'bank_details' => "Bank",
                'document_details' => "Documents",
                'admission_subject_details' => "Subjects",
                'toc_subject_details' => "TOC Subjects",
                'exam_subject_details' => "Exam Subjects",
                'fee_details' => "Fees"
            );
                
            if(@$isLockAndSubmit == 1){
                $menus['view_details'] =  "View";
            }else{
                $menus['preview_details'] =  "Preview";
            }
            
            
        @endphp

        <div class="col s12 m3 xl2">
        </div>
        @foreach($menus as $link => $label)
        <div class="col s12 m3 xl1">
            <div class="input-field">
                <a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="{{ route($link,Crypt::encrypt($student_id)) }}">
                    <span class="dropdown-title3" data-i18n="Persoanl">
                        @php echo @$label @endphp
                    </span>  
                </a>
            </div>  
        </div>

        @endforeach 
        
        </div>
    </div>
</div>