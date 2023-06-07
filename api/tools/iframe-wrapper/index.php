<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

$src = HttpHelper::getGetParam('src');
$width = HttpHelper::getGetParam('w') ? $_GET['w'] : 1920;
$height = HttpHelper::getGetParam('h') ? $_GET['h'] : 1080;
$isValidRequest = $src && $width >= 0 && $height >= 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Iframe Wrapper | Cruzium Digital Development Tools</title>

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
<?php if ($isValidRequest) : ?>
html,
body,
.page-wrapper {
	height:100%;
}
.container {
	width:100%;
	height:100%;
	max-width:none;
	overflow:hidden;
	position:relative;
	padding:0 10px;
}
iframe {
	position:absolute;
	top:50%;
	left:50%;
	background:#FFFFFF;
	visibility:hidden;
}
<?php endif; ?>
</style>
</head>

<body>
<div class="page-wrapper">
	<div class="container">
		<?php if ($isValidRequest) : ?>
		<iframe src="" width="<?php echo($width); ?>" height="<?php echo($height); ?>" frameborder="0"></iframe>
		<?php else : ?>
		<form action="" method="get">
			<h1 class="mb-4 text-center">iframe Wrapper</h1>
			<div class="form-group row">
				<label class="col-form-label col-md-3">URL</label>
				<div class="col-md-9">
					<input type="text" name="src" required class="form-control" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">Dimensions (pixels)</label>
				<div class="col-md-9">
					<div class="row">
						<div class="col">
							<div class="input-group">
								<div class="input-group-prepend"><span class="input-group-text">Width</span></div>
								<input type="number" name="w" min="1" placeholder="1920" class="form-control" />
							</div>
						</div>
						<div class="col-auto align-self-center p-0">&times;</div>
						<div class="col">
							<div class="input-group">
								<div class="input-group-prepend"><span class="input-group-text">Height</span></div>
								<input type="number" name="w" min="1" placeholder="1080" class="form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<p class="text-center">
				<button type="submit" class="btn btn-primary">Submit</button>
			</p>
			<p class="small text-center">&copy; <?php echo(date('Y')); ?> Cruzium Digital. All Rights Reserved.</p>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php if ($isValidRequest) : ?>
<?php HtmlHelper::includeJsFile('https://cdn.cruzium.info/jquery/3.4.1/jquery.min.js'); ?>
<script type="text/javascript" src=""></script>
<script type="text/javascript">
window.addEventListener('load', function() {
	var $container = $('.container');
	var $iframe = $('iframe');
	$(window).on('resize', function() {
		var scale = Math.min($container.width() / $iframe.width(), $container.height() / $iframe.height());
		$iframe.css({
			visibility: 'visible',
			transform: 'translate(-50%,-50%) scale(' + Math.min(scale, 1) + ')'
		});
	}).trigger('resize');
	setTimeout(function() {
		$iframe.one('load', function() {
			var _iframe = $(this).get(0).contentWindow.document;
			document.title = _iframe.title;
			var $link = $(_iframe.querySelector('link[type="image/x-icon"]'));
			if ($link.length) {
				$(document.createElement('link')).attr({
					rel: 'shortcut icon',
					type: 'image/x-icon',
					href: $link.attr('href')
				}).prependTo('head');
			}
		}).attr('src', '<?php echo($_GET['src']); ?>');
	}, 100);
});
</script>
<?php endif; ?>
</body>
</html>
