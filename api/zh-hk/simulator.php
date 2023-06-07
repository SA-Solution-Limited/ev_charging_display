<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

$file = 'mongo/'.md5('ev-charging-simulator').'.json';
if (!is_dir(dirname($file))) {
	FileSystemHelper::mkdir(dirname($file), 0775, true);
}
if (is_file($file)) {
	$simulator = json_decode(file_get_contents($file));
} else {
	$simulator = (object)array(
		'status' => 'vacant',
		'initialBatteryLevel' => 10,
		'currentBatteryLevel' => 10
	);
	file_put_contents($file, json_encode($simulator));
}

if (HttpHelper::isPost()) {
	$simulator->status = HttpHelper::getPostParam('status');
	$simulator->initialBatteryLevel = HttpHelper::getPostParam('initialBatteryLevel');
	$simulator->currentBatteryLevel = HttpHelper::getPostParam('currentBatteryLevel');
	file_put_contents($file, json_encode($simulator));
	HttpHelper::ajaxResponse(true);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Simulator | EV Charging Display</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="MobileOptimized" content="320">

<?php
HtmlHelper::includeCssFile('https://fonts.googleapis.com/css?family=Montserrat:200,400,700', array('id' => 'google-fonts'), false, true);
HtmlHelper::includeCssFile('https://cdn.cruzium.info/bootstrap/4.6.2/css/bootstrap.min.css', array('id' => 'bootstrap'), false, true);
HtmlHelper::includeCssFile('https://cdn.cruzium.info/bootstrap-extends/latest/bootstrap4-colors.min.css', array('id' => 'bootstrap-colors'), false, true);
HtmlHelper::includeCssFile('https://cdn.cruzium.info/jquery.ui/1.12.1/jquery.ui.min.css', array('id' => 'jquery-ui'), false, true);
HtmlHelper::includeCssFile('https://cdn.cruzium.info/toastr/latest/toastr.min.css', array('id' => 'toastr'), false, true);
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

/* custom style */
.slider {
	margin-top:calc(0.375rem + 0.35em + 1px);
	margin-bottom:calc(0.375rem + 0.35em + 1px);
}
.slider-value {
	text-align:center;
}
</style>

<?php HtmlHelper::includeJsFile('https://cdn.cruzium.info/technetium.framework/latest/util/util.min.js'); ?>
</head>

<body>
<div class="page-wrapper">
	<div class="container">
		<form name="simulator" action="" method="post">
			<h1 class="mb-4 text-center">EV Charging Simulator</h1>
			<div class="form-group row">
				<label class="col-form-label col-md-3">Status</label>
				<div class="col-md-9">
					<select name="status" class="form-control">
						<option value="vacant" <?php echo($simulator->status == 'vacant' ? 'selected' : ''); ?>>Vacant</option>
						<option value="entrance" <?php echo($simulator->status == 'entrance' ? 'selected' : ''); ?>>Vehicle Entering Charging Zone</option>
						<option value="charging" <?php echo($simulator->status == 'charging' ? 'selected' : ''); ?>>Charging</option>
						<option value="charging_completed" <?php echo($simulator->status == 'charging_completed' ? 'selected' : ''); ?>>Charging Completed</option>
						<option value="exit" <?php echo($simulator->status == 'exit' ? 'selected' : ''); ?>>Vehicle Exiting Charging Zone</option>
						<option value="warning" <?php echo($simulator->status == 'failure' ? 'selected' : ''); ?>>Warning</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">Initial Battery Level</label>
				<div class="col-md-9">
					<div class="input-group">
						<input type="number" name="initialBatteryLevel" value="<?php echo($simulator->initialBatteryLevel); ?>" class="form-control" />
						<div class="input-group-append"><span class="input-group-text">%</span></div>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">Current Battery Level</label>
				<div class="col-md-9">
					<input type="hidden" name="currentBatteryLevel" value="<?php echo($simulator->currentBatteryLevel); ?>" />
					<div class="slider"></div>
					<div class="slider-value"></div>
				</div>
			</div>
			<hr />
			<p class="small text-center">&copy; <?php echo(date('Y')); ?> Cruzium Digital. All Rights Reserved.</p>
		</div>
	</div>
</div>

<?php
HtmlHelper::includeJsFile('https://cdn.cruzium.info/jquery/3.4.1/jquery.min.js');
HtmlHelper::includeJsFile('https://cdn.cruzium.info/jquery.ui/1.12.1/jquery.ui.min.js');
HtmlHelper::includeJsFile('https://cdn.cruzium.info/jquery.ui.touch-punch/latest/jquery.ui.touch-punch.min.js');
HtmlHelper::includeJsFile('https://cdn.cruzium.info/toastr/latest/toastr.min.js');
HtmlHelper::includeJsFile('https://cdn.cruzium.info/form-essentials/latest/form/form.min.js');
?>
<script type="text/javascript">
window.addEventListener('load', function() {
	var form = new Form('simulator', {
		ajax: true,
		autoReset: false,
		displayMessage: false
	});

	var initSlider = function() {
		$('.slider').slider({
			value: parseInt($('[name=currentBatteryLevel]').val()),
			min: parseInt($('[name=initialBatteryLevel]').val()),
			max: 100,
			create: function(e, ui) {
				$('[name=currentBatteryLevel]').val($(this).slider('value'));
				$(e.target).next('.slider-value').text($(this).slider('value') + '%');
			},
			slide: function(e, ui) {
				$('[name=currentBatteryLevel]').val(ui.value);
				$(e.target).next('.slider-value').text(ui.value + '%');
			},
			stop: function(e, ui) {
				form.submit();
			}
		});
	};

	$(':input', form.$elem).on('change', function() {
		form.submit();
	});

	$('[name=initialBatteryLevel]').on('change', function() {
		$('.slider').slider('destroy');
		initSlider();
	});

	initSlider();
});
</script>
</body>
</html>
