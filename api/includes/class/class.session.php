<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('class.db.php');

class Session implements SessionHandlerInterface {
		
	public function open($path, $name) {
		return(true);
	}
	
	public function close() {
		return(true);
	}
	
	public function read($id) {
		$rs = Db::query('SELECT [decrypt:`ssnData`] AS "ssnData" FROM [table:session] WHERE `ssnId` = :ssnId', array(
			':ssnId'  => $id,
		));
		return($rs !== false && count($rs) ? $rs[0]['ssnData'] : '');
	}
	
	public function write($id, $data) {
		$result = Db::query('REPLACE INTO [table:session] VALUES (:ssnId, :ssnAccess, [encrypt::ssnData])', array(
			':ssnId'     => $id,
			':ssnAccess' => DateHelper::getUtcTimestamp(),
			':ssnData'   => $data,
		));
		return($result > 0);
	}
	
	public function destroy($id) {
		$result = Db::query('DELETE FROM [table:session] WHERE `ssnId` = :ssnId', array(
			':ssnId' => $id,
		));
		return($result > 0);
	}
	
	public function gc($maxlifetime) {
		$row = Db::query('DELETE FROM [table:session] WHERE `ssnAccess` < :ssnAccess', array(
			':ssnAccess' => DateHelper::getUtcTimestamp() - $maxlifetime,
		));
		return(true);
	}
}
?>
