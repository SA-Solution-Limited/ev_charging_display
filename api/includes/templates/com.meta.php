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
?>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo((isset($pageTitle) && $pageTitle ? $pageTitle.' | ' : '').$locale['site_name']); ?></title>
<meta http-equiv="content-language" content="<?php echo($this->locale); ?>" />

<meta name="description" content="<?php echo(isset($pageDesc) && $pageDesc ? $pageDesc : $locale['meta_desc']); ?>" />
<meta name="keywords" content="<?php echo(isset($pageKeyword) && $pageKeyword ? $pageKeyword : $locale['meta_keywords']); ?>" />
<meta name="robots" content="<?php echo(isset($pageRobots) && $pageRobots ? strtoupper($pageRobots) : 'INDEX,FOLLOW'); ?>" />

<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo($this->origin.$this->urlBase) ?>favicon.ico" />

<?php
/* Progressive Web App */
if (is_file('includes/templates/com.webapp.php')) {
	require_once('com.webapp.php');
}
?>

<?php
/* Open Graph */
if (is_file('includes/templates/com.opengraph.php')) {
	require_once('com.opengraph.php');
}
?>

<!-- Plugins StyleSheets -->
<?php
if ($fonts = $this->getSrvConfig('google-fonts')) {
	WebFontHelper::embedGoogleFonts($fonts, true);
}
if ($pid = $this->getSrvConfig('adobe-fonts', 'projectId')) {
	WebFontHelper::embedAdobeFonts($pid, true);
}
HtmlHelper::includeCssFile($this->urlBase.'library/cdn-plugins/normalize.css/normalize.css', array('id' => 'normalize'));
HtmlHelper::includeCssFile($this->urlBase.'library/cdn-plugins/bootstrap/css/bootstrap.min.css', array('id' => 'bootstrap'));
HtmlHelper::includeCssFile($this->urlBase.'library/cdn-plugins/bootstrap-extends/bootstrap4-colors.min.css', array('id' => 'botstrap-colors'));
if (isset($pagePluginStyles)) HtmlHelper::includeCssFile($pagePluginStyles, array(), true, true);
?>

<!-- Theme StyleSheets -->
<?php
HtmlHelper::includeCssFile($this->urlBase.'library/css/plugins.css', array('id' => 'plugins'), true);
HtmlHelper::includeCssFile($this->urlBase.'library/css/font.css', array('id' => 'font'), true);
HtmlHelper::includeCssFile($this->urlBase.'library/css/framework.css', array('id' => 'framework'), true);
HtmlHelper::includeCssFile($this->urlBase.'library/css/style.css', array('id' => 'style'), true);
HtmlHelper::includeCssFile($this->urlBase."library/css/style.{$this->locale}.css", array('id' => "style-{$this->locale}"), true);
HtmlHelper::includeCssFile($this->urlBase.'library/css/style-responsive.css', array('id' => 'style-responsive'), true);
if (isset($pageLevelStyles)) HtmlHelper::includeCssFile($pageLevelStyles, array(), true);
?>

<!-- Technetium Framework -->
<?php
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/technetium.framework/util/util.min.js', array(), false, true);
?>
<script type="text/javascript">
var locale = '<?php echo($this->locale); ?>';
var urlBase = '<?php echo($this->urlBase); ?>';
var urlLocale = '<?php echo($this->urlLocaleBase); ?>';
var urlApi = '<?php echo($this->urlBase); ?>api/';
</script>

<!-- AngularJS -->
<?php
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/angularjs/angular.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/angularjs/angular-resource.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/angularjs/angular-sanitize.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/angular-translate/angular-translate.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/angular-translate/angular-translate-loader-static-files.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/js/app/app.js');
HtmlHelper::includeJsFile($this->urlBase.'library/js/app/app.config.js');
HtmlHelper::includeJsFile($this->urlBase.'library/js/app/app.api.js');
HtmlHelper::includeJsFile($this->urlBase.'library/js/app/app.task.js');
if (isset($pageNgScripts)) HtmlHelper::includeJsFile($pageNgScripts);
?>
<script type="text/javascript">
var urlNgLib = '<?php echo($this->urlBase); ?>library/js/app/';
</script>

<?php
/* Tracking Code */
if (is_file('includes/templates/com.tracking.php')) {
	require_once('com.tracking.php');
}
?>
