<?php
class HrAttSetting {
	const ALLOW_TIME_START = "hr_att_allow_time_start";
	const ALLOW_TIME_END = "hr_att_allow_time_end";
	const SHIFT_WORKING_DAY = 1;
	const SHIFT_REST_DAY = 2;
	public static $shiftTypes = array(
		self::SHIFT_WORKING_DAY => '工作日',
		self::SHIFT_REST_DAY => '休息日'
	);
	
	const HOLIDAY_PUBLIC = 1;
	const HOLIDAY_SCHOOL = 2;
	public static $holidayTypes = array(
		self::HOLIDAY_PUBLIC => '公眾假期',
		self::HOLIDAY_SCHOOL => '持別日子'
	);
	
	const EVENT_WORKING_DAY = 1;
	const EVENT_REST_DAY = 2;
	public static $eventTypes = array(
		self::EVENT_WORKING_DAY => '工作',
		self::EVENT_REST_DAY => '休息'
	);
	
	const LEAVE_TWO_DAY = 1;
	const LEAVE_NO_PAY = 2;
	public static $leaveTypes = array(
		self::LEAVE_TWO_DAY => '2天年假',
		self::LEAVE_NO_PAY => '無薪假'
	);
	
	/**
	 * 遲到
	 * @var int
	 */
	const ATTENDANCE_EVENT_LATE = 1;
	/**
	 * 早退
	 * @var int
	 */
	const ATTENDANCE_EVENT_EARLY = 2;
	/**
	 * 外出
	 * @var int
	 */
	const ATTENDANCE_EVENT_OUT = 3;
	/**
	 * 缺席
	 * @var int
	 */
	const ATTENDANCE_EVENT_ABS = 4;
	/**
	 * 加班
	 * @var int
	 */
	const ATTENDANCE_EVENT_OT = 5;
	
	public static $attendanceEventTypes = array(
		self::ATTENDANCE_EVENT_LATE => "遲到",
		self::ATTENDANCE_EVENT_EARLY => "早退",
		self::ATTENDANCE_EVENT_OUT => "外出",
		self::ATTENDANCE_EVENT_ABS => "缺席",
		self::ATTENDANCE_EVENT_OT => "加班"
	);
}