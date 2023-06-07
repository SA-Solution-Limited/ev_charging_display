<?php
require_once('../a4plite/framework.inc.php');
require_once('service/PushMessageService.class.php');
require_once('service/CircularService.class.php');
require_once('common/SPNConstant.class.php');

$logger = Logger::getLogger("PushMessageLogFileAppender");

$logger->info("===== PushMessageJob START =====");
try {
	db::beginTransaction();
	
	$query = db::select( "pmd.id as id" )
			->select ( "pmd.login_id as parentId" )
			->select ( "pmd.device_token as deviceToken" )
			->select ( "pmd.device_type as deviceType" )
			->select ( "pm.message_content as message" )
			->from ( db::join ( "push_message_detail as pmd" )
					->innerjoin ( "push_message as pm" )->on ( "pm.id = pmd.push_id and pmd.is_send = 0" ) )
					->orderby( "pmd.login_id for update" );
	$records = $query->fetchAll ();
	$count = count ( $records );
	$logger->info("Retrieved $count push messages to send");
	
	$pushMessageService = new PushMessageService();
	$circularService = new CircularService();
	$parentId = "";
	$success = 0;
	for ($i = 0; $i < $count; $i++) {
		try {
			$row = $records[$i];
			
			$badge = 0;
			if ($parentId != $row["parentId"]) {
				$badge = $circularService->getUnreadCircularCount($parentId);
			}
			$parentId = $row["parentId"];
			$logger->info ( sprintf ( "Sending push message [id=%d]:\nDevice: %s\nType: %s\nMessage:%s\nBadge:%d",
					$row ["id"], $row ["deviceToken"], $row ["deviceType"] == SPNConstant::DEVICE_IOS ? "iOS" : "Android", $row ["message"], $badge ) );
			
			if ($row ["deviceType"] == SPNConstant::DEVICE_IOS) {
 				$pushMessageService->sendIOSPushMessage ( $row ["deviceToken"], $badge, $row ["message"] );
			} else {
				$pushMessageService->sendAndroidPushMessage ( $row ["deviceToken"], $row ["message"] );
			}
			db::executeNonQuery ( "update push_message_detail set is_send = :isSend, send_dt = :sendDt where id = :id", array (
					":isSend" => 1,
					":sendDt" => date ( "Y-m-d H:i:s" ),
					":id" => $row ["id"] 
			) );
			$success++;
			$logger->info ( sprintf ( "Send push message [id=%d] success\n", $row ["id"] ) );
		} catch (Exception $e) {
			$logger->info(sprintf ( "Send push message [id=%d] fail\n", $row ["id"]), $e);
		}
	}
	db::commit();
	
	$logger->info("Sent $success push messages");
	echo "Sent $success push messages";
} catch (Exception $e) {
	db::rollback();
	$logger->error("!!!!! PushMessageJob failed !!!!!", $e);
}
$logger->info("===== PushMessageJob END =====");