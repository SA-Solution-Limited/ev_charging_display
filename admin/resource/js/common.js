var CustomeModals = function () {
	
	var noticeTmpl =
		'<div class="modal fade modal-sm" tabindex="-1" style="display: none;" aria-hidden="true">' +
            '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
					'</div>' +
					'<div class="modal-body">' +
					'</div>' +
					'<div class="modal-footer">' +
					'</div>' +
				'</div>' +
			'</div>'+
		'</div>';
	
	var formTmpl = 
		'<div class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">>' +
		'<div class="modal-dialog modal-xl" >' +
			
		'</div>'+
	'</div>';

	return {
        //main function to initiate the module
        init: function () {
            
            // ajax-modal
            $('[data-toggle="ajax-modal"]').on('click', function (e) {
            	e.preventDefault();
            	
                $('body').modalmanager('loading');
                var target = $(this).data('target');
                var onshow = $(this).data('onshow');
                var onclose = $(this).data('onclose');
                var modal = $($(this).attr('href'));
                setTimeout(function () {
                	modal.load(target, '', function () {
                		if (typeof(onshow) != 'undefined' && typeof(eval(onshow)) == 'function') {
                			eval(onshow+'(this)');
                		}
                		if (typeof(onclose) != 'undefined' && typeof(eval(onclose)) == 'function') {
                			modal.on('hidden.bs.modal', function() {
                				eval(onclose+'()');
                			});
                		}
                		modal.modal();
                		App.updateUniform();
                		FormComponents.select2(modal.find('.select2'));
                    });
                }, 10);
            });
        },
        scrollableNotice: function(message, header, footer, callback) {
        	var modal = $(noticeTmpl);
        	var $header = $('<div class="bootbox-header" ></div>');
        	$header.html(header);
        	var $content = $('<div class="bootbox-body" ></div>');
        	$content.html(message);
        	var $footer = $('<div class="bootbox-footer" ></div>');
        	$footer.html(footer + '<button type="button" data-dismiss="modal" class="btn blue">OK</button>');
        	modal.find('.modal-body').append($content);
        	modal.find('.modal-header').append($header);
        	modal.find('.modal-footer').append($footer);
        	if (typeof(callback) != 'undefined' && typeof(eval(callback)) == 'function') {
        		modal.find('button[data-dismiss]').click(function(e) {
        			callback.call(this);
        		});
        	}
        	modal.modal();
        },
        form: function(modalContent, okCallback, cancelCallback){
        	var modal = $(formTmpl);
        	console.log(modal);
        	var $content = $('<div class="modal-content"></div>');
        	$content.html(modalContent);
        	modal.find('.modal-dialog').append($content);
        	console.log(modalContent);
        	
        	if (typeof(okCallback) != 'undefined' && typeof(eval(okCallback)) == 'function') {
        		modal.find('button[data-submit]').click(function(e) {
        			e.preventDefault();
        			okCallback.call(this);
        		});
        	}
        	
        	if (typeof(cancelCallback) != 'undefined' && typeof(eval(cancelCallback)) == 'function') {
        		modal.find('button[data-dismiss]').click(function(e) {
        			cancelCallback.call(this);
        		});
        	}
        	modal.modal();
        },
    };
} ();

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
						'<button type="button" data-bs-dismiss="modal" class="btn btn-info">Confirm</button>' +
					'</div>' +
				'</div>' +
			'</div>'
		'</div>';
	
	var confirmTmpl =
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
						'<button type="button" data-bs-dismiss="modal" data-confirm class="btn btn-info">Confirm</button>' +
						'<button type="button" data-bs-dismiss="modal" class="btn btn-danger pull-left">Cancel</button>' +
					'</div>' +
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
        }
	}
}();