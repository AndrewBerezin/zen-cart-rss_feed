<?php
/**
 * rss_feed.php
 *
 * @package rss feed
 * @copyright Copyright 2004-2015 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @link http://validator.w3.org/feed/docs/rss2.html
 * @link http://feedvalidator.org/
 * @version $Id: rss_feed.php, v 2.4.2 29.03.2015 18:19:02 AndrewBerezin $
 */

class rss_feed extends base {
  var $xmlns = array();
  var $encoding = "UTF-8";
  var $source_encoding = false;
  var $convert_to_utf8 = null;

  var $content_type = "text/xml";

  var $stylesheets = array();

  var $title = "";
  var $link = "";
  var $description = "";

  var $language = "en-us";
  var $copyright = false;
  var $managingEditor = false;
  var $webMaster = false;
  var $pubDate = false;
  var $lastBuildDate = false;
  var $category = false;
  var $generator = "RSS Feed Generator";
  var $docs = false;
  var $cloud = false;
  var $ttl = 1440;
  var $image = false;
  var $textInput = false;
  var $skipHours = false;
  var $skipDays = false;

  var $xElements = array();

  var $item = array();
  var $description_out = true;
  var $description_out_max = 0;

  var $rssFeedContent = false;
  var $rssFeedTimeCreated = false;

  var $rssFeedCahcheFlag = true;
  var $rssFeedCahcheFileName = false;
  var $rssFeedCahcheFrom = false;


  function __construct($xmlns = array()) {
    $xmlns_default = array(
                          'xmlns:content="http://purl.org/rss/1.0/modules/content/"',
                          'xmlns:wfw="http://wellformedweb.org/CommentAPI/"',
                          'xmlns:dc="http://purl.org/dc/elements/1.1/"',
                          'xmlns:atom="http://www.w3.org/2005/Atom"',
                          'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"',
                          'xmlns:slash="http://purl.org/rss/1.0/modules/slash/"',
                           );
    $this->rss_feed_xmlns($xmlns_default);
    $this->rss_feed_xmlns($xmlns);
  }

  function rss_feed_xmlns($xmlns = "") {
    if (is_array($xmlns)) {
      foreach ($xmlns as $i => $v) {
        $this->xmlns[] = $xmlns[$i];
      }
    } elseif (is_string($xmlns)) {
      $this->xmlns[] = $xmlns;
    }
  }

  function rss_feed_style($href) {
    $this->stylesheets[] = $href;
  }

  function rss_feed_encoding($encoding, $source_encoding=false) {
    $this->encoding = $encoding;
    $this->source_encoding = $source_encoding;
    if ($this->source_encoding == false) {
      $this->source_encoding = $this->encoding;
    }
    $this->encoding = strtolower($this->encoding);
    $this->source_encoding = strtolower($this->source_encoding);
    $this->convert_to_utf8 = ($this->encoding == 'utf-8' && $this->source_encoding != 'utf-8'  && $this->source_encoding != $this->encoding);
  }

  function rss_feed_content_type($content_type) {
    $this->content_type = $content_type;
  }

  function rss_feed_set($name, $value) {
    switch ($name) {
      case 'title':
        $this->title = $value;
        break;
      case 'link':
        $this->link = $value;
        break;
      case 'description':
        $this->description = $value;
        break;
      case 'language':
        $this->language = $value;
        break;
      case 'copyright':
        $this->copyright = $value;
        break;
      case 'managingEditor':
        $this->managingEditor = $value;
        break;
      case 'webMaster':
        $this->webMaster = $value;
        break;
      case 'pubDate':
        $this->pubDate = $value;
        break;
      case 'lastBuildDate':
        $this->lastBuildDate = $value;
        break;
// category $this->rss_feed_category
      case 'generator':
        $this->generator = $value;
        break;
      case 'docs':
        $this->docs = $value;
        break;
// cloud $this->rss_feed_cloud
      case 'ttl':
        $this->ttl = $value;
        break;
// image $this->rss_feed_image
// textInput $this->rss_feed_textInput
      case 'skipHours':
        $this->skipHours = explode(',', $value);
        break;
      case 'skipDays':
        $this->skipDays = explode(',', $value);
        break;
    }
    return;
  }

  function rss_feed_category($name, $domain=false) {
    $this->category['name'] = $name;
    $this->category['domain'] = $domain;
  }

  function rss_feed_cloud($domain, $port, $path, $registerProcedure, $protocol) {
    $this->cloud['domain'] = $domain;
    $this->cloud['port'] = $port;
    $this->cloud['path'] = $path;
    $this->cloud['registerProcedure'] = $registerProcedure;
    $this->cloud['protocol'] = $protocol;
  }

  function rss_feed_image($title, $link, $url, $width=0, $height=0, $description=false) {
    $this->image['title'] = $title;
    $this->image['link'] = $link;
    $this->image['url'] = $url;
    $this->width['width'] = $width;
    $this->width['height'] = $height;
    $this->width['description'] = $description;
  }

  function rss_feed_textInput($title, $link, $description, $name) {
    $this->textInput['title'] = $title;
    $this->textInput['link'] = $link;
    $this->textInput['description'] = $description;
    $this->textInput['name'] = $name;
  }

  function rss_feed_description_set($out, $max) {
    $this->description_out = $out;
    $this->description_out_max = $max;
  }

  function rss_feed_item($title, $link, $guid, $pubDate = false, $description = false, $enclosure = false, $comments = false, $author = false, $category = false, $source=false, $ext_tags = array()) {
    $this->item['title'][] = $title;
    $this->item['link'][] = $link;
    $this->item['guid'][] = $guid;
    $this->item['pubDate'][] = $pubDate;
    $this->item['description'][] = $description;
    $this->item['enclosure'][] = $enclosure;
    $this->item['comments'][] = $comments;
    $this->item['author'][] = $author;
    $this->item['category'][] = $category;
    $this->item['source'][] = $source;

    $this->item['ext_tags'][] = $ext_tags;
  }

  function rss_feed_content() {
    $feedContent = '<?xml version="1.0" encoding="' . $this->encoding . '"?'.'>' . "\n";
    foreach($this->stylesheets as $stylesheet) {
      if (substr($stylesheet, -3) == 'xsl') {
        $feedContent .= '<?xml-stylesheet type="text/xsl" href="' . $stylesheet . '" media="screen"?'.'>' . "\n";
      } else {
        $feedContent .= '<?xml-stylesheet type="text/css" href="' . $stylesheet . '" media="screen"?'.'>' . "\n";
      }
    }
    $this->xmlns = array_unique($this->xmlns);
    if (sizeof($this->xmlns) > 0) {
      $xmlns = "\n" . implode("\n", $this->xmlns);
    } else {
      $xmlns = "";
    }
    $feedContent .= '<!-- generator="Zen-Cart RSS Feed/' . RSS_FEED_VERSION . '" -->' . "\n";
    $feedContent .= '<rss version="2.0" ' . $xmlns . '>' . "\n" .
            '  <channel>' . "\n" .
            '    <title>' . $this->_clear_string($this->title) . '</title>' . "\n" .
            '    <link>' . $this->_clear_url($this->link) . '</link>' . "\n" .
            '    <description>' . $this->_clear_content($this->description) . '</description>' . "\n";
    $this->link_self = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $feedContent .= '    <atom:link href="' . $this->_clear_url($this->link_self) . '" rel="self" type="application/rss+xml" />' . "\n";
    if ($this->language)
      $feedContent .= '    <language>' . $this->language . '</language>' . "\n";
    if ($this->copyright)
      $feedContent .= '    <copyright>' . $this->_clear_string($this->copyright) . '</copyright>' . "\n";
    if ($this->managingEditor)
      $feedContent .= '    <managingEditor>' . $this->_clear_email($this->managingEditor) . '</managingEditor>' . "\n";
    if ($this->webMaster)
      $feedContent .= '    <webMaster>' . $this->_clear_email($this->webMaster) . '</webMaster>' . "\n";
    if ($this->pubDate)
      $feedContent .= '    <pubDate>' . $this->_date($this->pubDate) . '</pubDate>' . "\n";
    if (!$this->lastBuildDate)
      $this->lastBuildDate = date('r');
    $feedContent .= '    <lastBuildDate>' . $this->_date($this->lastBuildDate) . '</lastBuildDate>' . "\n";
    if ($this->category) {
      if ($this->category['domain']) {
        $feedContent .= '    <category domain="' . $this->_clear_url($this->category['domain']) . '">' . $this->_clear_string($this->category['name']) . '</category>' . "\n";
      } else {
        $feedContent .= '    <category>' . $this->_clear_string($this->category['name']) . '</category>' . "\n";
      }
    }
    if ($this->generator)
      $feedContent .= '    <generator>' . $this->_clear_string($this->generator) . '</generator>' . "\n";
    if ($this->docs)
      $feedContent .= '    <docs>' . $this->_clear_url($this->docs) . '</docs>' . "\n";
    if ($this->cloud)
      $feedContent .= '    <cloud domain=">' . $this->_clear_url($this->cloud['domain']) . '" port="' . $this->cloud['port'] . '" path="' . $this->cloud['path'] . '" registerProcedure="' . $this->cloud['registerProcedure'] . '" protocol="' . $this->cloud['protocol'] . '" />' . "\n";
    if ($this->ttl)
      $feedContent .= '    <ttl>' . $this->ttl . '</ttl>' . "\n";
    if ($this->image['url']) {
      $feedContent .= '    <image>' . "\n" .
              '      <title>' . $this->_clear_string($this->image['title']) . '</title>' . "\n" .
              '      <link>' . $this->_clear_url($this->image['link']) . '</link>' . "\n" .
              '      <url>' . $this->_clear_url($this->image['url']) . '</url>' . "\n";
      if ($this->image['width'] > 0)
        $feedContent .= '      <width>' . $this->image['width'] . '</width>' . "\n";
      if ($this->image['height'] > 0)
        $feedContent .= '      <height>' . $this->image['height'] . '</height>' . "\n";
      if ($this->image['description'])
        $feedContent .= '      <description>' . $this->_clear_string($this->image['description']) . '</description>' . "\n";
      $feedContent .= '    </image>' . "\n";
    }
    if ($this->textInput) {
      $feedContent .= '    <textInput>' . "\n" .
              '      <title>' . $this->_clear_string($this->textInput['title']) . '</title>' . "\n" .
              '      <description>' . $this->_clear_string($this->textInput['description']) . '</description>' . "\n" .
              '      <name>' . $this->_clear_string($this->textInput['name']) . '</name' . "\n" .
              '      <link>' . $this->_clear_url($this->textInput['link']) . '</link>' . "\n" .
              '    </textInput>' . "\n";
    }
    if (is_array($this->skipHours) && sizeof($this->skipHours) > 0) {
      $feedContent .= '    <skipHours>' . "\n";
      foreach ($this->skipHours as $hour) {
        $feedContent .= '      <hour>' . $hour . '</hour>' . "\n";
      }
      $feedContent .= '    </skipHours>' . "\n";
    }
    if (is_array($this->skipDays) && sizeof($this->skipDays) > 0) {
      $feedContent .= '    <skipDays>' . "\n";
      foreach ($this->skipHours as $day) {
        $feedContent .= '      <day>' . $day . '</day>' . "\n";
      }
      $feedContent .= '    </skipDays>' . "\n";
    }
    if (is_array($this->xElements) && sizeof($this->xElements) > 0) {
      foreach($this->xElements as $xtag => $xval) {
        $xtagE = $xtag;
        if (is_array($xval)) {
          foreach($xval as $xvalItem) {
            $feedContent .= '      <' . $xtag . '>' . $xvalItem . '</' . $xtagE . '>' . "\n";
          }
        } else {
          $feedContent .= '      <' . $xtag . '>' . $xval . '</' . $xtagE . '>' . "\n";
        }
      }
    }

    for ($i=0,$n=sizeof($this->item['title']);$i<$n;$i++) {
      $feedContent .= '    <item>' . "\n" .
              '      <title>' . $this->_clear_string($this->item['title'][$i]) . '</title>' . "\n" .
              '      <link>' . $this->_clear_url($this->item['link'][$i]) . '</link>' . "\n";
      if ($this->item['comments'][$i]) {
        if (is_array($this->item['comments'][$i])) {
          if (isset($this->item['comments'][$i]['url'])) {
            $feedContent .= '      <comments>' . $this->_clear_url($this->item['comments'][$i]['url']) . '</comments>' . "\n";
          }
          if (isset($this->item['comments'][$i]['rss'])) {
            $feedContent .= '      <wfw:commentRss>' . $this->_clear_url($this->item['comments'][$i]['rss']) . '</wfw:commentRss>' . "\n";
          }
          if (isset($this->item['comments'][$i]['count']) && $this->item['comments'][$i]['count'] > 0) {
            $feedContent .= '      <slash:comments>' . $this->item['comments'][$i]['count'] . '</slash:comments>' . "\n";
          }
        } else {
          $feedContent .= '      <comments>' . $this->_clear_url($this->item['comments'][$i]) . '</comments>' . "\n";
        }
      }
      if ($this->description_out == true && $this->item['description'][$i]) {
        $feedContent .= '      <description>' . $this->_clear_content($this->item['description'][$i], $this->description_out_max) . '</description>' . "\n";
//     if ($this->addContentTag == true) {
//        if (isset($xtags['content:encoded'])
//        $feedContent .= '      <content:encoded>' . '<![CDATA[' . $this->_clear_content($this->item['description'][$i], $this->description_out_max) . ']]>' . '</content>' . "\n";
      }
      if ($this->item['author'][$i]) {
        $feedContent .= '      <author>' . $this->_clear_email($this->item['author'][$i]) . '</author>' . "\n";
//        $feedContent .= '      <dc:creator>' . $this->_clear_email($this->item['author'][$i]) . '</dc:creator>' . "\n";
      }
      if ($this->item['enclosure'][$i] !== false && $this->item['enclosure'][$i] != '' && is_file(DIR_FS_CATALOG . DIR_WS_IMAGES . $this->item['enclosure'][$i])) {
        $imageinfo = getimagesize(DIR_FS_CATALOG . DIR_WS_IMAGES . $this->item['enclosure'][$i]);
        $feedContent .= '      <enclosure url="' . $this->_clear_url(HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . $this->item['enclosure'][$i]) . '" length="' . filesize(DIR_FS_CATALOG . DIR_WS_IMAGES . $this->item['enclosure'][$i]) . '" type="' . $imageinfo['mime'] . '" />' . "\n";
      }
      if ($this->item['guid'][$i]) {
        $feedContent .= '      <guid isPermaLink="' . ($this->item['guid'][$i]['PermaLink'] === true ? 'true' : 'false') . '">' . $this->_clear_url($this->item['guid'][$i]['url']) . '</guid>' . "\n";
      }
      if ($this->item['pubDate'][$i]) {
        $feedContent .= '      <pubDate>' . $this->_date($this->item['pubDate'][$i]) . '</pubDate>' . "\n";
      } else {
        $feedContent .= '      <pubDate>' . $this->_date() . '</pubDate>' . "\n";
      }
      if ($this->item['source'][$i]) {
        $feedContent .= '      <source url="' . $this->_clear_url($this->item['source'][$i]['url']) . '">' . $this->_clear_string($this->item['source'][$i]['name']) . '</source>' . "\n";
      }
      if ($this->item['category'][$i] !== false && is_array($this->item['category'][$i])) {
        foreach ($this->item['category'][$i] as $category) {
          if (isset($category['domain']) && $category['domain'] != '') {
            $feedContent .= '      <category domain="' . $this->_clear_url($category['domain']) . '">' . $this->_clear_string($category['name']) . '</category>' . "\n";
          } else {
            $feedContent .= '      <category>' . $this->_clear_string($category['name']) . '</category>' . "\n";
          }
        }
      }

      foreach($this->item['ext_tags'][$i] as $xtag => $xval) {
        $xtagE = $xtag;
        if (preg_match('@^(.*):(.*) type="(.*)"$@', $xtag, $m)) {
          $xtagE = $m[1] . ':' . $m[2];
//          var_dump($xtag, $xtagE, $m);echo '<br />';
        }
        if (is_array($xval)) {
          foreach($xval as $xvalItem) {
            $feedContent .= '      <' . $xtag . '>' . $this->_clear_content($xvalItem) . '</' . $xtagE . '>' . "\n";
          }
        } else {
          $feedContent .= '      <' . $xtag . '>' . $this->_clear_content($xval) . '</' . $xtagE . '>' . "\n";
        }
      }
      $feedContent .= '    </item>' . "\n";
    }
    $feedContent .= '  </channel>' . "\n" .
            '</rss>' . "\n";
    return $feedContent;
  }

  function rss_feed_out() {
    if ($this->rssFeedCahcheFrom == false) {
      $this->rssFeedContent = $this->rss_feed_content();
      $this->rssFeedTimeCreated = time();
      if ($this->rssFeedCahcheFlag == true) {
//        file_put_content($this->rssFeedCahcheFileName, $this->rssFeedContent);
        if (($f = fopen($this->rssFeedCahcheFileName, 'w'))) {
          fwrite($f, $this->rssFeedContent, strlen($this->rssFeedContent));
          fclose($f);
        }
      }
    }

//    @flush();
//    @ob_clean();
    header('Last-Modified: ' . gmdate("r", $this->rssFeedTimeCreated) . ' GMT');
    header('Expires: ' . gmdate("r", ($this->rssFeedTimeCreated+($this->ttl*60))) . ' GMT');
    header('Content-Type: ' . $this->content_type . '; charset=' . strtoupper($this->encoding) . '');
//    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
//    header("Content-disposition: inline; filename=rss.xml");
    echo $this->rssFeedContent;
  }

  function rss_feed_cache($zf_query, $time=false) {
    $this->rssFeedCahcheFrom = false;
    $this->rss_feed_cache_flush($time);
    $this->rssFeedCahcheFileName = DIR_FS_RSSFEED_CACHE . '/rssfeed_' . md5($zf_query);
    if ($this->rssFeedCahcheFlag && is_writable($this->rssFeedCahcheFileName)) {
      if (($this->rssFeedContent = file_get_contents($this->rssFeedCahcheFileName))) {
//        $this->rssFeedContent = unserialize($this->rssFeedContent);
        $this->rssFeedTimeCreated = filemtime($this->rssFeedCahcheFileName);
        $this->rssFeedCahcheFrom = true;
      }
    }
    return $this->rssFeedCahcheFrom;
  }

  function rssFeedCahcheSet($flag) {
    $this->rssFeedCahcheFlag = ($flag === true) ? true : false;
  }

  function rss_feed_cache_flush($time=false) {
    if ($za_dir = @dir(DIR_FS_RSSFEED_CACHE)) {
      clearstatcache();
      while ($zv_file = $za_dir->read()) {
        if (strpos($zv_file, 'rssfeed_') === 0) {
          if ($time == false || (time() - filemtime(DIR_FS_RSSFEED_CACHE . '/' . $zv_file)) > $time) {
            @unlink(DIR_FS_RSSFEED_CACHE . '/' . $zv_file);
          }
        }
      }
      $za_dir->close();
    }
  }

  function _clear_string($str) {
    $str = $this->_clear_problem_characters($str);
//    $str = html_entity_decode($str, ENT_QUOTES);
    $str = $this->_utf8_encode($str);
    $str = strip_tags($str);
    $str = htmlspecialchars($str);
    return $str;
  }

  function _clear_problem_characters($str) {
    $formattags = array("&");
    $replacevals = array("&#38;");
//    $str = str_replace($formattags, $replacevals, $str);
    $in = $out = array();
    $in[] = '@&(amp|#038);@i'; $out[] = '&';
    $in[] = '@&(#036);@i'; $out[] = '$';
    $in[] = '@&(quot);@i'; $out[] = '"';
    $in[] = '@&(#039);@i'; $out[] = '\'';
    $in[] = '@&(nbsp|#160);@i'; $out[] = ' ';
    $in[] = '@&(hellip|#8230);@i'; $out[] = '...';
    $in[] = '@&(copy|#169);@i'; $out[] = '(c)';
    $in[] = '@&(trade|#129);@i'; $out[] = '(tm)';
    $in[] = '@&(lt|#60);@i'; $out[] = '<';
    $in[] = '@&(gt|#62);@i'; $out[] = '>';
    $in[] = '@&(laquo);@i'; $out[] = '«';
    $in[] = '@&(raquo);@i'; $out[] = '»';
    $in[] = '@&(deg);@i'; $out[] = '°';
    $in[] = '@&(mdash);@i'; $out[] = '—';
    $in[] = '@&(reg);@i'; $out[] = '®';
    $in[] = '@&(–);@i'; $out[] = '-';
    $str = preg_replace($in, $out, $str);
    $str = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $str);
    return $str;
  }

  function _clear_html($str) {
    $in = $out = array();
    $in[] = '@<iframe[^>]*?>.*?</iframe>@si'; $out[] = "\n"; // Strip out iframe
    $in[] = '@<script[^>]*?>.*?</script>@si'; $out[] = "\n"; // Strip out javascript
    $in[] = '@<style[^>]*?>.*?</style>@siU'; $out[] = "\n"; // Strip style tags properly
    $in[] = '@<object[^>]*?>.*?</object>@siU'; $out[] = "\n"; // Strip out object
    $in[] = '@<![\s\S]*?–[ \t\n\r]*>@'; $out[] = "\n"; // Strip multi-line comments including CDATA
//    $in[] = '@<[\/\!]*?[^<>]*?'.'>@si'; $out[] = "\n"; // Strip out HTML tags
    $in[] = '/\s{2,}/'; $out[] = "\n"; // Strip multi-line comments including CDATA
    $str = preg_replace($in, $out, $str);

    $aDisabledAttributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseout', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $aDisabledAttributes = @implode('|', $aDisabledAttributes);
//    $str = preg_replace('/<(.*?)>/ie', "'<' . preg_replace(array('/javascript:[^\"\']*/i', '/(" . $aDisabledAttributes . ")[ \\t\\n]*=[ \\t\\n]*[\"\'][^\"\']*[\"\']/i', '/\s+/'), array('', '', ' '), stripslashes('\\1')) . '>'", $str );
    $pattern = '/\s+((' . $aDisabledAttributes . ')\s*=\s*\"[^\"]+\")/is';
    $str = preg_replace($pattern, '', $str);
    $pattern = '/\s+((style)\s*=\s*\"[^\"]+\")/is';
    $str = preg_replace($pattern, '', $str);

    return $str;
  }

  function _code2utf($str) {
    // cp1252 to utf8 table
    $trTable = array(
            "\x80" => "\xE2\x82\xAC",  // EURO SIGN
            "\x82" => "\xE2\x80\x9A",  // SINGLE LOW-9 QUOTATION MARK
            "\x83" => "\xC6\x92",      // LATIN SMALL LETTER F WITH HOOK
            "\x84" => "\xE2\x80\x9E",  // DOUBLE LOW-9 QUOTATION MARK
            "\x85" => "\xE2\x80\xA6",  // HORIZONTAL ELLIPSIS
            "\x86" => "\xE2\x80\xA0",  // DAGGER
            "\x87" => "\xE2\x80\xA1",  // DOUBLE DAGGER
            "\x88" => "\xCB\x86",      // MODIFIER LETTER CIRCUMFLEX ACCENT
            "\x89" => "\xE2\x80\xB0",  // PER MILLE SIGN
            "\x8A" => "\xC5\xA0",      // LATIN CAPITAL LETTER S WITH CARON
            "\x8B" => "\xE2\x80\xB9",  // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
            "\x8C" => "\xC5\x92",      // LATIN CAPITAL LIGATURE OE
            "\x8E" => "\xC5\xBD",      // LATIN CAPITAL LETTER Z WITH CARON
            "\x91" => "\xE2\x80\x98",  // LEFT SINGLE QUOTATION MARK
            "\x92" => "\xE2\x80\x99",  // RIGHT SINGLE QUOTATION MARK
            "\x93" => "\xE2\x80\x9C",  // LEFT DOUBLE QUOTATION MARK
            "\x94" => "\xE2\x80\x9D",  // RIGHT DOUBLE QUOTATION MARK
            "\x95" => "\xE2\x80\xA2",  // BULLET
            "\x96" => "\xE2\x80\x93",  // EN DASH
            "\x97" => "\xE2\x80\x94",  // EM DASH
            "\x98" => "\xCB\x9C",      // SMALL TILDE
            "\x99" => "\xE2\x84\xA2",  // TRADE MARK SIGN
            "\x9A" => "\xC5\xA1",      // LATIN SMALL LETTER S WITH CARON
            "\x9B" => "\xE2\x80\xBA",  // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
            "\x9C" => "\xC5\x93",      // LATIN SMALL LIGATURE OE
            "\x9E" => "\xC5\xBE",      // LATIN SMALL LETTER Z WITH CARON
            "\x9F" => "\xC5\xB8"       // LATIN CAPITAL LETTER Y WITH DIAERESIS
    );
    $str = strtr($str, $trTable);
    return $str;
  }

  function _utf8_encode($str) {
    if (is_null($this->convert_to_utf8)) {
      $this->convert_to_utf8 = ($this->encoding == 'utf-8' && $this->source_encoding != 'utf-8');
    }
    if ($this->convert_to_utf8 === true) {
      if (preg_match('@[\x7f-\xff]@', $str)) {
        $str = iconv($this->source_encoding, $this->encoding, $str);
//      } else {
//        $str = utf8_encode($str);
      }
      if ($this->source_encoding == 'iso-8859-1') {
        $str = preg_replace('@&#x([0-9a-f]+);@ei', '$this->_code2utf(chr(hexdec("\\1")))', $str);
        $str = preg_replace('@&#([0-9]+);@e', '$this->_code2utf(chr("\\1"))', $str);
      }
    }
    return $str;
  }

  function _expand_url_callback($str) {
    $out = $str[0];
    $url_parts = parse_url($str[2]);
    if (!isset($url_parts["scheme"])) {
      $self_url = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
      $out = $str[1] . '=' . $str[3] . 'http://' . $_SERVER['HTTP_HOST'] . $str[2] . $str[3];
    }
    return $out;
  }

  function _expand_url($str) {
      // rewrite all local urls to absolute urls
    $pattern = '(<a\s([^>]*?)href\s*=\s*[\'"](.*?)[\'"]([^>]*)>)(.*?)</a\s*>';
    $pattern = '(href|src|action)\s*=\s*[\'"]{0,1}(\.{0,2}[\\|\/]{1})(.*?)[\'"]{0,1}( .*?){0,1}>';
    $pattern = '(href|src|action)\s*=\s*[\'"](.*?)([\'"])';
    $str = preg_replace_callback('@' . $pattern . '@is', array($this, "_expand_url_callback"), $str);
    return $str;
  }

  function _clear_content($str, $max_leng=0) {
    $str = $this->_clear_html($str);

    $str = $this->_expand_url($str);

//    $str = html_entity_decode($str);
//    $str = $this->_clear_problem_characters($str);

    if ($this->source_encoding == 'iso-8859-1' && preg_match('@[\x7f-\xff]@', $str)) {
      $str = preg_replace('@([\x80-\x9f])@se', "'&#' . (ord('$1')) . ';'", $str);
    }
    $str = $this->_utf8_encode($str);

    $str = trim($str);

    if ($max_leng > 0 && mb_strlen($str) > $max_leng) {
      $str = mb_substr($str, 0, $max_leng) . '...';
    }

    if (preg_match( "/['\"\[\]<>&]/", $str)) {
      if (RSS_STRIP_TAGS == 'true') {
        $st_in[] = '@<br>@i'; $st_out[] = "\n";
        $st_in[] = '@<br />@i'; $st_out[] = "\n";
        $st_in[] = '@<br/>@i'; $st_out[] = "\n";
        $st_in[] = '@<li>@i'; $st_out[] = "\n•".chr(160);
        $st_in[] = '@<h\d>@i'; $st_out[] = "\n\n";
        $st_in[] = '@</h\d>@i'; $st_out[] = "\n\n";
        $st_in[] = '@</p>@i'; $st_out[] = "\n";
        $st_in[] = '@<p>@i'; $st_out[] = "\n";

        $str = preg_replace($st_in, $st_out, $str);
        $str = strip_tags($str);
        $str = htmlspecialchars($str, ENT_QUOTES);
      } else {
        $str = '<![CDATA[' . trim($str) . ']]>';
      }
    }


    if ($str == '') $str = false;

    return $str;

  }

  function _clear_url($url) {
    $url_parts = parse_url($url);
    $out = '';
    if (!isset($url_parts["scheme"])) $url_parts["scheme"] = 'http';
    $out .= $url_parts["scheme"] . '://';
    if (isset($url_parts["host"])) $out .= $url_parts["host"];
    if (isset($url_parts["port"])) $out .= ':' . $url_parts["port"];
    if (isset($url_parts["path"])) {
      $pathinfo = pathinfo($url_parts["path"]);
      if (!isset($pathinfo["dirname"]) || $pathinfo["dirname"] == '\\' || $pathinfo["dirname"] == '.') $pathinfo["dirname"] = '';
      $out .= rtrim($pathinfo["dirname"], '/') . '/';
      if ($pathinfo["basename"] != '') {
        $out .= str_replace('&', '%26', rawurlencode($pathinfo["basename"]));
      }
    }
    if (isset($url_parts["query"])) {
      $url_parts["query"] = str_replace('&amp;', '&', $url_parts["query"]);
      $url_parts["query"] = str_replace('&&', '&', $url_parts["query"]);
      $url_parts["query"] = str_replace('&', '&amp;', $url_parts["query"]);
      $out .= '?' . $url_parts["query"];
    }
    if (isset($url_parts["fragment"])) $out .= '#' . $url_parts["fragment"];
    $out = $this->_utf8_encode($out);
    return $out;
  }

  function _clear_email($str) {
    $str = str_replace(array('<', '>'), array('(', ')'), $str);
    $str = htmlspecialchars($str, ENT_QUOTES);
    $str = $this->_utf8_encode($str);
    return $str;
  }

  function _date($time=false) {
    if ($time === false) {
      $time = time();
    }
    if (!is_numeric($time)) {
      $time = strtotime($time);
    }
//    $date = gmdate('r', $time) . ' GMT';
    $date = date(DATE_RSS, $time);
    return $date;
  }

}

// EOF