#!/usr/bin/php
<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <lnkbackup@vm01.telton.de>
 * @license GPLv3
 */
require_once __DIR__.'/include/RebuildDNS.php';
$autodns = new RebuildDNS($argv);
$autodns->run();
#echo $autodns->getIPv6Hosts();
#echo $autodns->getIPv4Hosts();


?>
