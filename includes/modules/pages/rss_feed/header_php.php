<?php
/**
 * rss_feed header_php.php
 *
 * @package rss feed
 * @copyright Copyright 2004-2013 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2013 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @link http://validator.w3.org/feed/docs/rss2.html
 * @link http://feedvalidator.org/
 * @version $Id: header_php.php, v 2.4.2 11.02.2013 10:14 AndrewBerezin $
 */
/*
ToDo:
- feed=products&manufacturer_id=
- feed=reviews&products_id=
- Attribute processing
- Model, weight, brand & etc...
*/

//  @ini_set('display_errors', '1');
//  error_reporting(E_ALL);

// Version upgrade
if (defined('RSS_IMAGE_NAME')) {
  $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='RSS_IMAGE_NAME'");
}
if (defined('RSS_CACHE_TIME')) {
  $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='RSS_CACHE_TIME'");
}

$check = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='RSS_TITLE'");
$configuration_group_id = $check->fields['configuration_group_id'];

@define('RSS_UTM_ACTIVE', 'false');
@define('RSS_UTM_SOURCE', 'rss');
@define('RSS_UTM_MEDIUM', 'rssfeed');
@define('RSS_UTM_TERM', '');
@define('RSS_UTM_CONTENT', '');
@define('RSS_UTM_CAMPAIGN', '');

@define('RSS_BUYNOW_LINK', 'false');
@define('RSS_CACHE_TIME', RSS_TTL);

@define('RSS_PRODUCTS_CONTENT', 'true');

@define('RSS_PRODUCTS_CATEGORIES_TAG', 'main'); // false main all

@define('DIR_FS_RSSFEED_CACHE', DIR_FS_SQL_CACHE);
@define('RSS_ERROR_CACHE_DIR', 'Cache directory not found "' . DIR_FS_RSSFEED_CACHE . '"');

if (!get_cfg_var('safe_mode') && function_exists('set_time_limit')) {
  set_time_limit(0);
}

// disable gzip output buffering if active:
@ob_end_clean();
@ini_set('zlib.output_compression', 'Off');

$_SESSION['navigation']->remove_current_page();

require_once(DIR_WS_CLASSES . 'rss_feed.php');

$rss = new rss_feed();

//$rss->rss_feed_cache_flush(false);
if (RSS_CACHE_TIME > 0) {
  $rss->rssFeedCahcheSet(true);
} else {
  $rss->rssFeedCahcheSet(false);
}
  $rss->rssFeedCahcheSet(false);

$rss->rss_feed_encoding('utf-8', CHARSET);
$rss->rss_feed_content_type('text/xml');
//  $rss->rss_feed_content_type('application/rss+xml');
$rss->rss_feed_set('ttl', RSS_TTL);

if (!$rss->rss_feed_cache($_SERVER['QUERY_STRING'], RSS_CACHE_TIME*60)) {

  // Google Base and Custom Namespaces - http://base.google.com/support/bin/answer.py?answer=58085
  $rss->rss_feed_xmlns('xmlns:g="http://base.google.com/ns/1.0"');
  if (RSS_PRODUCTS_CONTENT == 'true') {
    $rss->rss_feed_xmlns(array('xmlns:content="http://purl.org/rss/1.0/modules/content/"', 'xmlns:dc="http://purl.org/dc/elements/1.1/"'));
  }
//  $rss->rss_feed_xmlns('xmlns:ecommerce="http://shopping.discovery.com/erss'); // "Ecommerce RSS" Module Specification (ERSS) - http://shopping.discovery.com/erss/
//  $rss->rss_feed_xmlns('xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"');
//  $rss->rss_feed_xmlns('xmlns:admin="http://webns.net/mvcb/"');
//  $rss->rss_feed_xmlns('xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"');

  $directory_array = array();
  $tpl_dir = $template->get_template_dir('rss(.*)\.css', DIR_WS_TEMPLATE, $current_page_base, 'css');
  $directory_array = $template->get_template_part($tpl_dir ,'/^rss/', '.css');
  foreach ($directory_array as $value) {
    $rss->rss_feed_style(HTTP_SERVER . DIR_WS_CATALOG . $tpl_dir . '/' . $value);
  }
  $tpl_dir = $template->get_template_dir('rss(.*)\.xsl', DIR_WS_TEMPLATE, $current_page_base, 'css');
  $directory_array = $template->get_template_part($tpl_dir ,'/^rss/', '.xsl');
  foreach ($directory_array as $value) {
    $rss->rss_feed_style(HTTP_SERVER . DIR_WS_CATALOG . $tpl_dir . '/' . $value);
  }

  $rss_title = (RSS_TITLE == '' ? STORE_NAME : RSS_TITLE);
  $rss_title .= RSS_TITLE_DELIMITER . rss_feed_title();

  if (is_file($template->get_template_dir(RSS_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images') . '/' . RSS_IMAGE)) {
    $image = zen_href_link($template->get_template_dir(RSS_IMAGE, DIR_WS_TEMPLATE, $current_page_base, 'images') . '/' . RSS_IMAGE, '', 'NONSSL', false, true, true);
  } elseif (is_file(DIR_FS_CATALOG . DIR_WS_IMAGES . RSS_IMAGE)) {
    $image = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . RSS_IMAGE;
  } else {
    $image = false;
  }
  $rss->rss_feed_image($rss_title, HTTP_SERVER . DIR_WS_CATALOG, $image);

  $rss->rss_feed_description_set(RSS_ITEMS_DESCRIPTION, RSS_ITEMS_DESCRIPTION_MAX_LENGTH);
  $rss->rss_feed_set('title', $rss_title);
  $rss->rss_feed_set('link', HTTP_SERVER . DIR_WS_CATALOG);
  $rss->rss_feed_set('description', RSS_DESCRIPTION);
  $rss->rss_feed_set('lastBuildDate', time());
  $rss->rss_feed_set('generator', 'Zen-Cart ' . RSS_FEED_VERSION . ' RSS 2.0 Feed');
  $rss->rss_feed_set('copyright', 'Copyright &copy; ' . date('Y') . ' ' . (RSS_COPYRIGHT == ''? STORE_OWNER : RSS_COPYRIGHT));
  $rss->rss_feed_set('managingEditor', (RSS_MANAGING_EDITOR == ''? STORE_OWNER_EMAIL_ADDRESS . " (" . STORE_OWNER . ")" : RSS_MANAGING_EDITOR));
  $rss->rss_feed_set('webMaster', (RSS_WEBMASTER == ''? STORE_OWNER_EMAIL_ADDRESS . " (" . STORE_OWNER . ")" : RSS_WEBMASTER));
  $rss->rss_feed_set('language', $_SESSION["languages_code"]);

  $additionalURL = '';
  if ($_SESSION["languages_code"] != DEFAULT_LANGUAGE) {
    $additionalURL .= '&language=' . $_SESSION["languages_code"];
  }
  if (isset($_GET["ref"])) {
    $additionalURL .= '&ref=' . $_GET["ref"];
  }
  if (isset($_GET["utm_source"]) && isset($_GET["utm_medium"])) {
    $additionalURL .= '&utm_source=' . $_GET["utm_source"] . '&utm_medium=' . $_GET["utm_medium"];
    if ($_GET["utm_term"] != '') $additionalURL .= '&utm_term=' . $_GET["utm_term"];
    if ($_GET["utm_content"] != '') $additionalURL .= '&utm_content=' . $_GET["utm_content"];
    if ($_GET["utm_campaign"] != '') $additionalURL .= '&utm_campaign=' . $_GET["utm_campaign"];
  } elseif (RSS_UTM_ACTIVE == 'true' && RSS_UTM_SOURCE != '' && RSS_UTM_MEDIUM != '') {
    $additionalURL .= '&utm_source=' . RSS_UTM_SOURCE . '&utm_medium=' . RSS_UTM_MEDIUM;
    if (RSS_UTM_TERM != '') $additionalURL .= '&utm_term=' . RSS_UTM_TERM;
    if (RSS_UTM_CONTENT != '') $additionalURL .= '&utm_content=' . RSS_UTM_CONTENT;
    if (RSS_UTM_CAMPAIGN != '') $additionalURL .= '&utm_campaign=' . RSS_UTM_CAMPAIGN;
  }

  $random = false;

  if (isset($_GET['products_id'])) $_GET['products_id'] = (int)$_GET['products_id'];

  $limit = "";
  if (isset($_GET['limit'])) {
    if ((int)$_GET['limit'] > 0) {
      $limit = " LIMIT " . (int)$_GET['limit'];
    }
  } elseif ((int)RSS_PRODUCTS_LIMIT > 0) {
    $limit = " LIMIT " . (int)RSS_PRODUCTS_LIMIT;
  }

  switch($_GET["feed"]) {

/*
    case 'orders':
      if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
        $message = false;
        $admin_name = zen_db_prepare_input($_SERVER["PHP_AUTH_USER"]);
        $admin_pass = zen_db_prepare_input($_SERVER["PHP_AUTH_PW"]);
        $sql = "select admin_id, admin_name, admin_pass from " . TABLE_ADMIN . " where admin_name = '" . zen_db_input($admin_name) . "'";
        $result = $db->Execute($sql);
        if (!($admin_name == $result->fields['admin_name'])) {
          $message = true;
          $pass_message = ERROR_WRONG_LOGIN;
        }
        if (!zen_validate_password($admin_pass, $result->fields['admin_pass'])) {
          $message = true;
          $pass_message = ERROR_WRONG_LOGIN;
        }
        if ($message == false) {
          $_SESSION['admin_id'] = $result->fields['admin_id'];
          zen_redirect(zen_href_link(FILENAME_DEFAULT, '', 'SSL'));
        }
      } else {
        $message = true;
      }
      break;
*/

    case 'news_brief':
    case 'news':
//    $rss->rssFeedCahcheSet(false);
//      require_once(DIR_WS_FUNCTIONS . 'news.php');
      $sql = "SELECT n.article_id, na.author_name, na.author_email, nt.news_article_name, nt.news_article_text, nt.news_article_shorttext, n.news_image, nt.news_image_text, n.news_date_published
              FROM " . TABLE_NEWS_ARTICLES . " n
                LEFT JOIN " . TABLE_NEWS_ARTICLES_TEXT . " nt ON (n.article_id=nt.article_id AND nt.language_id=:languageID),
                   " . TABLE_NEWS_AUTHORS . " na
              WHERE n.authors_id = na.authors_id
                AND n.news_status = 1
                AND n.news_date_published <= NOW()
                AND n.news_date_published >= DATE_SUB(NOW(), INTERVAL :NEWS_RSS_FEED_NUMBER_OF_DAYS DAY)
              ORDER BY news_date_published DESC, n.sort_order";
      $sql = $db->bindVars($sql, ':languageID', $_SESSION['languages_id'], 'integer');
      $sql = $db->bindVars($sql, ':NEWS_RSS_FEED_NUMBER_OF_DAYS', NEWS_RSS_FEED_NUMBER_OF_DAYS, 'integer');
      $article = $db->Execute($sql);
      while (!$article->EOF) {
        $xtags = array();
        $link = rss_feed_href_link(FILENAME_NEWS_ARTICLE, 'article_id=' . $article->fields['article_id'] . $additionalURL, 'NONSSL', false);
        if ($_GET["feed"] == 'news_brief') {
          $news_text = $article->fields['news_article_shorttext'];
        } else {
          $news_text = $article->fields['news_article_text'];
        }
        $rss->rss_feed_item($article->fields['news_article_name'],
                            $link,
                            array('url' => $link, 'PermaLink' => true),
                            $article->fields['news_date_published'],
                            $news_text,
                            $article->fields['news_image'],
                            rss_feed_href_link(FILENAME_NEWS_COMMENTS, 'article_id=' . $article->fields['article_id'] . $additionalURL, 'NONSSL', false),
                            $article->fields['author_name'] . " <" . $article->fields['author_email'] . ">",
                            false,
                            false,
                            $xtags
                            );
        $article->MoveNext();
      }
      break;

    case 'categories':
      if (isset($_GET['cPath']) && $current_category_id > 0) {
        $cat_id = $current_category_id;
        $catPath = $_GET['cPath'];
      } else {
        $cat_id = 0;
        $catPath = '';
      }
        zen_rss_category_tree($cat_id, $catPath, isset($_GET['limit']) ? (int)$_GET['limit'] : 32767);
      break;

    case 'specials_random':
      $random = true;
      $rss->rssFeedCahcheSet(false);
      $limit = " LIMIT :MAX_RANDOM_SELECT_SPECIALS";
    case 'specials':
//    $sql = "SELECT DISTINCT p.*, pd.*, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, s.specials_new_products_price as price, m.*, r.*
      $sql = "SELECT DISTINCT p.products_id, pd.products_name, pd.products_description, p.products_image, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_price, p.products_tax_class_id, s.specials_new_products_price as price, p.products_quantity, p.products_model, p.products_weight, p.manufacturers_id, m.manufacturers_name
              FROM " . TABLE_PRODUCTS . " p
                LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (pd.products_id = p.products_id)
                LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
                LEFT JOIN " . TABLE_SPECIALS . " s ON (p.products_id = s.products_id)
              WHERE p.products_status = 1
                AND pd.language_id = :languageID
                AND s.status = 1
             " . $limit;
      $sql = $db->bindVars($sql, ':languageID', $_SESSION['languages_id'], 'integer');
      $sql = $db->bindVars($sql, ':MAX_RANDOM_SELECT_SPECIALS', MAX_RANDOM_SELECT_SPECIALS, 'integer');
      zen_rss_products($sql, $random);
      break;

    case 'featured_random':
      $random = true;
      $rss->rssFeedCahcheSet(false);
      $limit = " LIMIT :MAX_RANDOM_SELECT_FEATURED_PRODUCTS";
    case 'featured':
//    $sql = "SELECT DISTINCT p.*, pd.*, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_price_sorter as price, m.*, r.*
      $sql = "SELECT DISTINCT p.products_id, pd.products_name, pd.products_description, p.products_image, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_price_sorter as price, p.products_tax_class_id, p.products_quantity, p.products_model, p.products_weight, p.manufacturers_id, m.manufacturers_name
             FROM " . TABLE_PRODUCTS . " p
               LEFT JOIN " . TABLE_FEATURED . " f ON (p.products_id = f.products_id)
               LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
               LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
             WHERE p.products_status = 1
               AND f.status = 1
               AND pd.language_id = :languageID
             ORDER BY pd.products_name DESC
             " . $limit;
      $sql = $db->bindVars($sql, ':languageID', $_SESSION['languages_id'], 'integer');
      $sql = $db->bindVars($sql, ':MAX_RANDOM_SELECT_FEATURED_PRODUCTS', MAX_RANDOM_SELECT_FEATURED_PRODUCTS, 'integer');
      zen_rss_products($sql, $random);
      break;

    case 'best_sellers_random':
      $random = true;
      $rss->rssFeedCahcheSet(false);
      $limit = " LIMIT :MAX_DISPLAY_BESTSELLERS";
    case 'best_sellers':
      $where_cat = $from_cat = "";
      if (isset($_GET['cPath']) && $current_category_id > 0) {
        if (RSS_PRODUCTS_CATEGORIES == 'all') {
          $from_cat = ", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c ";
          $where_cat = " AND p.products_id = p2c.products_id
                         AND c.categories_id = :currentCategoryID
                         AND p2c.categories_id = :currentCategoryID ";
        } else {
          $where_cat = " AND p.master_categories_id = :currentCategoryID ";
        }
        $where_cat = $db->bindVars($where_cat, ':currentCategoryID', $current_category_id, 'integer');
      }
//    $sql = "SELECT DISTINCT p.*, pd.*, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_price_sorter as price, m.*, r.*
      $sql = "SELECT DISTINCT p.products_id, pd.products_name, pd.products_description, p.products_image, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_ordered, p.products_price_sorter as price, p.products_tax_class_id, p.products_quantity, p.products_model, p.products_weight, p.manufacturers_id, m.manufacturers_name
              FROM " . TABLE_PRODUCTS . " p
                LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (pd.products_id = p.products_id)
                LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
              " . $from_cat . "
              WHERE p.products_status = 1
                AND p.products_ordered > 0
                AND pd.language_id = :languageID
              ORDER BY p.products_ordered DESC, pd.products_name
              " . $limit;
      $sql = $db->bindVars($sql, ':languageID', $_SESSION['languages_id'], 'integer');
      $sql = $db->bindVars($sql, ':MAX_DISPLAY_BESTSELLERS', MAX_DISPLAY_BESTSELLERS, 'integer');
      zen_rss_products($sql, $random);
      break;

    case 'upcoming_random':
      $random = true;
      $rss->rssFeedCahcheSet(false);
      $limit = " LIMIT :MAX_DISPLAY_UPCOMING_PRODUCTS";
    case 'upcoming':
      $where_cat = $from_cat = "";
      $display_limit = zen_get_upcoming_date_range();
      if (isset($_GET['cPath']) && $current_category_id > 0) {
        if (RSS_PRODUCTS_CATEGORIES == 'all') {
          $from_cat = ", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c ";
          $where_cat = "AND p.products_id = p2c.products_id
                        AND c.categories_id = " . (int)$current_category_id . "
                        AND p2c.categories_id = " . (int)$current_category_id . " ";
        } else {
          $where_cat = "AND p.master_categories_id = " . (int)$current_category_id . " ";
        }
      }
//    $sql = "SELECT DISTINCT p.*, pd.*, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_date_available as date_expected, p.products_price_sorter as price, m.*, r.*
      $sql = "SELECT DISTINCT p.products_id, pd.products_name, pd.products_description, p.products_image, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_date_available as date_expected, p.products_price_sorter as price, p.products_tax_class_id, p.products_quantity, p.products_model, p.products_weight, p.manufacturers_id, m.manufacturers_name
              FROM " . TABLE_PRODUCTS . " p
                LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (pd.products_id = p.products_id)
                LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
              " . $from_cat . "
              WHERE to_days(products_date_available) >= to_days(now())
                AND p.products_status = 1
                AND pd.language_id = :languageID
              " . $where_cat . "
              " . $display_limit . "
              ORDER BY " . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . $limit;
      $sql = $db->bindVars($sql, ':languageID', $_SESSION['languages_id'], 'integer');
      $sql = $db->bindVars($sql, ':MAX_DISPLAY_UPCOMING_PRODUCTS', MAX_DISPLAY_UPCOMING_PRODUCTS, 'integer');
      zen_rss_products($sql, $random);
      break;

    case 'new_products_random':
      $random = true;
      $rss->rssFeedCahcheSet(false);
      $limit = " LIMIT " . MAX_RANDOM_SELECT_NEW;
    case 'new_products':
//      $disp_order_default = PRODUCT_NEW_LIST_SORT_DEFAULT;
//      require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_LISTING_DISPLAY_ORDER));
      $order_by = " order by products_date DESC, pd.products_name";

      $where_days = zen_get_new_date_range();
//      $where_days = str_replace('p.products_date_added', 'products_date', $where_days);

    case 'products':
    default:
      $where_cat = $from_cat = "";
      if (isset($_GET['cPath']) && $current_category_id > 0) {
        if (RSS_PRODUCTS_CATEGORIES == 'all') {
          $from_cat = ", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c ";
          $where_cat = "AND p.products_id = p2c.products_id
                        AND c.categories_id = " . (int)$current_category_id . "
                        AND p2c.categories_id = " . (int)$current_category_id . " ";
        } else {
          $where_cat = "AND p.master_categories_id = " . (int)$current_category_id . " ";
        }
      }
      $where_prod = '';
      if (isset($_GET['products_id'])) {
        $where_prod = " AND p.products_id=" . (int)$_GET['products_id'];
        $limit = " LIMIT 1";
      } else if (isset($_GET['products_model'])) {
        $where_prod = " AND p.products_model=" . zen_db_input(zen_db_prepare_input($_GET['products_model']));
      }
      if (!isset($order_by)) {
        $order_by = " ORDER BY p.products_last_modified DESC, p.products_sort_order";
      }
      if (!isset($where_days)) {
        $where_days = '';
      }
//    $sql = "SELECT DISTINCT p.*, pd.*, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.products_price_sorter AS price, m.*, r.*
      $sql = "SELECT DISTINCT p.products_id, pd.products_name, pd.products_description, p.products_image, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS products_date, p.master_categories_id, p.products_price_sorter AS price, p.products_tax_class_id, p.products_quantity, p.products_model, p.products_weight, p.manufacturers_id, m.manufacturers_name
                       FROM " . TABLE_PRODUCTS . " p
                         LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
                         LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
                         " . $from_cat . "
                       WHERE pd.language_id = " . (int)$_SESSION['languages_id'] . "
                         AND p.products_status = 1
                         " . $where_cat . "
                         " . $where_days . "
                         " . $where_prod . "
                         " . $order_by . "
                         " . $limit;
      zen_rss_products($sql, $random);
      break;

    case 'reviews':
    //products_id
      break;

  }

}

function zen_rss_products($sql, $random) {
  global $db, $currencies, $rss, $additionalURL;

  $imageSize = (isset($_GET['imgsize']) ? $_GET['imgsize'] : RSS_DEFAULT_IMAGE_SIZE);

  $sql_maxdate = "SELECT GREATEST(MAX(products_date_added), MAX(IFNULL(products_last_modified, '0001-01-01 00:00:00'))) as max_date
                  FROM " . TABLE_PRODUCTS . "
                  WHERE products_status = 1";
  $maxdate = $db->Execute($sql_maxdate);
  if (!$maxdate->EOF) {
    $rss->rss_feed_set('lastBuildDate', $maxdate->fields['max_date']);
  }

  if ($random) {
    $products = zen_random_select($sql);
  } else {
    $products = $db->Execute($sql);
  }

  $cashTaxRate = array();
  $file_main_product_image = DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE);
  $file_additional_images = DIR_WS_MODULES . zen_get_module_directory('additional_images.php');
  $category = false;
  while (!$products->EOF) {
    $info_page = zen_get_info_page($products->fields['products_id']);
    $xtags = array();

    if (RSS_PRODUCTS_PRICE == 'true' && $products->fields['price'] > 0) {
      if (!isset($cashTaxRate[$products->fields['products_tax_class_id']])) {
        $cashTaxRate[$products->fields['products_tax_class_id']] = zen_get_tax_rate($products->fields['products_tax_class_id']);
      }
      $xtags['g:price'] = number_format(zen_add_tax($products->fields['price'] * $currencies->get_value($_SESSION['currency']), $cashTaxRate[$products->fields['products_tax_class_id']]), $currencies->get_decimal_places($_SESSION['currency']), '.', '');
//      $xtags['c:price_formatted type="string"'] = zen_get_products_display_price($products->fields['products_id']);
//      $xtags['c:price_formatted type="string"'] = $currencies->display_price($products->fields['price'], $cashTaxRate[$products->fields['products_tax_class_id']]);
    }
    if (RSS_PRODUCTS_CURRENCY == 'true') {
      $xtags['g:currency'] = ($_SESSION["currency"] == 'RUR' ? 'RUB' : $_SESSION["currency"]);
    }
    if (RSS_PRODUCTS_ID == 'true') {
      $xtags['g:id'] = $products->fields['products_id'];
    }
    if (RSS_PRODUCTS_WEIGHT == 'true' && $products->fields['products_weight'] > 0) {
      $xtags['g:weight'] = $products->fields['products_weight'];
    }
    if (RSS_PRODUCTS_BRAND == 'true' && zen_not_null($products->fields['manufacturers_name'])) {
      $xtags['g:brand'] = $rss->_clear_string($products->fields['manufacturers_name']);
    }
    if (RSS_PRODUCTS_QUANTITY == 'true' && $products->fields['products_quantity'] > 0) {
      $xtags['g:quantity'] = $products->fields['products_quantity'];
    }
    if (RSS_PRODUCTS_MODEL == 'true' && zen_not_null($products->fields['products_model'])) {
      $xtags['g:model_number'] = $rss->_clear_string($products->fields['products_model']);
    }
    $sql = "SELECT COUNT(*) AS total, (AVG(reviews_rating)/5) AS average_rating
            FROM " . TABLE_REVIEWS . "
            WHERE products_id=:productsID";
    $sql = $db->bindVars($sql, ':productsID', $products->fields['products_id'], 'integer');
    $reviews = $db->Execute($sql);
//      echo '<!--'."\n";var_dump($reviews->fields);echo '-->'."\n";
    if (RSS_PRODUCTS_RATING == 'true' && zen_not_null($reviews->fields['average_rating']) && $reviews->fields['average_rating'] > 0) {
      $xtags['g:rating'] = zen_round($reviews->fields['average_rating'], 0); // rating - The rating of the item. Format: Text. XML example: <g:rating>4 stars</g:rating>
//        echo '<!--'."\n";var_dump($xtags['g:rating']);echo '-->'."\n";
    }
/*
image_link
The URL of an associated image for the item. Use your full-sized images; do not use thumbnail images.
If you do not have an image available, leave the attribute blank.
Do not include logos or an image that says, "Image not available."
Format:
URL. (Must include the http:// portion.) Up to 10 URLs can be included.
For XML, include each URL as a separate <image_link> attribute.
XML example:
<g:image_link>http://www.example.com/image1.jpg</g:image_link>
<g:image_link>http://www.example.com/image2.jpg</g:image_link>
*/
    if (RSS_PRODUCTS_IMAGES == 'true' && zen_not_null($products->fields['products_image'])) {
      $products_image = ltrim($products->fields['products_image'], '/');
//        require($file_main_product_image);
      $products_image_extension = substr($products_image, strrpos($products_image, '.'));
      $products_image_base = str_replace($products_image_extension, '', $products_image);
      $products_image_medium = $products_image_base . IMAGE_SUFFIX_MEDIUM . $products_image_extension;
      $products_image_large = $products_image_base . IMAGE_SUFFIX_LARGE . $products_image_extension;

      // check for a medium image else use small
      if (!file_exists(DIR_WS_IMAGES . 'medium/' . $products_image_medium)) {
        $products_image_medium = DIR_WS_IMAGES . $products_image;
      } else {
        $products_image_medium = DIR_WS_IMAGES . 'medium/' . $products_image_medium;
      }
      // check for a large image else use medium else use small
      if (!file_exists(DIR_WS_IMAGES . 'large/' . $products_image_large)) {
        //  if (!file_exists(DIR_WS_IMAGES . 'medium/' . $products_image_medium)) {
        if (!file_exists($products_image_medium)) {
          $products_image_large = DIR_WS_IMAGES . $products_image;
        } else {
        //    $products_image_large = DIR_WS_IMAGES . 'medium/' . $products_image_medium;
          $products_image_large = $products_image_medium;
        }
      } else {
        $products_image_large = DIR_WS_IMAGES . 'large/' . $products_image_large;
      }
      switch ($imageSize) {
        case 'small':
          $xtags['g:image_link'][0] = $rss->_clear_url(HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . $products_image);
          break;
        case 'medium':
          $xtags['g:image_link'][0] = $rss->_clear_url(HTTP_SERVER . DIR_WS_CATALOG . $products_image_medium);
          break;
        case 'large':
        default:
          $xtags['g:image_link'][0] = $rss->_clear_url(HTTP_SERVER . DIR_WS_CATALOG . $products_image_large);
          break;
      }
      if (isset($_GET['products_id']) || isset($_GET['products_model'])) {
        require($file_additional_images);
        $num_images = min(9, $num_images);
        for ($i=0, $n=$num_images; $i<$n; $i++) {
          $file = $images_array[$i];
          $products_image_large = str_replace(DIR_WS_IMAGES, DIR_WS_IMAGES . 'large/', $products_image_directory) . str_replace($products_image_extension, '', $file) . IMAGE_SUFFIX_LARGE . $products_image_extension;
          $flag_has_large = file_exists($products_image_large);
          $products_image_large = ($flag_has_large ? $products_image_large : $products_image_directory . $file);
          $xtags['g:image_link'][] = $rss->_clear_url(HTTP_SERVER . DIR_WS_CATALOG . $products_image_large);
        }
      }
    }

    $link = rss_feed_href_link($info_page, 'products_id=' . $products->fields['products_id'] . $additionalURL, 'NONSSL', false);

    $products_description = $products->fields['products_description'];
    if (RSS_PRODUCTS_DESCRIPTION_IMAGE == 'true' && zen_not_null($products->fields['products_image'])) {
      $image_url = zen_image(DIR_WS_IMAGES . $products->fields['products_image'], $products->fields['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'style="float: left; margin: 0px 8px 8px 0px;"');
      $image_url = str_replace('<img src="', '<img src="' . HTTP_SERVER . DIR_WS_CATALOG, $image_url);
      $image_link = '<a href="' . $link . '">' . $image_url . '</a>';
      if (RSS_STRIP_TAGS == 'true') {
        $products_description = '<![CDATA[' . $image_link . ']]>' . $products_description;
      } else {
        $products_description = $image_link . $products_description;
      }
    }

    if (RSS_PRODUCTS_CONTENT == 'true') {
      $xtags['content:encoded'] = $products->fields['products_description'];
    }

    $price = zen_get_products_display_price($products->fields['products_id']);

    $buynow_button = zen_image_button(BUTTON_IMAGE_BUY_NOW, BUTTON_BUY_NOW_ALT);
    $buynow_button = str_replace('<img src="', '<img src="' . HTTP_SERVER . DIR_WS_CATALOG, $buynow_button);
    $buynow_button = preg_replace('@onmouseover="[^"]*"@i', '', $buynow_button);
    $buynow_button = preg_replace('@onmouseout="[^"]*"@i', '', $buynow_button);
    $buynow_button = str_replace(' >', '>', $buynow_button);
    $buynow_link = rss_feed_href_link(FILENAME_SHOPPING_CART, 'products_id=' . $products->fields['products_id'] . '&action=buy_now' . $additionalURL, 'SSL', false);

    if (RSS_PRODUCTS_DESCRIPTION_BUYNOW == 'true') {
      $buynow_anchor = "\n" . '<br /><br />' . '<a href="' . $buynow_link . '" target="_blank">' . $buynow_button . '</a>' . "\n";
      if (RSS_STRIP_TAGS == 'true') {
        $products_description .= '<![CDATA[' . $buynow_anchor . ']]>';
      } else {
        $products_description .= $buynow_anchor;
      }
    }

    if (RSS_BUYNOW_LINK == 'true') {
      $xtags['zencart:buynow_link'] = $buynow_link;
      $xtags['zencart:buynow_button'] = $buynow_button;
    }

    if (RSS_PRODUCTS_CATEGORIES_TAG != 'false') {
      if (RSS_PRODUCTS_CATEGORIES_TAG == 'all') {
        $category_list = rss_feed_get_category_list($products->fields['products_id']);
      } else {
        $category_list = array($products->fields['master_categories_id']);
      }
      $category = array();
      foreach ($category_list as $i => $category_list_id) {
        $category[$i] = rss_feed_category_info($category_list_id);
      }
    }

// TODO:
// Attribute processing
// Model, weight, brand & etc...
    $rss->rss_feed_item($products->fields['products_name'],
                        $link,
                        array('url' => $link, 'PermaLink' => true),
                        $products->fields['products_date'],
                        $products_description,
                        $rss->_clear_url(HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . $products->fields['products_image']),
                        array('url' => rss_feed_href_link(FILENAME_PRODUCT_REVIEWS,'products_id=' . $products->fields['products_id'] . $additionalURL, 'NONSSL', false), 'count' => $reviews->fields['total']),
                        (RSS_AUTHOR == ''? STORE_OWNER_EMAIL_ADDRESS . " <" . STORE_OWNER . ">" : RSS_AUTHOR),
                        $category,
                        false,
                        $xtags
                        );
    if ($random)
      break;
    $products->MoveNext();
  }
}

function zen_rss_category_tree($id_parent=0, $str_cPath='', $limit = 32767) {
  global $db, $rss, $additionalURL;
  if ($limit < 0) return;
//  $categories = $db->Execute("SELECT c.*, cd.*, GREATEST(c.date_added, IFNULL(c.last_modified, '0001-01-01 00:00:00')) AS categories_date
  $sql = "SELECT c.categories_id, c.parent_id, GREATEST(c.date_added, IFNULL(c.last_modified, '0001-01-01 00:00:00')) AS categories_date, c.categories_image, cd.categories_name, cd.categories_description
          FROM " . TABLE_CATEGORIES . " c
            LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id = cd.categories_id)
          WHERE c.parent_id = :parentID
            AND cd.language_id = :languageID
            AND c.categories_status = 1
          ORDER BY c.sort_order, cd.categories_name";
  $sql = $db->bindVars($sql, ':parentID', $id_parent, 'integer');
  $sql = $db->bindVars($sql, ':languageID', $_SESSION['languages_id'], 'integer');
  $categories = $db->Execute($sql, '', false, 150);
  if ($categories->RecordCount() == 0)
    return;
  while (!$categories->EOF && $limit>0) {
    $new_str_cPath = (zen_not_null($str_cPath) ? $str_cPath . '_' . $categories->fields['categories_id'] : $categories->fields['categories_id']);
    $products_in_category = zen_count_products_in_category($categories->fields['categories_id']);
    if ((CATEGORIES_COUNT_ZERO == '1' && $products_in_category == 0) or $products_in_category >= 1) {
      $limit--;
      $link = rss_feed_href_link(FILENAME_DEFAULT, 'cPath=' . $new_str_cPath . $additionalURL, 'NONSSL', false);
      $rss->rss_feed_item($categories->fields['categories_name'],
                          $link,
                          array('url' => $link, 'PermaLink' => true),
                          $categories->fields['categories_date'],
                          $categories->fields['categories_description'],
                          $categories->fields['categories_image'],
                          false,
                          (RSS_AUTHOR == ''? STORE_OWNER_EMAIL_ADDRESS . " <" . STORE_OWNER . ">" : RSS_AUTHOR)
                          );
    }
    if (zen_has_category_subcategories($categories->fields['categories_id'])) {
      zen_rss_category_tree($categories->fields['categories_id'], $new_str_cPath, $limit);
    }
    $categories->MoveNext();
  }
}

function rss_feed_category_info($categories_id) {
  global $db, $rss, $additionalURL;
  static $categories_info = array();
  if (!isset($categories_info[$categories_id])) {
    $cPath = zen_get_generated_category_path_rev($categories_id);
    $link = rss_feed_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath . $additionalURL, 'NONSSL', false);
    $categories_info[$categories_id]['domain'] = $link;
    $categories_info[$categories_id]['name'] = zen_get_category_name($categories_id, $_SESSION['languages_id']);
  }
  return $categories_info[$categories_id];
}

function rss_feed_get_category_list($products_id) {
  global $db;
  $category_list = array();
  $sql = "SELECT * FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = :productsID";
  $sql = $db->bindVars($sql, ':productsID', $products_id, 'integer');
  $p2c = $db->Execute($sql);
  while (!$p2c->EOF) {
    $category_list[] = $p2c->fields['categories_id'];
    $p2c->MoveNext();
  }
  return $category_list;
}

// EOF