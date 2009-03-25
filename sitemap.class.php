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
 * @version    CVS: $Id: sitemap.php,v 0.9.0.3 2009/03/01 cellog Exp $
 */

class sitemap {

	private $file_name = 'sitemap.xml'; // the sitemap.xml file which is usually sitemap.xml
    private $version ='0.9.0.3'; // the version of the class
	public $siteUrl; // the site url
	public $siteDir; // you have to put your full dirctory like /var/www/home/site/
	public $proxy; // the proxy for the isp or the hoster
	public $proxy_port; // the port for the isp proxy or the hoster 
    private $search_eng = array('http://www.google.com/webmasters/sitemaps/ping','http://submissions.ask.com/ping','http://webmaster.live.com/ping.aspx'); // an array with the search engines url.
	function __constructor(){
		$this->file_name= $file_name;
		$this->siteUrl = '';
		$this->siteDir = '';
		$this->proxy = NULL;
		$this->proxy_port = NULL;
        $this->search_eng = $search_eng;
	}
/*
 * prepare function , will get the siteUrl
 * and make sure that the files are ready to be written
 * @param $siteUrl string which is the full site url 
 */
	public function prepare($siteUrl){
		$this->siteUrl = $siteUrl;
		if(!file_exists($this->siteDir.'/'.$this->file_name)){
			$handle = fopen($this->siteDir.'/'.$this->file_name,'w');
			fwrite($handle,$this->writeFirst());
		}else{
			@unlink($this->siteDir.'/'.$this->file_name);
			@unlink($this->siteDir.'/'.$this->file_name.'.gz');
			$handle = fopen($this->siteDir.'/'.$this->file_name,'w');
			fwrite($handle,$this->writeFirst());
		}
		fclose($handle);
		return true;
	}

/*
 * writeFirst function will add the first few lines of code to the sitemap.xml file
 */
	private function writeFirst(){
		$this->defaultData = "<?xml version='1.0' encoding='UTF-8'?>
<!-- sitemap-generator-program='sitemap-creator-class' sitemap-generator-version='$this->version' -->
<!-- programmed-by='Mhd Zaher Ghaibeh' programmer-email='zaher@mhdzaherghaibeh.name' -->
<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>
</urlset>";
		return $this->defaultData;
	}

/*
 * addElements function , will add the required elements to the xml node
 * which we are building.
 * @param $elementArray array the elements array
 * @return true boolian when done.
 */
	public function addElements($elementArray){
		$xml = new SimpleXMLElement($this->writeFirst());
		$count = count($elementArray);
		for($i=0;$i<$count;$i++){
			$data = $xml->addChild('url');
			$data->addChild('loc',htmlentities($elementArray[$i]['loc']));
			$data->addChild('lastmod', $elementArray[$i]['lastmod']);
			$data->addChild('changefreq',$elementArray[$i]['changefreq']);
			$data->addChild('priority',$elementArray[$i]['priority']);
		}
		$this->write($xml->asXML());
		$this->submit($this->search_eng);
		$this->genGZ();
		$this->genRobot();
		return true;
	}
/*
 * write function write the conetnt to the sitemap.xml file
 * but be noticed that the old one will be deleted
 * @param $content string , which is fomrmated as XML content
 * @return true boolian when done 
 */
	private function write($content){
		if(!file_exists($this->siteDir.'/'.$this->file_name)){
			$handle = fopen($this->siteDir.'/'.$this->file_name,'w');
			fwrite($handle,$content);
		}else{
			@unlink($this->siteDir.'/'.$this->file_name);
			$handle = fopen($this->siteDir.'/'.$this->file_name,'w');
			fwrite($handle,$content);
		}
		fclose($handle);
		return true;
	}

/*
 * submit function, will submitted the sitemap url to google right now
 * @param $site string : the url to google sitemap ping services.
 */
    private function submit($sites = 'http://www.google.com/webmasters/sitemaps/ping')
    {
    	if(is_array($sites)){
            foreach($sites as $site){
                $url = $site.'?sitemap='.htmlentities($this->siteUrl.'/'.$this->file_name).'';
                $result = $this->fetch_remote_file($url,$this->proxy,$this->proxy_port);
                $code = $result->status;
                if ($code != 200) {
                     print('Error while submitting the file :
                           '.$result->error.' to '.$site.' <br />');
                }elseif($code == 200){
                    print $this->siteUrl.'/'.$this->file_name.
                          ' has been submitted successfuly to '.$site.'<br />';
                }
            }
        }else{
            $url = $sites.'?sitemap='.htmlentities($this->siteUrl.'/'.$this->file_name).'';
            $result = $this->fetch_remote_file($url,$this->proxy,$this->proxy_port);
            $code = $result->status;
            if ($code != 200) {
                 print('Error while submitting the file : '.$result->error.'<br />');
            }elseif($code == 200){
                print $this->siteUrl.'/'.$this->file_name.
                      ' has been submitted successfuly to '.$sites.'<br />';
            }
        }
    }

/*
 * fetch_remote_file function , fetch the result of the url we sent
 * @param $url string : the url which we want to get the result from
 * @param $proxyHost string : the proxy which is used in the user internet connection , i heard that godaddy users must use one
 * @param $proxyPort string : the port of the proxy which we are using .
 * @param $headers string : the headers that we are going to sent with our http request.
 * @return $client string: the result of the fecthing.
 */
	private function fetch_remote_file ($url, $proxyHost= NULL , $proxyPort = NULL,$headers = "" ) {
		require_once('Snoopy.class.inc');
		$client = new Snoopy();
		$client->rawheaders["Pragma"] = "no-cache";
		$client->agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/3.0.5';
		if(($proxyHost != NULL) && ( $proxyPort != NULL )){
			$client->proxy_host = $proxyHost;
			$client->proxy_port = $proxyPort;
		}
		@$client->fetch($url);
		return $client;
	}

/*
 * genGZ function , is used to gzip the sitemap.xml file
 * but be noticed that the old one will be deleted
 */
    private function genGZ(){
		$command = 'gzip -fc9 '.$this->siteDir.'/'.$this->file_name.' > '.$this->siteDir.'/'.$this->file_name.'';
		system($command,$reval);
        if($reval != FALSE){
            print('an error in generating the gz file.<br />');
        }
    }

/*
 * genRobot function , is used to generate the robots.txt file.
 * but be noticed that the old one will be deleted
 */
    private function genRobot(){
    	global $siteConfig;
 		$content ="User-Agent: *
Allow: /*
Disallow : /admin/*
Sitemap: ".$this->siteUrl."/".$this->file_name."
Sitemap: ".$this->siteUrl."/".$this->file_name.".gz";
		if(!file_exists($this->siteDir.'/robots.txt')){
			$handle = fopen($this->siteDir.'/robots.txt','w');
			fwrite($handle,$content);
		}else{
			@unlink($this->siteDir.'/robots.txt');
			$handle = fopen($this->siteDir.'/robots.txt','w');
			fwrite($handle,$content);
		}
		fclose($handle);
		return true;
    }
}
?>