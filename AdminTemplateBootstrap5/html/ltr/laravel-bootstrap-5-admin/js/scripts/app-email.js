function toggle(e){checkboxes=document.getElementsByName("foo");for(var i=0,o=checkboxes.length;i<o;i++)checkboxes[i].checked=e.checked}function resizetable(){$(".vertical-layout").length>0?$(".app-email .collection").css({maxHeight:$(window).height()-350+"px"}):$(".app-email .collection").css({maxHeight:$(window).height()-410+"px"})}$(document).ready(function(){"use strict";$(window).width()>900&&$("#email-sidenav").removeClass("sidenav");new Quill(".snow-container .compose-editor",{modules:{toolbar:".compose-quill-toolbar"},placeholder:"Write a Message... ",theme:"snow"});if($("#email-sidenav").sidenav({onOpenStart:function(){$("#sidebar-list").addClass("sidebar-show")},onCloseEnd:function(){$("#sidebar-list").removeClass("sidebar-show")}}),$("#sidebar-list").length>0)new PerfectScrollbar("#sidebar-list",{theme:"dark",wheelPropagation:!1});if($(".app-email .collection").length>0)new PerfectScrollbar(".app-email .collection",{theme:"dark",wheelPropagation:!1});if($(".email-list li").click(function(){var e=$(this);e.hasClass("sidebar-title")||($("li").removeClass("active"),e.addClass("active"))}),$('.app-email i[type="button"]').click(function(e){$(this).closest("tr").remove()}),$(".app-email .favorite i").on("click",function(e){e.preventDefault(),$(this).toggleClass("amber-text")}),$(".app-email .email-label i").on("click",function(e){e.preventDefault(),$(this).toggleClass("amber-text"),"label_outline"==$(this).text()?$(this).text("label"):$(this).text("label_outline")}),$(".app-email .delete-mails").on("click",function(){$(".collection-item").find("input:checked").closest(".collection-item").remove()}),$(".app-email .delete-task").on("click",function(){$(this).closest(".collection-item").remove()}),$(".sidenav-trigger").on("click",function(){$(window).width()<960&&($(".sidenav").sidenav("close"),$(".app-sidebar").sidenav("close"))}),$("#email_filter").on("keyup",function(){$(".email-brief-info").css("animation","none");var e=$(this).val().toLowerCase();""!=e?($(".email-collection .email-brief-info").filter(function(){$(this).toggle($(this).text().toLowerCase().indexOf(e)>-1)}),0==$(".email-brief-info:visible").length?$(".no-data-found").hasClass("show")||$(".no-data-found").addClass("show"):$(".no-data-found").removeClass("show")):$(".email-collection .email-brief-info").show()}),$(".compose-email-trigger").on("click",function(){$(".email-overlay").addClass("show"),$(".email-compose-sidebar").addClass("show")}),$(".email-compose-sidebar .cancel-email-item, .email-compose-sidebar .close-icon, .email-compose-sidebar .send-email-item, .email-overlay").on("click",function(){$(".email-overlay").removeClass("show"),$(".email-compose-sidebar").removeClass("show"),$("input").val(""),$(".compose-editor .ql-editor p").html(""),$("#edit-item-from").val("user@example.com")}),$(".email-compose-sidebar").length>0)new PerfectScrollbar(".email-compose-sidebar",{theme:"dark",wheelPropagation:!1});$("html[data-textdirection='rtl']").length>0&&$("#email-sidenav").sidenav({edge:"right",onOpenStart:function(){$("#sidebar-list").addClass("sidebar-show")},onCloseEnd:function(){$("#sidebar-list").removeClass("sidebar-show")}})}),$(window).on("resize",function(){resizetable(),$(".email-compose-sidebar").removeClass("show"),$(".email-overlay").removeClass("show"),$("input").val(""),$(".compose-editor .ql-editor p").html(""),$("#edit-item-from").val("user@example.com"),$(window).width()>899&&$("#email-sidenav").removeClass("sidenav"),$(window).width()<900&&$("#email-sidenav").addClass("sidenav")}),resizetable();