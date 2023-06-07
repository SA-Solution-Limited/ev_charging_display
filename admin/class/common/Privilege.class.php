<?php
class Privilege {
	public static $REGISTRAION_PRIV = array(
		"REGISTRATION_FORM_READ" => "閲讀申請",
		"REGISTRATION_FORM_WRITE" => "編輯申請",
		"REGISTRATION_FORM_STATUS"  => "更改申請狀態",
			
		"REGISTRATION_APPROVED" => "接納申請",
		"REGISTRATION_DENIED" => "輪候名單",
			
		"REGISTRATION_DESIGN_TIME" => "時間管理",
		"REGISTRATION_ASSIGN_TIME" => "面試準備",
		"REGISTRATION_INTERVIEW" => "面試評分",
		"REGISTRATION_INTERVIEW_WRITE" => "更新面試評分",
		"REGISTRATION_INTERVIEW_RESULT" => "更改評分註項",
		"REGISTRATION_INTERVIEW_EMAIL" => "寄送不取錄電郵",
		"REGISTRATION_ADMISSION" => "收生準備",
	);
	
	public static $STUDENT_PRIV = array(
			"STUDENT_NEW_READ" => "來年新生",
			"STUDENT_NEW_WRITE" => "修改來年新生",
			
			"STUDENT_CURRENT_READ" => "現在就讀學生",
			"STUDENT_CURRENT_WRITE" => "修改現在就讀學生",
			
			"STUDENT_GRADUATE_READ" => "畢業生",
			"STUDENT_GRADUATE_WRITE" => "修改畢業生",
			
			"STUDENT_ALL_READ" => "所有學生",
			"STUDENT_ALL_WRITE" => "修改所有學生",
			
			"STUDENT_PARENT_DETAILS_READ" => "家長資訊",
			"STUDENT_PARENT_DETAILS_WRITE" => "家長資訊",
	);
	
	public static $CIRCULAR_PRIV = array(
			// student/circular
			"STUDENT_CIRCULAR_READ" => "通告",
			"STUDENT_CIRCULAR_WRITE" => "修改通告",
			"STUDENT_CIRCULAR_RECIPIENT" => "修改通告收件人",
			"STUDENT_CIRCULAR_REPORT" => "通告報表",
	);
	
	public static $LEAVE_PRIV = array(
			// student/leave
			"STUDENT_LEAVE_READ" => "學生請假記錄",
			"STUDENT_LEAVE_DELETE" => "刪除學生請假記錄",
	);
	
	public static $PARENT_MESS_PRIV = array(
			// student/parent_message
			"STUDENT_PARENT_MESSAGE" => "家長通訊",
	);
	
	public static $SPECIAL_EVENT_PRIV = array(
			// student/specialEvent
			"STUDENT_SPEICAL_EVENT_READ" => "特別活動項目",
			"STUDENT_SPEICAL_EVENT_WRITE" => "修改特別活動項目",
	);
	
	public static $SUTDENT_FILE_PRIV = array(
			// student/file
			"STUDENT_FILE_READ" => "學生檔案",
			"STUDENT_FILE_WRITE" => "修改學生檔案",
			"STUDENT_FILE_RECIPIENT" => "學生檔案收件人",
	);
	
	public static $EVAL_SETTING_PRIV = array(
			//Evaluation
			"EVAL_THEME_SETTING_READ" => "主題設定",
			"EVAL_THEME_SETTING_WRITE" => "修改主題設定",

			"EVAL_PROGRESS_SETTING_READ" => "成績表設定",
			"EVAL_PROGRESS_SETTING_WRITE" => "修改成績表設定",		

			"EVAL_THEME_CHART" => "評估圖表",
				
			"EVAL_PROGRESS_REMARK" => "成績表備註",
	);
	
	public static $EVAL_PRIV = array(
			"EVAL_THEME_READ" => "主題評分",
			"EVAL_THEME_WRITE" => "修改主題評分",
			"EVAL_THEME_EXPORT" => "匯出主題評分",
			"EVAL_THEME_UNLOCK" => "解除鎖定主題評分",
			"EVAL_THEME_SUPER" => "無限制修改主題評分",
				
			"EVAL_PROGRESS_READ" => "成績表評分",
			"EVAL_PROGRESS_WRITE" => "修改成績表評分",
			"EVAL_PROGRESS_EXPORT" => "匯出成績表評分",
			"EVAL_PROGRESS_UNLOCK" => "解除鎖定成績表評分",
			"EVAL_PROGRESS_SUPER" => "無限制修改成績表評分",
			
			"EVAL_OBSERVATION_READ" => "觀察記錄評分",
			"EVAL_OBSERVATION_WRITE" => "修改觀察記錄評分",
			"EVAL_OBSERVATION_EXPORT" => "匯出觀察記錄評分",
			"EVAL_OBSERVATION_UNLOCK" => "解除鎖定觀察記錄評分",
			"EVAL_OBSERVATION_SUPER" => "無限制修改觀察記錄評分",
				
			"EVAL_DEVELOP_READ" => "發展報告評分",
			"EVAL_DEVELOP_WRITE" => "修改發展報告評分",
			"EVAL_DEVELOP_EXPORT" => "匯出發展報告評分",
			"EVAL_DEVELOP_SUPER" => "無限制修改發展報告評分",
	);
	
	public static $SETTINT_PRIV = array(
			"SYS_SETTING" => "系統設定",
	);
	
	public static $STAFF_GENERAL_PRIV = array(
		"STAFF_INFO" => "教職員資料",
		"STAFF_REPORT" => "教職員報表",
		"STAFF_INFO_SETTING" => "職位/培訓設定",
		"STAFF_ROLE_SETTING" => "用戶登入權限設定",
		//"STAFF_ROLE_DETAILS_SETTING" => "登入權限詳細設定" unchageable
	);
	
	public static $STAFF_PAYROLL_PRIV = array(
			"STAFF_PAYROLE_PREPARE" => "出糧準備",
			"STAFF_PAYROLE_CALCULATION" => "出糧計算",
			"STAFF_PAYROLE_HISTORY" => "員工薪酬歷史",
			"STAFF_PAYROLE_REPORT" => "年度報表",
			"STAFF_PAYROLE_SETTING" => "出糧設定",
	);
	
	public static $STAFF_ATTEND_PRIV = array(
			"STAFF_ATTENDANCE_MANAGE" => "更表管理",
			"STAFF_ATTENDANCE_SHIFT" => "員工更表",
			"STAFF_ATTENDANCE_SHIFT_ROSTER" => "獨立員工更表",
			"STAFF_ATTENDANCE_SHIFT_SWAP" => "調更",

			"STAFF_ATTENDANCE_HISTORY" => "考勤紀錄",
			"STAFF_ATTENDANCE_LEAVE" => "預定假期 / 剩餘假期",
			
			"STAFF_ATTENDANCE_LATE_LEAVE" => "遲到/早退紀錄",
			"STAFF_ATTENDANCE_SPECIAL_EVENT" => "突發事項管理",
			"STAFF_ATTENDANCE_SETTING" => "更表設定", 
	);
	
	public static $REPORT_PRIV = array(
			"REPORTS_EXPORTS" => "學費記錄",
			"EXPORT_REPORT" => "查詢",
			"EXPORT_REPORT_PERSONAL" => "查詢個人資料",
			"EXPORT_REPORT_CONTRACT" => "匯出職員合約",
			"EXPORT_REPORT_CONTRACT_SETTING" => "匯出職員合約設定",
	);
	
	public static $WEB_SETTING_PRIV = array(
		"WEB_SETTING" => "網頁設定",
		"WEB_NEWS_VIEW" => "最新消息",
		"WEB_CALENDAR_VIEW" => "假期表",
		"WEB_DINING_MENU_VIEW" => "每周餐單",
		"WEB_ALBUM_VIEW" => "相簿",
		"WEB_CONTENT_VIEW" => "其他內容",
	);
	
	public static $GROUP_PRIV = array(		
		"STDUENT_GROUP" => "組別管理",
	);
	
	public static $PTA_PRIV = array(
			"PTA_MANAGE" => "PTA管理",
	);
	
	public static $FUNC = array(
			// Registration
			"REGISTRATION_FORM_READ",
			"REGISTRATION_FORM_WRITE",
			"REGISTRATION_FORM_STATUS",
			
			"REGISTRATION_APPROVED",
			"REGISTRATION_DENIED",
			
			"REGISTRATION_DESIGN_TIME",
			"REGISTRATION_ASSIGN_TIME",
			"REGISTRATION_INTERVIEW",
			"REGISTRATION_INTERVIEW_WRITE",
			"REGISTRATION_INTERVIEW_RESULT",
			"REGISTRATION_INTERVIEW_EMAIL",
			"REGISTRATION_ADMISSION",
				
			"STUDENT_NEW_READ",
			"STUDENT_NEW_WRITE",
			
			"STUDENT_CURRENT_READ",
			"STUDENT_CURRENT_WRITE",
			
			"STUDENT_PARENT_DETAILS_READ",
			"STUDENT_PARENT_DETAILS_WRITE",
			
			"STUDENT_GRADUATE_READ",
			"STUDENT_GRADUATE_WRITE",
			
			"STUDENT_ALL_READ",
			"STUDENT_ALL_WRITE",
			
			"STDUENT_GROUP",
			
			// student/circular
			"STUDENT_CIRCULAR_READ",
			"STUDENT_CIRCULAR_WRITE",
			"STUDENT_CIRCULAR_RECIPIENT",
			"STUDENT_CIRCULAR_REPORT",
			
			// student/leave
			"STUDENT_LEAVE_READ",
			"STUDENT_LEAVE_DELETE",
			
			// student/parent_message
			"STUDENT_PARENT_MESSAGE",
			
			// student/specialEvent
			"STUDENT_SPEICAL_EVENT_READ",
			"STUDENT_SPEICAL_EVENT_WRITE",
			
			"STUDENT_FILE_READ",
			"STUDENT_FILE_WRITE",
			"STUDENT_FILE_RECIPIENT",
			
			"EXPORT_REPORT",
			"EXPORT_REPORT_PERSONAL",
			"EXPORT_REPORT_CONTRACT",
			"EXPORT_REPORT_CONTRACT_SETTING",
			
			//Evaluation
			"EVAL_THEME_SETTING_READ",
			"EVAL_THEME_SETTING_WRITE",

			"EVAL_PROGRESS_SETTING_READ",
			"EVAL_PROGRESS_SETTING_WRITE",

			"EVAL_THEME_READ",
			"EVAL_THEME_WRITE",
			"EVAL_THEME_EXPORT",
			"EVAL_THEME_UNLOCK",
			"EVAL_THEME_SUPER",
			
			"EVAL_PROGRESS_READ",
			"EVAL_PROGRESS_WRITE",
			"EVAL_PROGRESS_EXPORT",
			"EVAL_PROGRESS_UNLOCK",
			"EVAL_PROGRESS_SUPER",

			"EVAL_OBSERVATION_READ",
			"EVAL_OBSERVATION_WRITE",
			"EVAL_OBSERVATION_EXPORT",
			"EVAL_OBSERVATION_UNLOCK",
			"EVAL_OBSERVATION_SUPER",
			
			"EVAL_DEVELOP_READ",
			"EVAL_DEVELOP_WRITE",
			"EVAL_DEVELOP_EXPORT",
			"EVAL_DEVELOP_SUPER",
			
			"EVAL_THEME_CHART",
			
			"EVAL_PROGRESS_REMARK",
			
			//System Setting
			"SYS_SETTING",
			
			//staff manage
			"STAFF_INFO",
			"STAFF_REPORT",
			"STAFF_INFO_SETTING",
			"STAFF_ROLE_SETTING",
			"STAFF_ROLE_DETAILS_SETTING",
			
			"STAFF_PAYROLE_PREPARE",
			"STAFF_PAYROLE_CALCULATION",
			"STAFF_PAYROLE_HISTORY",
			"STAFF_PAYROLE_REPORT",
			"STAFF_PAYROLE_SETTING",
			
			"STAFF_ATTENDANCE_MANAGE",
			"STAFF_ATTENDANCE_SHIFT",
			"STAFF_ATTENDANCE_SHIFT_ROSTER",
			"STAFF_ATTENDANCE_SHIFT_SWAP",

			"STAFF_ATTENDANCE_HISTORY",
			"STAFF_ATTENDANCE_LEAVE",
			
			"STAFF_ATTENDANCE_LATE_LEAVE",
			"STAFF_ATTENDANCE_SPECIAL_EVENT",
			"STAFF_ATTENDANCE_SETTING",
			
			"REPORTS_EXPORTS",
			
			"WEB_SETTING",
			"WEB_NEWS_VIEW",
			"WEB_CALENDAR_VIEW",
			"WEB_DINING_MENU_VIEW",
			"WEB_ALBUM_VIEW",
			"WEB_CONTENT_VIEW",
			
			"PTA_MANAGE",
	);
	
	public static function Render($element, $right) {
		echo self::HasRight($right) ? $element : '';
	}
	
	public static function HasRight($right) {
		$myRights = self::GetMyAccessRights();
		return isset($myRights) && is_array($myRights) && in_array($right, $myRights);
	}
	
	public static function GetMyAccessRights() {
		$model = a4p::model('RequestVar');
		if (!isset($model->accessRights)) {
			$sessionVar = a4p::model('SessionVar');
			$username = $sessionVar->username;
			
			require_once 'service/UserService.class.php';
			$service = new UserService();
			$model->accessRights = $service->getUserAccessRights($username);
		}
		return $model->accessRights;
	}
}