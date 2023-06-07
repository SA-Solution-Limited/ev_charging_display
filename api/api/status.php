<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 * 
 * @var array|null $params
 */

require_once('includes/class/class.ajaxform.php');

class ChargingStatus extends AjaxForm {

	function __construct($params = null) {
		session_write_close();

		$count = 0;
		$maxCount = 20;
		do {
			$simulator = $this->fetchStatus();
			usleep(250 * 1000);
		} while (++$count < $maxCount && HttpHelper::getGetParam('status') == $simulator->status && HttpHelper::getGetParam('currentBatteryLevel') == $simulator->currentBatteryLevel);

		if ($simulator->status == 'charging' || $simulator->status == 'charging_completed') {
			$simulator->plate = 'AB 1234';
			$simulator->energyConsumption = ($simulator->currentBatteryLevel - $simulator->initialBatteryLevel) * 1.25;
		} else if ($simulator->status == 'warning') {
			$simulator->warning = array(
				'message' => '集電弓充電警告',
				'code' => '05',
			);
		}
		
		$this->response(true, null, array(
			'data' => $simulator,
		));
	}

	protected function fetchStatus() {
		$file = 'mongo/'.md5('ev-charging-simulator').'.json';
		if (!is_dir(dirname($file))) {
			FileSystemHelper::mkdir(dirname($file), 0775, true);
		}
		if (is_file($file)) {
			$simulator = json_decode(file_get_contents($file));
		} else {
			$simulator = (object)array(
				'status' => 'vacant',
				'initialBatteryLevel' => 10,
				'currentBatteryLevel' => 10
			);
			file_put_contents($file, json_encode($simulator));
		}
		
		if ($simulator->status != 'charging') {
			$simulator->batteryLevel = null;
		}

		return($simulator);
	}
}

new ChargingStatus($params);
?>
