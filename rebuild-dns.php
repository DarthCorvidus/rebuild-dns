#!/usr/bin/php
<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <rebuild-dns@vm01.telton.de>
 * @license GPLv3
 */
require_once __DIR__.'/include/RebuildDNS.php';
require_once __DIR__.'/include/Config.php';
$config = new Config(__DIR__."/config.ini");
$autodns = new RebuildDNS($argv, $config);
$autodns->run();
#echo $autodns->getIPv6Hosts();
#echo $autodns->getIPv4Hosts();


?>
