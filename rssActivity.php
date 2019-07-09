
$dictionary = [
	'ESPN' => [
		'https://www.espn.com/espn/rss/news',
		'http://www.espnfc.com/podcasts/rss/_/method/itunes/type/espnfcpodcast'],
	'NY Times' => 'https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml',
	'Jeffe Kennedy' => 'http://blog.jeffekennedy.com/feed/',
	'Hacker News' => 'https://hnrss.org/newcomments',
	'Under the Skin' => 'https://feeds.megaphone.fm/LM1344278906',
];

rssActivity($dictionary,'ESPN',3);

/**
 * Grabs RSS feed URL from dictionary and specifies whether there has been any activity for a given number of days.
 * @param array $dictionary the dictionary keyed by company name and valued by RSS feed URL
 * @param string $company the company whose activity is being checked
 * @param int $days the number of days to check for activity 
 */
function rssActivity($dictionary, $company, $days)
{
	// grab url from dictionary
	$url = $dictionary[$company];

	// if company has multiple feeds, loop through each url
	if(is_array($url))
	{
		$count = [];

		foreach($url as $u)
		{
			$day_diff = grabFromFeed($u);
			$count[] = $day_diff;
		}

		// find number of days since most recent post
		$min = min($count);

		echo 'There has '.($min > $days ? 'not ' : '').'been activity in the past '.$days.' day(s). Company was last active '.$min.' day(s) ago.';
	}
	else
	{
		$day_diff = grabFromFeed($url);

		echo 'There has '.($day_diff > $days ? 'not ' : '').'been activity in the past '.$days.' day(s). Company was last active '.$day_diff.' day(s) ago.';
	}
}

/**
 * Looks into the most recent post from an RSS feed URL and calculates the number of days since last activity.
 * @param string $url the RSS feed URL
 * @return int $day_diff number of days since last activity
 */
function grabFromFeed($url)
{
	//grab contents, make SimpleXMLElement
	$content = file_get_contents($url);
	$feed = new \SimpleXMLElement($content);

	// grab the most recent post's publish date
	$channel = $feed->channel;
	$first = $channel->item[0]->pubDate;

	// convert now and publish date into epoch time
	$now = time();
	$pub_date = strtotime($first);

	// calculate the difference in epoch
	$epoch_diff = $now - $pub_date;

	// convert seconds to days (60 sec per minute, 60 min per hr, 24 hr per day)
	$day_diff = round($epoch_diff / (60*60*24));

	return $day_diff;
}
