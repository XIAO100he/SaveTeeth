/*global $*/
'use strict';

$(function () {
	var scrollAuto = $('.js-scroll');
	scrollAuto.animate({
		scrollTop: scrollAuto[0].scrollHeight
	}, 100);
});

