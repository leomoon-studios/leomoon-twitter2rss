<?php
######################################################################################################################################
#                                                        LeoMoon  Twitter2RSS                                                        #
######################################################################################################################################
/*
## Written by
  - Amin Babaeipanah
  - Arash Soleimani

## Changelog
  - 1.2: Twitter2RSS is now a class (twitter2rss.class.php)
  - 1.1: Cache added by Arash Soleimani and cron feature was removed
  - 1.0: First release with no cache (cron based)

## How to install
  - 00: If you have any problems, you can look at index.php example file.
  - 01: Sign up for Twitter developer account with your twitter login @: https://dev.twitter.com/apps
  - 02: Create a new application
  - 03: Fill "Name", "Description", and "Website". "Website" must be the same website where this script will be installed...
  - 04: Click on your newly created application
  - 05: Make sure "Access" is "Read only"
  - 06: Make sure "Callback URL" is the same as "Website" in step 3
  - 07: Click "Update this Twitter application's settings
  - 08: Click "OAuth tool" tab and get "Consumer key", "Consumer secret", "Access token", and "Access token secret" and paste them accordingly below...
  - 09: Upload "index.php", "OAuth.php", and "twitteroauth.php", and "cache" folder to your webserver. Something like below:
      * www.yourwebsite.com/twitter2rss/index.php
      * www.yourwebsite.com/twitter2rss/OAuth.php
      * www.yourwebsite.com/twitter2rss/twitteroauth.php
      * www.yourwebsite.com/twitter2rss/cache/
  - 10: Go to "www.yourwebsite.com/twitter2rss/index.php" and your rss will be genrated
  - 11: If "www.yourwebsite.com/twitter2rss/cache/" is empty, then change permission of "cache" folder to 777.
*/
######################################################################################################################################
#                                                        EDIT VARIABLES BELOW                                                        #
######################################################################################################################################
include_once dirname(__FILE__) . '/twitteroauth.php';
class twitter2rss {
	var $CONSUMER_KEY = '';
	var $CONSUMER_SECRET = '';
	var $ACCESS_TOKEN = '';
	var $ACCESS_SECRET = '';
	var $screen_name = '';
	var $cache_dir = './cache/';
	var $cache_file = '';
	var $cache_time = 15;
	var $limit = 20;
	function __construct($screenName, $options = null){
		$this->screen_name = $screenName;
		if(isset($options['keys'])){
			$this->CONSUMER_KEY = $options['keys']['CONSUMER_KEY'];
			$this->CONSUMER_SECRET = $options['keys']['CONSUMER_SECRET'];
			$this->ACCESS_TOKEN = $options['keys']['ACCESS_TOKEN'];
			$this->ACCESS_SECRET = $options['keys']['ACCESS_SECRET'];
		}
		if(isset($options['cache_dir'])){
			$this->cache_dir = $options['cache_dir'];
			$this->cache_file = $this->cache_dir.md5($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
		if(isset($options['cache_time'])){
			$this->cache_time = $options['cache_time'];
		}
		if(isset($options['limit'])){
			$this->limit = $options['limit'];
		}
		
		
	}
	function render(){
		if (file_exists($this->cache_file) && (filemtime($this->cache_file) > (time() - 60 * $this->cache_time ))) {
			header('Content-Type:text/xml;charset=utf-8');
			echo file_get_contents($this->cache_file);
		}else{
			$twitter = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $this->ACCESS_TOKEN, $this->ACCESS_SECRET);
			$request = $twitter->get('statuses/user_timeline',array('screen_name'=>$this->screen_name,'count'=>$this->limit));
			if(!isset($request->error)) {
				$rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				$rss.= '<rss version="2.0">' . "\n";
				$rss.= '    ' . '<channel>' . "\n";
				$rss.= '        '.'<title>@'.$screen_name.' Twitter Feed</title>'."\n";
				$rss.= '        '.'<link>https://twitter.com/'.$screen_name.'</link>'."\n";
				$rss.= '        '.'<description>Null</description>'."\n";
				$rss.= '        '.'<language>en-us</language>'."\n";
				$rss.= '        '.'<lastBuildDate>'.date('r').'</lastBuildDate>'."\n";
				if(count($request)>0) {
					foreach($request as $tweet) {
						$rss .= '        ' . '<item>' . "\n";
						$rss .= '            ' . '<title>' . $tweet->text . '</title>' . "\n";
						$rss .= '            ' . '<description><![CDATA[' . $tweet->text . ']]></description>' . "\n";
						$rss .= '            ' . '<guid>http://www.twitter.com/' . $tweet->user->screen_name . '/statuses/' . $tweet->id_str . '</guid>' . "\n";
						$rss .= '            ' . '<link>http://www.twitter.com/' . $tweet->user->screen_name . '/statuses/' . $tweet->id_str . '</link>' . "\n";
						$rss .= '            ' . '<pubDate>'.date('r',strtotime($tweet->created_at)).'</pubDate>' . "\n";
						$rss .= '        ' . '</item>' . "\n";
					}
				}
				$rss.= '    ' . '</channel>' . "\n";
				$rss.= '</rss>' . "\n";
				file_put_contents($this->cache_file, $rss, LOCK_EX);
				header('Content-Type:text/xml;charset=utf-8');
				echo $rss;
			}
		}
	}
}
?>
