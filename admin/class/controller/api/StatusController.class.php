<?php
require_once "controller/AbstractBaseController.class.php";
require_once "entity/ChargingStatus.class.php";

/**
 * @ajaxenable
 */
class StatusController extends AbstractBaseController {
    private $service;

    public function __construct()
    {
        parent::__construct();
    }

    public function getStatus(){
        $status = ChargingStatus::findById(1);
        return $this->json(array("success"=>true,"message"=>null, "data"=>$status));
    }

    public function updateStatus(){
        $status = ChargingStatus::findById(1);
        Helper::bind($_REQUEST, $status);
        $status->SaveOrUpdate();
    }
}