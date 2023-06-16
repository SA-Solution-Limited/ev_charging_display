<?php
require_once "controller/AbstractBaseController.class.php";
require_once "service/CMSService.class.php";
require_once "model/media/MediaListItemModel.class.php";

/**
 * @ajaxenable
 */
class MediaController extends AbstractBaseController {
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CMSService();
    }

    public function getMediaList(){
        $medias = $this->service->getActiveSlideShowMedias();
        $returnModels = array();
        foreach($medias as $m){
            $returnModel = new MediaListItemModel();
            Helper::bind($m, $returnModel);
            $returnModels[] = $returnModel;
        }
        return $this->json(array("result"=>"success", "slideshow"=>$returnModels));
    }   

    public function getMediaFile(){
        if(!isset($_REQUEST['id'])){
            http_response_code(404);
            die();
        }
        $id = $_REQUEST['id'];
        $media = $this->service->findById($id);

        if(!isset($media)){
            http_response_code(404);
            die();
        }

        $this->file($media->physicalPath, $media->fileType, $media->displayName);

    }
}