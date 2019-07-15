$(document).ready(function() {
	// 갤러리
	$('a.gallery_images').magnificPopup({
		type: 'image',
		gallery: {
			enabled: true,
		},
		image: {
			titleSrc: function(item) {
				return item.el.attr('title');
			}
		}
	});
});