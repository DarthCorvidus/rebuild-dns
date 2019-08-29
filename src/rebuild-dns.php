#!/usr/bin/php
<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <rebuild-dns@vm01.telton.de>
 * @license GPLv3
 */
#Include
require_once __DIR__.'/include/Config.php';
require_once __DIR__.'/include/MACEntry.php';
require_once __DIR__.'/include/MACTable.php';
require_once __DIR__.'/include/RebuildDNS.php';
#/Include
$config = new Config(__DIR__."/config.ini");
$autodns = new RebuildDNS($argv, $config);
$autodns->run();
?>