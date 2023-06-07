<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('includes/action.export.php');
$export = new Export();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HTML Exporter | Cruzium Digital Development Tools</title>
<link rel="shortcut icon" type="image/x-icon" href="https://cruzium.digital/favicon.ico" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="MobileOptimized" content="320">

<?php
HtmlHelper::includeCssFile('https://fonts.googleapis.com/css?family=Montserrat:200,400,700', array('id' => 'google-fonts'), false, true);
HtmlHelper::includeCssFile('https://cdn.cruzium.info/bootstrap/4.6.2/css/bootstrap.min.css', array('id' => 'bootstrap'), false, true);
HtmlHelper::includeCssFile('https://cdn.cruzium.info/bootstrap-extends/latest/bootstrap4-colors.min.css', array('id' => 'bootstrap-colors'), false, true);
?>
<style type="text/css">
/* base styles */
:root {
	--bs-primary:#FF6300;
	--bs-primary-darker:#000000;
	--bs-primary-shadow:rgba(255,99,0,.5);
	--bs-secondary-darker:#000000;
	--bs-secondary-shadow:rgba(255,99,0,.5);
	--bs-danger-darker:#000000;
}
body {
	font-family:Montserrat,Arial,Helvetica,sans-serif;
}
hr {
	border-top-color:#454545;
}
.btn {
	border-radius:0;
	font-weight:bold;
	text-transform:uppercase;
}
.btn:not(.btn-sm):not(.btn-lg) {
	padding:0.75rem 1.25rem;
}
.form-control {
	border-radius:0;
}
.form-control:focus {
	box-shadow:0 0 0 0.2rem var(--bs-primary-shadow);
	border-color:var(--bs-primary);
}
.input-group-text {
	border-radius:0;
}
.page-wrapper {
	padding-top:10px;
	padding-bottom:10px;
}
@media (prefers-color-scheme:dark) {
	body {
		background:#2A2A2E;
		color:#FFFFFF;
	}
}

/* custom styles */
pre {
	min-height:200px;
	margin:1rem 0;
	padding:10px 15px;
	border:1px solid #454545;
	color:inherit;
}
pre code {
	color:#CE9178;
}
</style>
</head>

<body>
<div class="page-wrapper">
	<div class="container">
		<h1 class="mb-4 text-center">HTML Exporter</h1>
		<pre><?php echo(implode("\n", $export->getMessages())); ?></pre>
		<p class="text-center"><a href="download.php" class="btn btn-primary">Download</a></p>
		<p class="small text-center">&copy; <?php echo(date('Y')); ?> Cruzium Digital. All Rights Reserved.</p>
	</div>
</div>
</body>
</html>
