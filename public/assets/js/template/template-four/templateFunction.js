$(function() {
    $(".suppliers-slider-cities").slick({
        arrows: true,
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 3,
        responsive: [
            { breakpoint: 1367, settings: { slidesToShow: 4, slidesToScroll: 2, infinite: true } },
            { breakpoint: 991, settings: { slidesToShow: 3, slidesToScroll: 1 } },
            { breakpoint: 767, settings: { slidesToShow: 3, slidesToScroll: 1 } },
            { breakpoint: 360, settings: { slidesToShow: 3, slidesToScroll: 1 } }
        ]
    });
    $(".suppliers-slider-long_term_service").slick({
        arrows: true,
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 3,
        responsive: [
            { breakpoint: 1367, settings: { slidesToShow: 4, slidesToScroll: 2, infinite: true } },
            { breakpoint: 991, settings: { slidesToShow: 3, slidesToScroll: 1 } },
            { breakpoint: 767, settings: { slidesToShow: 3, slidesToScroll: 1 } },
            { breakpoint: 360, settings: { slidesToShow: 3, slidesToScroll: 1 } }
        ]
    });
    // $('.render_long_term_service').slick({
    //     infinite: true,
    //     speed: 300,
    //     slidesToShow: 5,
    //     slidesToScroll: 3,
    //     centerMode: false,
    //     centerPadding: '60px',
    //     arrows: true,
    //     dots: false,
    //     responsive: [
    //         { breakpoint: 1199, settings: { slidesToShow: 4, slidesToScroll: 1, infinite: true, dots: false, centerMode: true, } },
    //         { breakpoint: 991, settings: { slidesToShow: 3, slidesToScroll: 1, dots: false, centerMode: true, } },
    //         { breakpoint: 767, settings: { slidesToShow: 3, slidesToScroll: 1, dots: false, centerMode: true, } },
    //         { breakpoint: 576, settings: { slidesToShow: 3, slidesToScroll: 1, dots: false, centerMode: true, centerPadding: '0', } }
    //     ]
    // });
})