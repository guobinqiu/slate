// JavaScript Document

$(function () {
	$(".taskWrap").Tabs(".taskWrap", {
		initIndex: 0,
		menus: "#task .menu a",
		menusHover: "#task .menu .hover",
		tabcons: ".all ul",
		isClick: 'click',
		conHeight: '0',
		maxHeight: '115'
	});
});