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
</div>

<!-- Global JavaScripts -->
<?php
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/jquery/jquery.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/popper.js/popper.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/bootstrap/js/bootstrap.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/day.js/dayjs.min.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/day.js/locale/zh-hk.js');
HtmlHelper::includeJsFile($this->urlBase.'library/cdn-plugins/technetium.framework/api-helper/api-helper.js');
?>

<!-- Page Level Plugins -->
<?php
if (isset($pageLevelPlugins)) HtmlHelper::includeJsFile($pageLevelPlugins);
?>

<!-- Page Level Scripts -->
<?php
if (isset($pageLevelScripts)) HtmlHelper::includeJsFile($pageLevelScripts);
?>

<!-- Plugin Configurations -->
<script type="text/javascript">
(function() {
	dayjs.locale('zh-hk');
})();
</script>

<!-- Page Init Scripts -->
<script type="text/javascript">
window.addEventListener('load', function() {
	<?php if (isset($pageInitScripts)) echo($pageInitScripts); ?>
});
</script>
</body>
</html>
