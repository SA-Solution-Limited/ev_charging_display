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

if ($this->doNotTrack || !($code = $this->getSrvConfig('google', 'analytics'))) return;

if (preg_match('/^UA-/', $code)) :
?>
<!-- Google Analysics -->
<script type="text/javascript">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', '<?php echo($code); ?>', 'auto');
ga('send', 'pageview');
</script>
<?php
elseif (preg_match('/^G-/', $code)) :
?>
<!-- Google Analytics Global Site Tag (gtag.js) -->
<?php HtmlHelper::includeJsFile('https://www.googletagmanager.com/gtag/js?id='.$code, null, array('async' => true)); ?>
<script type="text/javascript">
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo($code); ?>');
</script>
<?php
endif;
?>
