<?php
require_once('controller/AbstractAdminController.class.php');

class CMSController extends AbstractAdminController
{
	/**
	 * Page load handler
	 * @param array $env Contains variables to bind to view
	 */
	protected function onPageLoad($param = null) {
		if (!isset($param[1])) {
			$param[1] = 'index';
		}

		if(method_exists($this, $param[1])){
			return $this->{$param[1]}();
		}else{
			return $this->index();
		}
	}
	
	public function index() {

		$this->env ['init'] .= <<<js

		$(document).ready(function () {
			var datatable = $('#datatable').DataTable({
        		serverSide: true,
				ajax: {
					url: "/CMS/query",
					type: "POST",
					data: function ( d ) {
						return $.extend( {}, d, getFormData($("#filter")) );
					},
					columns: [
						{ "orderable": false, name: "Action" },
						{ "orderable": true, name: "FileName" },
						{ "orderable": true, name: "CreateAt" },
						{ "orderable": true, name: "AvalibleFrom" },
						{ "orderable": true, name: "AvalibleTo" },
						{ "orderable": true, name: "Published" },
					],
				}

			});

			$("#filter").on("change", "input", function(){
				datatable.ajax.reload();
			})

			function getFormData(form){
				var unindexed_array = form.serializeArray();
				var indexed_array = {};
			
				$.map(unindexed_array, function(n, i){
					indexed_array[n['name']] = n['value'];
				});
			
				return indexed_array;
			}
		});

js;
		return($this->view('admin/cms/index.php'));
	}

	public function query(){
		require_once 'model/CMS/CMSDatabaseRequestModel.class.php';
		require_once 'model/DataTableModel.class.php';
		$request = new CMSDatabaseRequestModel();
		Helper::bind($_REQUEST, $request);

		$model = new DataTableModel();
		return $this->json($model );
	}

	public function edit(){

	}

}
