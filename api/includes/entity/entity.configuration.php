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
 * Entity class for `configuration`
 * @property string $cfgKey
 * @property string|null $cfgValue
 * @property string|null $cfgDefaultValue
 * @property string $cfgType Default value: text
 * @property string|null $cfgOptions
 */
class ConfigurationEntity extends Entity {
	public $cfgKey;
	public $cfgValue;
	public $cfgDefaultValue;
	public $cfgType = 'text';
	public $cfgOptions;
}
?>
