/* Boostrap3-Extends
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Boostrap3-Extends.js - v1.0
   
   Dependencies:
   Bootstrap 3.3.7 - https://getbootstrap.com/docs/3.3/
*/

(function() {
	'use strict';
	
	/* Multiple modal fix */
	$(document).on('show.bs.modal', '.modal', function() {
		var zIndex = 1040 + (10 * $('.modal:visible').length);
		$(this).css({paddingLeft: 0, zIndex: zIndex});
		setTimeout(function() {
			$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
		}, 0);
	}).on('hidden.bs.modal', '.modal', function() {
		$('.modal:visible').length && $(document.body).addClass('modal-open');
	});
})();
