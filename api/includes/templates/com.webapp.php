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

if (!$this->getSiteConfig('pwa', 'enable')) return;

$utm = array(
	'utm_source' => HttpHelper::getGetParam('utm_source', null),
	'utm_medium' => HttpHelper::getGetParam('utm_medium', null),
	'utm_campaign' => HttpHelper::getGetParam('utm_campaign', null),
	'utm_term' => HttpHelper::getGetParam('utm_term', null),
	'utm_content' => HttpHelper::getGetParam('utm_content', null),
);
?>
<!-- Progressive Web App Settings -->
<link rel="manifest" href="<?php echo(UrlHelper::addQueryString($utm, "{$this->urlBase}manifest.json")); ?>" />
<link rel="apple-touch-icon" href="<?php echo($this->urlBase) ?>images/webapp/icon.png" />
<link rel="apple-touch-startup-image" href="<?php echo($this->urlBase) ?>images/webapp/splash.png" />
<meta name="apple-mobile-web-app-title" content="<?php echo($locale['site_name']); ?>" />
<meta name="apple-mobile-web-app-capable" content="yes" />
