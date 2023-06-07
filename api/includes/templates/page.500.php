<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 * 
 * @var Site $this
 */

if (HttpHelper::isAjax()) {
	HttpHelper::ajaxResponse(false, 'Internal server error.');
}

$mailLink = 'mailto:it@cruzium.com';
$mailLink .= '?subject='.rawurlencode("{$errorCode} Server Error");
$mailLink .= '&body='.rawurlencode('URL: '.UrlHelper::getOrigin().$_SERVER['REQUEST_URI']."\nCode: {$errorCode}\nTime: ".date('Y-m-d H:i:s'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>500 <?php echo(ArrayHelper::getValue($locale, 'site_name') ? "| {$locale['site_name']}" : 'Internal Server Error'); ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo($this->origin.$this->urlBase) ?>favicon.ico" />

<link href="https://fonts.googleapis.com/css?family=Montserrat:200,400,700" rel="stylesheet">
<style type="text/css">
:root {
	--background-color:#FFFFFF;
	--foreground-color:#211B19;
}
*,
*:before,
*:after {
	-webkit-box-sizing:border-box;
	box-sizing:border-box;
	transition-duration:0.2s;
	transition-property:none;
}
html {
	font-size:14px;
}
body {
	padding:0;
	margin:0;
	background:var(--background-color);
	font-family:'Montserrat', sans-serif;
	color:var(--foreground-color);
}
a {
	color:inherit;
}
.page-wrapper {
	position:relative;
	height:100vh;
}
.page-wrapper > .container {
	width:100%;
	max-width:360px;
	position:absolute;
	left:50%;
	top:50%;
	line-height:1.4;
	text-align:center;
	transform:translate(-50%, -50%);
}
.title {
	position:relative;
	margin:0 auto 1.25rem;
	z-index:-1;
}
.title > h1 {
	margin:0;
	font-size:96px;
	font-weight:200;
	line-height:1;
	text-transform:uppercase;
}
.title > h2 {
	margin:0;
	padding:0 10px;
	font-size:16px;
	font-weight:400;
	text-transform:uppercase;
	display:inline-block;
}
.buttons > a {
	padding:0.5rem 1rem;
	background:#FF6300;
	font-size:1rem;
	font-weight:700;
	color:#FFFFFF;
	text-decoration:none;
	text-transform:uppercase;
	display:inline-block;
	transition-property:background-color, color;
}
.buttons > a:hover {
	color:#FF6300;
	background:#211B19;
}
.message {
	padding:0 10px;
}
.message strong {
	font-size:1.25em;
}
@media (min-width:480px) {
	.page-wrapper > .container {
		max-width:480px;
	}
	.title > h1 {
		font-size:148px;
	}
	.title > h2 {
		font-size:24px;
	}
}
@media (min-width:768px) {
	html {
		font-size:18px;
	}
	.page-wrapper > .container {
		max-width:768px;
	}
	.title > h1 {
		font-size:236px;
	}
	.title > h2 {
		position:absolute;
		bottom:0px;
		left:50%;
		padding-top:10px;
		padding-bottom:10px;
		background:var(--background-color);
		font-size:28px;
		white-space:nowrap;
		transform:translateX(-50%);
	}
}
@media (min-width:992px) {
	.page-wrapper > .container {
		max-width:840px;
	}
}
@media (prefers-color-scheme:dark) {
	:root {
		--background-color:#2A2A2E;
		--foreground-color:#FFFFFF;
	}
}
</style>

<?php require_once('com.tracking.php'); ?>
</head>
<body>
<div class="page-wrapper">
	<div class="container">
		<div class="title">
			<h1>Oops!</h1>
			<h2>500 - Something went wrong</h2>
		</div>
		<?php if ($errorCode) : ?>
		<p class="message">
			Please <a href="<?php echo($mailLink); ?>">contact our administrator</a> and quote the following code:<br />
			<strong><?php echo($errorCode); ?></strong>
		</p>
		<?php endif; ?>
	</div>
</div>
</body>
</html>
