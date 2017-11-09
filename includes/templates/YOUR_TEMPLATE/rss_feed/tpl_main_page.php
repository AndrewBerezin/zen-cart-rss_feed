<?php
/**
 * rss_feed tpl_main_page.php
 *
 * @package rss feed
 * @copyright Copyright 2004-2012 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_main_page.php, v 2.3.3 22.10.2010 15:25:20 Andrew Berezin $
 */

  if (headers_sent($filename, $linenum)) {
    echo "Headers already sent in $filename on line $linenum";
//    exit;
  }

  $rss->rss_feed_out();

  require(DIR_WS_INCLUDES . 'application_bottom.php');

  zen_exit();