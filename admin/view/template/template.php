<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/extlib/sneat/assets" data-template="vertical-menu-template">
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8" />
<title><?=$env['title']?></title>
	<?php /*<meta http-equiv="X-UA-Compatible" content="IE=8"> */?>
	<meta
	  name="viewport"
	  content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
	/>
	<meta content="" name="description" />
	<meta content="" name="author" />
	<meta name="MobileOptimized" content="320">
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link rel="stylesheet" type="text/css" href="/extlib/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />
	
	<!-- Icons -->
    <link rel="stylesheet" href="/extlib/sneat/assets/vendor/fonts/boxicons.css">
    <link rel="stylesheet" href="/extlib/sneat/assets/vendor/fonts/flag-icons.css">
	<link rel="stylesheet" type="text/css" href="/extlib/vendor/components/font-awesome/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/extlib/sneat/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/extlib/sneat/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/extlib/sneat/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/extlib/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/extlib/sneat/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="/extlib/sneat/assets/vendor/js/helpers.js"></script>
	<style type="text/css">
	.layout-menu-fixed .layout-navbar-full .layout-menu,
	.layout-menu-fixed-offcanvas .layout-navbar-full .layout-menu {
	top: 62px !important;
	}
	.layout-page {
	padding-top: 62px !important;
	}
	.content-wrapper {
	padding-bottom: 38.9375px !important;
	}</style>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="/extlib/sneat/assets/js/config.js"></script>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES --> 
<!-- END THEME STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<?=$env['styles']?>
<!-- END PAGE LEVEL STYLES -->
<link rel="shortcut icon" href="favicon.ico" />
<?php plugin::element("head", $env); ?>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="<?=$env['bodyclass']?>">
<div class="layout-wrapper layout-content-navbar  ">
  <div class="layout-container">
  <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" data-bg-class="bg-menu-theme" style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
  </aside>
  <div class="layout-page">
	<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
		<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0   d-xl-none ">
			<?php template::section("header"); ?>
		</div>
		<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
			<?php template::section("header"); ?>
		</div>
		<div class="navbar-search-wrapper search-input-wrapper  d-none">
			<?php template::section("header"); ?>
		</div>
	</div>
	<!-- BEGIN CONTAINER -->   
	<div class="content-wrapper">
		<div class="container-xxl flex-grow-1 container-p-y">
		<?php template::section("content"); ?>
		</div>

		<?php template::section("footer"); ?>
	</div>
	<!-- END CONTAINER -->
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
	<script type="text/javascript" src="/extlib/vendor/components/jquery/jquery.min.js"></script>  
	<script type="text/javascript" src="/extlib/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/extlib/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<?=$env['plugins']?>
	<!-- END PAGE LEVEL PLUGINS -->
	<?=$env['scripts']?>
	<script type="text/javascript">
		jQuery(document).ready(function() {    
		  
		   <?=$env['init']?>
		   
		});
	</script>
	<?php a4p::loadScript(); ?>
	<?php plugin::element("script", $env); ?>
	<!-- END JAVASCRIPTS -->
  </div>
  </div>
</div>
</body>
<!-- END BODY -->
</html>