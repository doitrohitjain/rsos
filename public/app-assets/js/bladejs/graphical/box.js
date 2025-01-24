$(document).ready(function() {  
    showLoading();  
    $.ajax({
        url: config.routes.htmlGrpahicalData,
        type: "get",
        dataType: "html", 
        success: function (html) {
            hideLoading(); 
            $("#maindata").append(html);
        },
        error: function (response) {
            hideLoading();
            toastr.error(response);
            console.log('Error:', response);
        }

    });
});

 