var Buttons = function() {
	
	var status = 0;
	
	var init = function(els) {
		if (status) return;
		$('body').on('click', 'a[data-href], button[data-href]', redirect);
		$('body').on('click', 'a[data-trigger], button[data-trigger]', trigger);
		$('body').on('click', 'a[data-imagemodal], button[data-imagemodal]', imageModal);
		status = 1;
	};
	
	var redirect = function(e) {
		var href = $(this).data('href');
		var target = $(this).data('target');
		if (typeof(href) != 'undefined') {
			if (typeof(target) != 'undefined' && target != '_self') {
				var w = window.open(href, target);
				w.focus();
			} else {
				window.location.assign(href);
			}
		}
	}
	
	var trigger = function(e) {
		var fn = $(this).data('trigger');
		if (typeof(fn) != 'undefined' && typeof(eval(fn)) == 'function') {
			return eval(fn+'(this)');
		}
		return(true);
	}

	var imageModal = function(e){
		var src = $(this).data('imagemodal');
		console.log(src);
		Boxes.showImage(src);
	}
	
	return({
		init: init,
	});
}();

var Boxes = function() {
	var alertTmpl =
		'<div class="modal fade" tabindex="-1">' +
			'<div class="modal-dialog">' +
				'<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<h5 class="modal-title" id="exampleModalLabel1">Alert</h5>' +
                        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                    '</div>' +
					'<div class="modal-body">' +
					'</div>' +
					'<div class="modal-footer">' +
						'<button type="button" data-bs-dismiss="modal" class="btn btn-info"><i class="menu-icon tf-icons bx bx-check"></i> Confirm</button>' +
					'</div>' +
				'</div>' +
			'</div>'
		'</div>';
	
	var confirmTmpl =
		'<div class="modal fade" tabindex="-1">' +
			'<div class="modal-dialog">' +
				'<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<h5 class="modal-title" id="exampleModalLabel1">Confirmation</h5>' +
                        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                    '</div>' +
                    '<div class="modal-body">' +
					'</div>' +
					'<div class="modal-footer">' +
						'<button type="button" data-bs-dismiss="modal" class="btn btn-danger me-auto"><i class="menu-icon tf-icons bx bx-arrow-back"></i> Cancel</button>' +
						'<button type="button" data-bs-dismiss="modal" data-confirm class="btn btn-info"><i class="menu-icon tf-icons bx bx-check"></i> Confirm</button>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

	var imageTmpl =
		'<div class="modal fade" tabindex="-1">' +
			'<div class="modal-dialog">' +
				'<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                    '</div>' +
                    '<div class="modal-body">' +
						'<img src="" style=" max-width: 100%; max-height: 500px; width: auto; "/>'
					'</div>'
				'</div>' +
			'</div>' +
		'</div>';
	
	return { 
		alert: function(message, callback) {
            var id = Date.now();
        	var modal = $(alertTmpl);
            modal.attr('id', id);
        	var content = $('<div class="bootbox-body"></div>');
        	content.html(message);

        	modal.find('.modal-body').append(content);
            modal.appendTo('body');
            var AlertModal = bootstrap.Modal.getOrCreateInstance(modal);

        	if (typeof(callback) != 'undefined' && typeof(eval(callback)) == 'function') {
        		modal.find('button[data-bs-dismiss]').click(function(e) {
        			callback.call(this);
                    AlertModal.dispose();
        		});
        	}

            AlertModal.toggle();
        },		
        confirm: function(message, callback) {
            var id = Date.now();
        	var modal = $(confirmTmpl);
            modal.attr('id', id);
        	var content = $('<div class="bootbox-body"></div>');
        	content.html(message);
        	modal.find('.modal-body').append(content);
            modal.appendTo('body');
            var ConfirmModal = bootstrap.Modal.getOrCreateInstance(modal);

        	modal.find('button[data-confirm]').click(function(e) {
        		var confirm = $(this).data('confirm');
        		if (typeof(callback) != 'undefined' && typeof(eval(callback)) == 'function') {
        			var result = confirm == 'confirm';
        			callback.call(this, result);
        		}
                ConfirmModal.toggle();
        	});

            ConfirmModal.toggle();
        },
		showImage: function(src){
			var id = Date.now();
        	var modal = $(imageTmpl);
			modal.attr('id', id);
			modal.find('img').attr("src", src);
			
            var ImageModel = bootstrap.Modal.getOrCreateInstance(modal);
            ImageModel.toggle();
		}
	}
}();