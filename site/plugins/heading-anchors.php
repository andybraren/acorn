<?php

// Heading Anchor Plugin
// created by fitzage
// https://forum.getkirby.com/t/plugin-to-make-header-tags-linkable/3469
// modified Andy Braren
// Auto-generates anchors for every heading 

// CHANGELOG
// 2015-12-08 - Initial
// 2017-11-11 - Rewrote mostly everything to reduce complexity. Also fixed Amazon URLs to support ones with the product name in the URL.

// Anchor ID generator
// Uses the text of headings to create nice anchor IDs

kirbytext::$post[] = function($kirbytext, $value) {
  $value = preg_replace_callback('#<(h[1-3]).*>(.*?)<\/(h[1-3])>#', 'newID', $value);
  return $value;
};

function newID($match) {
  
  $delete = array(':','(',')','?','.','!','$',',','%','^','&',"'",';','"','[',']','{','}','|','`','#');
  $hyphenate = array(' ','~','@','*','+','=','/','>','<',' - ',' / ');
  
  list($all, $h, $headingtext) = $match;
  
  $id = str_replace($delete, '', $headingtext);
  $id = str_replace($hyphenate, '-', $id);
  $id = strtolower($id);
    
  return '<' . $h . ' id="' . $id . '">' . $headingtext . '</' . $h . '>';
}

// Amazon affiliate links
// Adds affiliate parameter to all Amazon links (currently only one format, the one that it should be)

kirbytext::$post[] = function($kirbytext, $value) {
  //$value = preg_replace_callback('/https?:\/\/(www\.)?amazon\.com\/([a-zA-Z0-9\/]*)/', 'add_affiliate', $value);
  //$value = preg_replace_callback('#https:\/\/(www\.)?amazon\.com\/(.*?)\/(dp\/|gp\/)?([a-zA-Z0-9\/]*)\/(.*?)"#', 'add_affiliate', $value);
  
  $value = preg_replace_callback('/"https:\/\/(www\.)?amazon\.com\/(.*)"/', 'add_amazon_affiliate', $value);
  
  return $value;
};

function add_amazon_affiliate($match) {
  
  // Get the product name
  // Only works for .com right now, maybe reversing the string would allow me to not have to use it as an anchor
  preg_match('#(?:\.com\/)(.*)\/(?:dp|gp)#', $match[0], $productname);
  $name = (isset($productname[1])) ? $productname[1] . '/' : '';
  
  // Get the region
  preg_match('#\/(dp|gp)\/#', $match[0], $region);
  $region = (isset($region[1])) ? $region[1] . '/' : '';
  
  // Get the ASIN
  // http://www.sebastianviereck.de/en/php-ueberpruefen-ob-ein-string-eine-valide-asin-ist/
  preg_match('#B[0-9]{2}[0-9A-Z]{7}|[0-9]{9}(X|0-9])#', $match[0], $asinmatch);
  $asin = $asinmatch[0] ?? '';
  
  // Grabs any existing Associate ID
  preg_match('#\?tag=(.*?)(?:&|")#', $match[0], $idmatch);
  if (isset($idmatch[1])) {
    $id = '?tag=' . $idmatch[1];
  } elseif ($settingid = site()->setting('monetization/affiliate/amazon')) {
    $id = '?tag=' . $settingid;
  } else {
    $id = '';
  }
  
  /*
  Test URLs
  
  https://regex101.com/
  
  https://www.amazon.com/dp/B0156KULRA
  
  "https://www.amazon.com/gp/product/B071S84ZW7?tag=tinkertryamzn-20"
  
  "https://www.amazon.com/gp/product/B071S84ZW7?tag=tinkertryamzn-20&whatever=shshs?blah=dhdh"
  
  https://www.amazon.com/gp/product/B0721NP5XK/ref=s9u_newr_gw_i3?ie=UTF8&fpl=fresh&pd_rd_i=B0721NP5XK&pd_rd_r=102e798a-c703-11e7-b7d1-efea25c04d71&pd_rd_w=XhVih&pd_rd_wg=JBKTY&pf_rd_m=ATVPDKIKX0DER&pf_rd_s=&pf_rd_r=Y7393VSVEZM54QBHCEAR&pf_rd_t=36701&pf_rd_p=1cf9d009-399c-49e1-901a-7b8786e59436&pf_rd_i=desktop
  
  https://www.amazon.com/Lenovo-ThinkPad-Docking-Station-0A33970/dp/B008ABKADI
  
  https://www.amazon.com/Apple-iPhone-GSM-Unlocked-5-8/dp/B075QNGDZL/ref=sr_tr_2?s=wireless&ie=UTF8&qid=1510430014&sr=1-2&keywords=iphone&th=1
  
  "https://www.amazon.com/Apple-iPhone-GSM-Unlocked-5-8/gp/B075QNGDZL/ref=sr_tr_2?s=wireless&ie=UTF8&qid=1510430014&sr=1-2&keywords=iphone&th=1"
  
  https://www.amazon.com/dp/B001CMV1VW/?tag=thewire06-20&linkCode=xm2&ascsubtag=AgEAAAAAAAAAAQME
*/  

}
















// Amazon affiliate links
// Adds affiliate parameter to all Amazon links (currently only one format, the one that it should be)
/*
kirbytext::$pre[] = function($kirbytext, $value) {
  $value = preg_replace_callback('/https?:\/\/(www\.)?amazon\.com\/([a-zA-Z0-9\/]*)/', 'add_affiliate', $value);
  return $value;
};
*/

/*
kirbytext::$post[] = function($kirbytext, $value) {
  return preg_replace_callback('!\(toc\)!', function($match) use($value) {
    preg_match_all('!<h2>(.*)</h2>!', $value, $matches);
    $ul = brick('ul');
    $ul->addClass('toc');
    foreach($matches[1] as $match) {
      $li = brick('li', '<a href="#' . str::slug($match) . '">' . $match . '</a>');
      $ul->append($li);
    }
    return $ul;
  }, $value);
};
*/

/*
// Adds ID attributes to all headers that are in fields processed by kirbytext.
function retitle($match) {
    // Characters in the $delete array will be removed
    // Characters in the $hyphenate array will be changed to hyphens
    $delete = c::get('headid-delete', array(':','(',')','?','.','!','$',',','%','^','&',"'",';','"','[',']','{','}','|','`','#'));
    $hyphenate = c::get('headid-hyphenate', array(' ','~','@','*','+','=','/','>','<'));
    list($_unused, $h2, $title) = $match;
    preg_match('/id=\"(.*)\"/',$_unused,$idmatches);
    preg_match('/name=\"(.*)\"/',$_unused,$namematches);
    if (empty($idmatches) && empty($namematches)) {
        $id = strip_tags($title);
        $id = strtolower(str_replace($delete,'',str_replace($hyphenate,'-',$id)));
        $id = preg_replace('/<\/?a[^>]*>/','',$id);
        return "<$h2 id='$id'><a href='#$id'>$title</a></$h2>";
    } elseif (!empty($idmatches) && empty($namematches)) {
        return "<$h2 $idmatches[0]><a href='#$idmatches[1]'>$title</a></$h2>";
    } elseif (empty($idmatches) && !empty($namematches)) {
        return "<$h2 id='$namematches[1]'><a href='#$namematches[1]'>$title</a></$h2>";
    }
}

// These filters run after all markdown and kirbytext is processed.
kirbytext::$post[] = function($kirbytext, $value) {
    $value = preg_replace_callback("#<(h[1-6]).*>(.*?)</\\1>#", "retitle", $value);
    return $value;
};
*/


// Replace brackets in kirbytags with parenthesis
// https://forum.getkirby.com/t/way-to-use-parenthesis-inside-kirbytag/861/4
/*
kirbytext::$post[] = function($kirbytext, $value) {
  $snippets = array(
    '[' => '(',
    ']' => ')',
  );
  $keys     = array_keys($snippets);
  $values   = array_values($snippets);
  return str_replace($keys, $values, $value);
};
*/