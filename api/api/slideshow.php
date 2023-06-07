<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 * 
 * @var array|null $params
 */

require_once('includes/class/class.ajaxform.php');

class Slideshow extends AjaxForm {

	function __construct($params = null) {
		global $site;

		$this->response(true, null, array(
			'data' => array(
				$site->origin.$site->urlBase.'uploads/slideshow/hketoll.jpg',
				'https://placehold.co/1240x774?font=roboto',
			),
		));
	}
}

new Slideshow($params);
?>
