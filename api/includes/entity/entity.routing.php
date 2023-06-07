<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('includes/class/class.entity.php');

/**
 * Entity class for `routing`
 * @property int $rouId
 * @property string $rouType Possible values: REWRITE; REDIRECT
 * @property string $rouSlug
 * @property string|null $rouLocale
 * @property string $rouTarget
 * @property int $rouTotalClick Default value: 0
 * @property boolean $rouIsEnabled Default value: 1
 * @property boolean $rouIsDeleted Default value: 0
 * @property int|null $rouDelKey
 * @property string $rouCreated Default value: 1970-01-01 00:00:00
 * @property int $rouCreator
 * @property string|null $rouModified
 * @property int|null $rouModifier
 */
class RoutingEntity extends Entity {
	public $rouId;
	public $rouType;
	public $rouSlug;
	public $rouLocale;
	public $rouTarget;
	public $rouTotalClick = 0;
	public $rouIsEnabled = 1;
	public $rouIsDeleted = 0;
	public $rouDelKey;
	public $rouCreated = '1970-01-01 00:00:00';
	public $rouCreator;
	public $rouModified;
	public $rouModifier;
}
?>
