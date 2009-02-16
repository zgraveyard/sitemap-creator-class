<?php
/**
 * sitemap Creator
 *
 * PHP versions 5
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   class
 * @package    sitemap
 * @author     Mhd Zaher Ghaibeh <zaher@mhdzaherghaibeh.name>
 * @copyright  2009 Mhd Zaher Ghaibeh
 * @license    http://www.gnu.org/licenses/gpl.html  GPL V 2.0
 * @version    CVS: $Id: sitemap.php,v 0.9 2009/02/14 cellog Exp $
 */
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