<?php

require_once "entity/Media.class.php";
class CMSService
{
    /**
     * 
     * @return Media|null
     */
    public function findById($id = 0, $isCreate = false){
        
        $entity = Media::findById($id);
        if(!isset($entity) && $isCreate){
            $entity = new Media();
        }
        return $entity;
    }

    /**
     * 
     * @return array|null
     */
    public function getActiveSlideShowMedias(){
        return Media::findAll("mediaFor = 'client_slide_show' and avalibleFrom < now() and DATE_ADD(avalibleTo, INTERVAL 1 DAY) > now() and isPublish = 1");
    }

    public function query(&$noOfRecord, &$filteredRecord, CMSDatabaseRequestModel $filter){
        $query = db::select();
        $from = db::join ( "media" );
        $query->from($from);
        $query->select("count(*)");
        $noOfRecord = $query->fetchOneRow()[0];

        $param = array();
        if(isset($filter->filename) && strlen($filter->filename) > 0){
            $query->where("displayName like :displayname");
            $param['displayname'] = db::like($filter->filename);
        }
        if(isset($filter->published)){
            $query->where("isPublish = 1");
        }else{
            $query->where("isPublish = 0");
        }
        $filteredRecord = $query->fetchOneRow($param)[0];

        $query->reset();
        $query->select("*");

        if (! Helper::isEmpty ( $filter->order[0]['column'] ) && ! Helper::isEmpty ($filter->order[0]['dir'] )) {
            $columnList = ['id', 'displayName', 'createAt', 'avalibleFrom', 'avalibleTo', 'isPublish'];
            $orderColumn = $columnList[$filter->order[0]['column']];
			$query->orderby( $orderColumn. " " .  $filter->order[0]['dir']);
		}
        $query->limit($filter->start, $filter->length);

        $results = $query->fetchAll($param);
        $entities = array();
        foreach($results as $r){
            $entity = new Media();
            Helper::bind($r, $entity);
            $entity->avalibleFrom = date_format(new DateTime($entity->avalibleFrom), "Y-m-d");
            $entity->avalibleTo = date_format(new DateTime($entity->avalibleTo), "Y-m-d");
            $entities[] = $entity;
        }
        return $entities;
    }
}
?>