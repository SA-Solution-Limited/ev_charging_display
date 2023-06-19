<?php

/** @table charging_status */
class ChargingStatus extends Entity
{
	/** @id id */
	public $id;

	/** @column status */
	public $status;

	/** @column initialBatteryLevel */
	public $initialBatteryLevel;

	/** @column currentBatteryLevel */
	public $currentBatteryLevel;

	/** @column batteryLevel */
	public $batteryLevel;
	
}
