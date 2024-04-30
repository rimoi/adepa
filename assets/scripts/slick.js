$(function () {
    $(".js-slick-educatheur").slick({
		centerPadding: '50px',
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		responsive: [
			{
				breakpoint: 768,
				settings: {
					autoplay: true,
					autoplaySpeed: 2000,
					slidesToShow: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					autoplay: true,
					autoplaySpeed: 2000,
					slidesToShow: 1
				}
			}
		]
    });
})