var ajaxBaseUrl = phpJsVarSet.routes.ajaxBaseUrl;
var current_ip = phpJsVarSet.extra.current_ip;
var isBackAllowedForTheCurrentRole = phpJsVarSet.extra.isBackAllowedForTheCurrentRole;
var whiteListMasterIps = phpJsVarSet.extra.whiteListMasterIps;
var current_login_role_id = phpJsVarSet.extra.current_login_role_id;
var allowedBackButtonForRoles = phpJsVarSet.extra.allowedBackButtonForRoles;


whiteListMasterIps = $.parseJSON(whiteListMasterIps); //convert to javascript array
current_ip = current_ip.replace('"', '');
current_ip = current_ip.replace('"', '');

function showLoading() {
    $('.mainCls').removeClass('hide');
}

function hideLoading() {
    $('.mainCls').addClass('hide');
}

document.onreadystatechange = function () {
    var state = document.readyState;
    if (state == 'interactive') {
        showLoading();
    } else if (state == 'complete') {
        hideLoading();
    }
    var height = $(window).height();
};

// $( "a , button" ).on( "click", function() {
// 	showLoading();
// 	setTimeout(function(){
// 		hideLoading();
// 	}, 10000);
// });

$(document).bind('ajaxStart', function () {
    showLoading();
}).bind('ajaxStop', function () {
    hideLoading();
});


$('.resetBtn').html("<button onclick='location.reload();' class='btn btn-primary'>Reset</button>");

$('form *').prop('autocomplete', 'off');


$('table').each(function () {
    $(this).parent("div").addClass("scroll");
});


function showConfirmBox(formId) {
    var clsName = "api";
    if ($(formId).hasClass(clsName)) {
        alert('a');
        return true;
    } else {

        event.preventDefault();
        swal({
            title: 'Are you sure save your information.',
            text: "You won't be able to revert this!",
            icon: 'success',
            buttons: true,
        })
            .then((willsave) => {
                if (willsave) {
                    alert('b');
                    $(formId).addClass(clsName);
                    $(formId).submit();
                }
            });
    }
}

function preventBack() {
    window.history.forward();
}

// console.log(isBackAllowedForTheCurrentRole);
if (isBackAllowedForTheCurrentRole) {
} else {
    if ($.inArray(current_ip, whiteListMasterIps) != -1) {
    } else {
        setTimeout("preventBack()", 0);
    }
}

window.onunload = function () {
    null
};

$('.modalCls').on('click', function () {
    var content = $(this).attr('data-content');
    var staus = toggleModel(content);
});

function toggleModel(content = null) {
    $('#myModalInfoTooltip').modal('close');
    $('#modalContentId').html("");
    $('#modalContentId').html(content);
    $('#myModalInfoTooltip').modal('open');
    return true;
}


$('.modalDragableCls').on('click', function () {
    var content = $(this).attr('data-content');
    var staus = toggleModelDragable(content);
});

function toggleModelDragable(content = null) {
    $('#myModalDragableInfoTooltip').modal('close');
    $('#modalDragableContentId').html("");
    $('#modalDragableContentId').html(content);
    $('#myModalDragableInfoTooltip').modal('open');
    return true;
}

// For Govt notificatins start
$('.modalGovtNotificationCls').on('click', function () {
    var content = '<iframe src="https://rajadvt.rajasthan.gov.in/#/" class="myIframe w-100" height="600px"></iframe>';
    var staus = toggleModel(content);
});

function toggleModel(content = null) {
    $('#myModalInfoTooltip').modal('close');
    $('#modalContentId').html("");
    $('#modalContentId').html(content);
    $('#myModalInfoTooltip').modal('open');
    return true;
}

// For Govt notificatins end
//alert(whiteListMasterIps);


if ($.inArray(current_ip, whiteListMasterIps) != -1) {

} else {
    /* Disable Developer Tools start */
    $(document).bind("contextmenu", function (e) {
        return false;
    });
    document.onkeypress = function (event) {
        event = (event || window.event);
        if (event.keyCode == 123) {
            return false;
        }
    }
    document.onmousedown = function (event) {
        event = (event || window.event);
        if (event.keyCode == 123) {
            return false;
        }
    }
    document.onkeydown = function (event) {
        event = (event || window.event);
        if (event.keyCode == 123) {
            return false;
        }
    }
    /* Disable Developer Tools end */
}

$(document).ready(function() {
	if ($('#mainalert').length === 1) {
		// Create a new div with id="mainalertinner" and set its text content
		var newDiv = '<span> &nbsp;&nbsp;&nbsp; डैशबोर्ड में आसान नेविगेशन के लिए कॉलेप्सिबल लेबल किए गए हैं।</span>';
		// Append the new div to the element with id="mainalert"
		$('#mainalert').html(newDiv);
	}
});


