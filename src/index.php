<?
// example
include_once dirname(__FILE__) . '/twitter2rss.class.php';
$screenName = 'YourTwitterAccountName';
$options = array(
	'keys'=>array(
		'CONSUMER_KEY'=>'CONSUMERKEYGOESHERE',
		'CONSUMER_SECRET'=>'CONSUMERSECRETGOESHERE',
		'ACCESS_TOKEN'=>'ACCESSTOKENGOESHERE',
		'ACCESS_SECRET'=>'ACCESSSECRETGOESHERE',
	),
	'cache_dir'=>'./cache/', // default: ./cache/  (optional)
	'cache_time'=>10, // minutes; default: 15   (optional)
	'limit'=>10, // default: 20  (optional)
	
);

$rss = new twitter2rss($screenName, $options);
$rss->render();

?>
