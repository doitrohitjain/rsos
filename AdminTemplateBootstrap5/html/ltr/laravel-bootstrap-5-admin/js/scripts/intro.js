$(window).on("load",function(){$(".modal").modal({onOpenEnd:function(){$(".carousel.carousel-slider").carousel({fullWidth:!0,indicators:!0,onCycleTo:function(){1==$(".carousel-item.active").index()?$(".btn-prev").addClass("disabled"):$(".carousel-item.active").index()>1&&($(".btn-prev").removeClass("disabled"),$(".btn-next").removeClass("disabled"),3==$(".carousel-item.active").index()&&$(".btn-next").addClass("disabled"))}})}}),setTimeout(function(){$(".modal").modal("open")},1800),$(".btn-next").on("click",function(e){$(".intro-carousel").carousel("next")}),$(".btn-prev").on("click",function(e){$(".intro-carousel").carousel("prev")})});