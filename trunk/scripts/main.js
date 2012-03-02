// JavaScript Document
jQuery(function($){
	var cb = $.browser;
	console.log(cb);
	if(cb.webkit){
		console.log(cb.version);
	}
	else if(cb.mozilla){
		console.log(cb.version);
	}
	else if(cb.msie){
	}
	
});