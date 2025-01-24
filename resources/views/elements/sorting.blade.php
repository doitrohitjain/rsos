<!-- Sorting Code  -->  
<div class="input-field col m12 s12">
    @php $lbl='Sorting By';   $fld='sorting[]'; @endphp 
    <h8> @php echo $lbl; @endphp </h8>
    <select name="sorting[]" multiple class="sortingMultiSelect form-control sorting select2 browser-default center-align">
        @foreach($sortingField as  $value => $field)
            @php $selectedItem = null;@endphp
             
            @if(isset($inputs['sorting']) && !empty($inputs['sorting']) && in_array(@$value,@$inputs['sorting'] ))
                @php $selectedItem = "selected";@endphp
            @endif
            <option value="{{ $value }}" data-placeholder="{{ $field['placeholder'] }}" data-field = "{{ @$field['field_same'] }}" {{ $selectedItem }}>{{ @$field['placeholder'] }}</option>
        @endforeach
    </select>
</div> 
<!-- Sorting Code -->

<script>
    $(document).ready(function() {
        var selected = [];
        var currentSelected = null;
        $('.sortingMultiSelect').on("change", function(event, params) { 
            var counter= 0;
            $(".sortingMultiSelect option:selected").each(function () {
                var $this = $(this);
                if ($this.length) {
                    selected[counter] = $this.attr('data-field');
                }
                counter++;
                currentSelectedValue = $this.attr('value');
                currentSelected = $this.attr('data-field');
                currentPlaceholderSelected = $this.attr('data-placeholder');
            });
            let len1 = selected.length;
            let len2 = $.unique(selected).length;
            if (len1 > len2) { 
                var message = "You can select asc or dec for the particular field ( " + currentPlaceholderSelected + ")";
                swal({
                    title: "Validation Error",
                    text: message,
                    icon: "error",
                    button: "Close",
                    timer: 30000
                });
                $(".sortingMultiSelect option[value='"+currentSelectedValue+"']").prop("selected", false);
            }
        });
    });
</script>