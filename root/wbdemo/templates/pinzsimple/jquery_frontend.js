if (jQuery) {
	jQuery(document).ready(function(){
		// Check for JS in the href attribute
		function cleanHREF(str) {
			return str.replace(/\<a(.*?)href=['"](javascript:)(.+?)<\/a>/gi, "Naughty!");
		}
	});

};