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

    public function slideshow(){
        $medias = $this->service->getActiveSlideShowMedias();
        foreach($medias as $m){
            $returnModels[] = "/api/media/getMediaFile?id=".$m->id;
        }
        return $this->json(array("success"=>true,"message"=>null,"data"=>$returnModels));
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