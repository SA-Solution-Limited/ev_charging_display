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

if (!$this->getSiteConfig('opengraph', 'enable')) return;
?>
<!-- Open Graph -->
<?php
$ogImage = isset($ogImage) ? $ogImage : 'images/og-image.jpg';
if (is_file($ogImage)) {
	list($ogImageWidth, $ogImageHeight) = getimagesize($ogImage);
} else {
	unset($ogImage);
}
?>
<meta property="og:url" content="<?php echo(isset($ogUrl) && $ogUrl ? $ogUrl : $this->origin.$this->urlLocaleBase.$this->urlCurrent); ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo(isset($ogTitle) && $ogTitle ? $ogTitle : $locale['og_title']); ?>" />
<?php if (isset($ogImage) && $ogImage) : ?>
<meta property="og:image" content="<?php echo($this->origin.$this->urlBase.$ogImage); ?>" />
<meta property="og:image:width" content="<?php echo($ogImageWidth); ?>" />
<meta property="og:image:height" content="<?php echo($ogImageHeight); ?>" />
<?php endif; ?>
<meta property="og:description" content="<?php echo(isset($ogDesc) && $ogDesc ? $ogDesc : $locale['og_desc']); ?>" />
<meta property="og:site_name" content="<?php echo($locale['site_name']); ?>" />

<?php if ($this->getSrvConfig('facebook') && (($fbAdmins = $this->getSrvConfig('facebook', 'admins')) || ($fbAppId = $this->getSrvConfig('facebook', 'appId')))) : ?>
<!-- Facebook -->
<?php if ($fbAdmins) : ?>
<meta property="fb:admins" content="<?php echo($fbAdmins); ?>" />
<?php endif; ?>
<?php if ($fbAppId) : ?>
<meta property="fb:app_id" content="<?php echo($fbAppId); ?>" />
<?php endif; ?>
<?php endif; ?>

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo(isset($ogUrl) && $ogUrl ? $ogUrl : $this->origin.$this->urlLocaleBase.$this->urlCurrent); ?>">
<meta property="twitter:title" content="<?php echo(isset($ogTitle) && $ogTitle ? $ogTitle : $locale['og_title']); ?>">
<meta property="twitter:description" content="<?php echo(isset($ogDesc) && $ogDesc ? $ogDesc : $locale['og_desc']); ?>">
<?php if (isset($ogImage)) : ?>
<meta property="twitter:image" content="<?php echo($this->origin.$this->urlBase.$ogImage); ?>">
<?php endif; ?>
