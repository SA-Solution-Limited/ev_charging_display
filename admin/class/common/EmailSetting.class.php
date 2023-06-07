<?php 

class EmailSetting {
	/* Production Setting */
	// email content settings
	//*
	public static $from = "taipo@spn.edu.hk";
	public static $from_name = "天主教聖保祿幼兒園(大圍)";
	
	// send email settings
	public static $host = "mail.spn.edu.hk";
	public static $port = 465;
	public static $ssl = true;
	public static $username = "taipo@spn.edu.hk";
	public static $password = "Wantau28";
	public static $cc = "spcn.reg@gmail.com";
	
	/*/ //Development Setting 
	// email content settings
	 

	public static $from = "spcn.info@gmail.com";
	public static $from_name = "天主教聖保祿幼兒園(大圍)";
	
	// send email settings
	public static $host = "smtp.gmail.com";
	public static $port = 465;
	public static $ssl = true;
	public static $username = "spcn.info@gmail.com";
	public static $password = "26561066";
	public static $cc = "spcn.reg@gmail.com";
	//*/
}