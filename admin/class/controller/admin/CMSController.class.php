<?php

use PSpell\Config;

require_once('controller/AbstractAdminController.class.php');
require_once('service/CMSService.class.php');

class CMSController extends AbstractAdminController
{

	private $service;
	public function __construct()
	{
		parent::__construct();
		$this->service = new CMSService();
	}
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
		$this->env ['scripts'] .= <<<js
		function confirmDelete(elem){
			var id = $(elem).data("id");
			var displayName = $(elem).data("displayName");
			Boxes.confirm('Confirm Delete '+displayName+'?', function(){window.location = '/CMS/delete?id='+id})
		}
js;
		$this->env ['init'] .= <<<js
			var datatable = $('#datatable').DataTable({
        		serverSide: true,
				ajax: {
					url: "/CMS/query",
					type: "POST",
					data: function ( d ) {
						return $.extend( {}, d, getFormData($("#filter")) );
					},
				},
				columnDefs: [
					{ orderable: false, targets: 0 }
				],
				columns: [
					{ data: function(data){
						var actions = [];
						actions.push('<button class="btn btn-info" data-imageModal="/api/media/getMediaFile?id='+data.id+'"><i class="menu-icon tf-icons bx bx-search"></i></button>')
						actions.push('<a class="btn btn-info" href="/CMS/edit?id='+data.id+'"><i class="menu-icon tf-icons bx bx-edit"></i></a>')
						actions.push('<button class="btn btn-danger" data-trigger="confirmDelete" data-id="'+data.id+'" data-displayName="'+data.displayName+'"><i class="menu-icon tf-icons bx bx-trash"></i></button>')
						return actions.join('');
					}},
					{ data: "displayName" },
					{ data: "createAt" },
					{ data: "avalibleFrom" },
					{ data: "avalibleTo" },
					{ data: function(data){
						if(data.isPublish == 1){
							return '<i class="menu-icon tf-icons bx bx-check"></i>'
						}else{
							return '<i class="menu-icon tf-icons bx bx-cross"></i>'
						}
					} },
				],
				order: [[2, 'desc']],

			});

			$("#filter").on("change", "input", function(){
				datatable.ajax.reload();
			})
			$("#filter").on("keyup", "input", function(){
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
js;
		return($this->view('admin/cms/index.php'));
	}

	public function query(){
		require_once 'model/CMS/CMSDatabaseRequestModel.class.php';
		require_once 'model/DataTableModel.class.php';
		$request = new CMSDatabaseRequestModel();
		Helper::bind($_REQUEST, $request);

		$model = new DataTableModel();
		$noOfRecord = 0;
		$filteredRecord = 0;
		$result = $this->service->query($noOfRecord, $filteredRecord, $request);
		$model->recordsTotal = $noOfRecord;
		$model->recordsFiltered = $filteredRecord;
		$model->data = $result;
		$model->draw = $request->draw +1;
		return $this->json($model );
	}

	public function edit(){
		$media = $this->service->findById($_REQUEST["id"] ?? null, true);
		a4p::assign("media", $media);

		if(isset($_POST['id'])){
			Helper::bind($_REQUEST, $media);
			if(isset($_POST['published'])){
				$media->isPublish = true;
			}else{
				$media->isPublish = false;
			}
			if (isset($_FILES['media']) && !empty($_FILES['media']['tmp_name'])) {
				$mediaFile = $_FILES['media'];
				if (!file_exists(Constant::$MediaUploadedPath)) {
					mkdir(Constant::$MediaUploadedPath, 0777, true);
					file_put_contents(Constant::$MediaUploadedPath."/.htaccess", 'Options -Indexes');
				}
				$extension = explode(".", $mediaFile['name']);
				$saveFileName = uniqid().".".end($extension);
				$savingPath = Constant::$MediaUploadedPath.$saveFileName;
				move_uploaded_file($mediaFile['tmp_name'] , $savingPath );
				$media->fileType = $mediaFile['type'];
				$media->fileSize = $mediaFile['size'];
				$media->physicalPath = $savingPath;
				$media->displayName = $mediaFile['name'];
				$media->mediaFor = "client_slide_show";
				$media->createAt = date_format(new DateTime(), "Y-m-d H:i:s");
			}
			$media->SaveOrUpdate();
		}

		return($this->view('admin/cms/edit.php'));
	}

	public function delete(){
		if(!isset($_REQUEST['id'])){
            return $this->redirect("/CMS");
        }else{
			$media = $this->service->findById($_REQUEST["id"]);
			if(isset($media)){
				if(file_exists($media->physicalPath)){
					unlink($media->physicalPath);
				}
				$media->Delete();
			}
			return $this->redirect("/CMS");
		}
	}

}
