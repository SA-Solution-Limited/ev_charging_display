<?php
class SPNConfigurator implements LoggerConfigurator {
   
   public $configuration;
   
   function __construct(){
       $this->configuration = array (
				'RollingLogFileAppender' => 1,
				'ApiLogFileAppender' => 2,
		);
   }
   
   
   public function configure(LoggerHierarchy $hierarchy, $input = null) {
       // Use a different layout for the next appender
       $layout = new LoggerLayoutPattern();
       $layout->setConversionPattern("%n>>> %date{Y-m-d H:i:s,u} - {%c} [%-5p] %m");
       $layout->activateOptions();


       // Create an appender which logs to file
       $appFile = new LoggerAppenderRollingFile('default');
       $appFile->setFile(dirname(__FILE__).'/../logs/sys.log');
       $appFile->setAppend(true);
       $appFile->setThreshold('all');
       $appFile->setMaxBackupIndex(10);
       $appFile->setMaxFileSize("5MB");
       $appFile->setLayout($layout);
       $appFile->activateOptions();
       
       // Create an appender which logs to file
       $apiLogFile = new LoggerAppenderRollingFile('ApiLogFileAppender');
       $apiLogFile->setFile(dirname(__FILE__).'/../logs/api.log');
       $apiLogFile->setAppend(true);
       $apiLogFile->setThreshold('all');
       $apiLogFile->setMaxBackupIndex(10);
       $apiLogFile->setMaxFileSize("5MB");
       $apiLogFile->setLayout($layout);
       $apiLogFile->activateOptions();
       
       // Add specific appender to specific logger
       $root = $hierarchy->getRootLogger();
       
       $logger = $root->getLogger('ApiLogFileAppender');
       $logger->setAdditivity(false);
       $logger->addAppender($apiLogFile);
       
       // should be set at the last step
       $root->setAdditivity(true);
       $root->addAppender($appFile);
   }
}