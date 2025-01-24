<div class="card col s12 m12 l12">
    @foreach(@$subjectWiseFacultyMaster as $facultyTypeUpper => $subjectUpper)
        <div>
            <table2>
                <tr>
                    <td width="10%">
                        <span class="" style="font-weight: 800;font-size:20px;">
                            @if($facultyTypeUpper == 'is_science_faculty')
                                @php echo "Science"; @endphp
                            @endif

                            @if($facultyTypeUpper == 'is_commerce_faculty')
                                @php echo "Commerce"; @endphp
                            @endif

                            @if($facultyTypeUpper == 'is_arts_faculty')
                                @php echo "Arts"; @endphp
                            @endif

                            @if($facultyTypeUpper == 'is_allow_faculty')
                                @php echo "Common"; @endphp
                            @endif
                            :
                        </span>
                    </td>
                    <td width="20%">
                    @php   $limit = count($subjectUpper);
                        $counter=0;  @endphp
                        @foreach(@$subjectUpper as $subjectId => $subject)
                            @php 
                                $counter++;
                            @endphp
                            <td>
                                @if($counter == $limit)
                                    {{ @$subject }}
                                @else
                                    {{ @$subject }} ,
                                @endif
                                
                            </td>
                        @endforeach
                    </td>
                </tr>
            </table> 
        </div>
    @endforeach
</div>