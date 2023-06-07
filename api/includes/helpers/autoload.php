<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

$scan = scandir(dirname(__FILE__));
foreach ($scan as $file) {
    if (is_file(dirname(__FILE__)."/{$file}") && preg_match('/^helper\..+\.php$/', $file)) {
        require_once($file);
    }
}
?>
