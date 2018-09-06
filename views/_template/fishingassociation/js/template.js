$(document).ready(function() {
    // 지역 선택
    $("#sido").change(function(event) {
        var my_value = $(this).val();
        url = '/home/get_region/2/'+encodeURI(my_value);
        $.post( url, function( data ) {
            console.log(data);
            $( "#sigungu" ).html( data.html );
        });
    });

    // 모더나이즈 크로스브라우징 플레이스홀더
    if (!Modernizr.input.placeholder) {

        $('[placeholder]').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function() {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur();
        $('[placeholder]').parents('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            })
        });
    }

    // 위로 가기 스크롤
    //Check to see if the window is top if not then display button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 86) {
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
        }
    });
    //Click event to scroll to top
    $('.scrollToTop').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    // nav
    $(".gnb").hover(function() {
        // $("#header").css('height','auto');
        $("#header").addClass('active');
        $(".gnb_sub").stop().slideDown('fast', function() {

        });
    }, function() {
        $(".gnb_sub").stop().slideUp('fast', function() {
          $("#header").removeClass('active');
        });
    });

    // $(".gnb > li").not('.active').hover(function() {
    //     $(this).children('a').children('img').each(function() {
    //         this.src = this.src.replace("_off.", "_on.");
    //     })
    // }, function() {
    //     $(this).children('a').children('img').each(function() {
    //         this.src = this.src.replace("_on.", "_off.");
    //     })
    // });



    // mobile nav
    $("#cd-menu-trigger").click(function(event) {
        // $(this).toggleClass('is-clicked');
        $("#mobile_gnb_wrap").toggle();
        $(".mobile_gnb_bg").show("slide", { direction: "right" }, 300);;

    });

    $(".mobile_gnb > li > a").click(function(event) {
        $(".mobile_gnb_sub").hide();
        $(this).siblings('.mobile_gnb_sub').slideToggle(200);
    });

    $(".close_mobile_gnb").click(function(event) {
        $(".mobile_gnb_bg").hide();
        $("#mobile_gnb_wrap").hide();
    });

    $("#mobile_gnb_wrap").click(function(event) {
        if (!$("#mobile_gnb_wrap").has(event.target).length) {
            $(".mobile_gnb_bg").hide();
            $("#mobile_gnb_wrap").hide();
        }
    });


    // 롤오버
    $(".rollover").hover(function() {
        this.src = this.src.replace("_off.", "_on.");
    }, function() {
        this.src = this.src.replace("_on.", "_off.");
    });

    // 툴팁
    $("[data-toggle='tooltip']").tooltip();



});