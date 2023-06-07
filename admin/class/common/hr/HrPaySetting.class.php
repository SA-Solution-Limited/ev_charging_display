<?php
class HrPaySetting {
	const CAL_STATUS_PENDING = 0;
	const CAL_STATUS_RUNNING = 1;
	const CAL_STATUS_COMPLETE = 2;
	const CAL_STATUS_LOCKED = 3;
	
	const CAL_ITEM_STATUS_PENDING = 0;
	const CAL_ITEM_STATUS_RUNNING = 1;
	const CAL_ITEM_STATUS_COMPLETE = 2;
	const CAL_ITEM_STATUS_REPLIED = 3;
	
	const HOLIDAY_INTERNAL_CODE_LATE = "LATE";
	
	public static $calculationStatuses = array(
		self::CAL_STATUS_PENDING => "已排程",
		self::CAL_STATUS_RUNNING => "計算中",
		self::CAL_STATUS_COMPLETE => "完成計算",
		self::CAL_STATUS_LOCKED => "已鎖定"
	);
	
	public static $calculationLockStatuses = array(
		self::CAL_STATUS_PENDING => "已排程",
		self::CAL_STATUS_RUNNING => "計算中",
		self::CAL_STATUS_COMPLETE => "未鎖定",
		self::CAL_STATUS_LOCKED => "已鎖定"
	);
	
	public static $calculationItemStatuses = array(
		self::CAL_ITEM_STATUS_PENDING => "已排程",
		self::CAL_ITEM_STATUS_RUNNING => "計算中",
		self::CAL_ITEM_STATUS_COMPLETE => "完成計算",
		self::CAL_ITEM_STATUS_REPLIED => "已回覆"
	);
	
	public static $calculationItemRepliedStatuses = array(
		self::CAL_ITEM_STATUS_PENDING => "已排程",
		self::CAL_ITEM_STATUS_RUNNING => "計算中",
		self::CAL_ITEM_STATUS_COMPLETE => "未回覆",
		self::CAL_ITEM_STATUS_REPLIED => "已回覆"
	);
	
	public static $staffPositionToTaxEnglishName = array(
			1 => "Teacher",
			2 => "Clerk",
			3 => "Minor Staff",
			4 => "Cook",
			5 => "Minor Staff",
			6 => "Teacher",
			7 => "Teacher",
			8 => "Principal",
			9 => "Clerk",
			10 => "Clerk",
			11 => "Clerk",
			12 => "Clerk",
			13 => "Teacher",
			14 => "Teacher",
			15 => "Teacher",
			16 => "Teacher",
			17 => "Teacher"
	);
}