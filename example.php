<?php
include('sitemap.class.php');
$map = array();
$articles = array(
array("id"=>1,"created_date"=>"2009-01-24"),
array("id"=>2,"created_date"=>"2009-01-24"),
array("id"=>3,"created_date"=>"2009-01-24"),
array("id"=>4,"created_date"=>"2009-01-24"),
);
//collecting the category section
if(is_array($articles)){
  $countarticles= count($articles);
  for($i=0;$i< $countarticles;$i++){
  $map[]=array(
			"loc"=>'http://www.domain.com/article.php?id='.$articles[$i]['id'].'',
			"lastmod"=>$articles[$i]['created_date'],
			"changefreq"=>'monthly',
			"priority"=>'1.0'
		);
  }
}
$siteMap = new sitemap();
$siteMap->prepare();
$siteMap->siteUrl = 'http://www.domain.com';
$siteMap->siteDir = $_SERVER['DOCUMENT_ROOT'];
$siteMap->proxy='proxy.isp.net'; // use if the proxy is enabled in your ISP , use NULL in your site
$siteMap->proxy_port='3311'; // use if the proxy is enabled in your ISP , use NULL in your site
if(!$siteMap->addElements($map)){
die('error');	
};
?>