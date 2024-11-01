<?php
function ytnf_get_youtube_id($url,$type='video')
{ 
    $video_id = false;
    $url = parse_url($url);
	switch ($type)
	{
		case "video":
			if (strcasecmp($url['host'], 'youtu.be') === 0)
			{
				$video_id = substr($url['path'], 1);
			}
			elseif (strcasecmp($url['host'], 'www.youtube.com') === 0)
			{
				if (isset($url['query']))
				{
					parse_str($url['query'], $url['query']);
					if (isset($url['query']['v']))
					{
						$video_id = $url['query']['v'];
					}
				}
				if ($video_id == false)
				{
					$url['path'] = explode('/', substr($url['path'], 1));
					if (in_array($url['path'][0], array('e', 'embed', 'v')))
					{
						$temp = explode('&',$url['path'][1]);
						$video_id = $temp[0];
					}
				}
			}
			break;
		
		case "playlist":
			if (strcasecmp($url['host'], 'www.youtube.com') === 0)
			{
				if (isset($url['query']))
				{
					parse_str($url['query'], $url['query']);
					if (isset($url['query']['list']))
					{
						$video_id = $url['query']['list'];
					}
				}
				if ($video_id == false)
				{
					$url['path'] = explode('/', substr($url['path'], 1));
					if (in_array($url['path'][0], array('e', 'embed', 'v')))
					{
						$temp = explode('&',$url['path'][1]);
						$video_id = $temp[0];
					}
				}
			}
			break;
	}
    return strip_tags($video_id);
}

function ytnf_is_valid_youtube_video($url,$type)
{
	switch ($type)
	{
		case "video":
			$headers = get_headers('http://gdata.youtube.com/feeds/api/videos/' . ytnf_get_youtube_id($url,'video'));
			if (!strpos($headers[0], '200')) 
			{
				global $youtube_debug_mode;
				if($youtube_debug_mode)
				{
					$to = 'mindstien@gmail.com';
					$subject = 'Youtube Debug Log: "'.get_bloginfo('name').'"';
					$message = "<p>url=[$url]<p>";
					$message .= "<p>Video ID =[".ytnf_get_youtube_id($url,'video')."]<p>";
					$message .= "<p>type=[$type]<p>";
					$test = wp_remote_get('http://gdata.youtube.com/feeds/api/videos/' . ytnf_get_youtube_id($url,'video'));
					$message .= "<p>youtube response=[".print_r($test,true)."]<p>";
					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					wp_mail( $to, $subject, $message);
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
				}
				return false;
			}
			break;
		
		case "playlist";
			$headers = get_headers('http://gdata.youtube.com/feeds/api/playlists/' . ytnf_get_youtube_id($url,'playlist').'?v=2');
			if (!strpos($headers[0], '200')) 
			{
				global $youtube_debug_mode;
				if($youtube_debug_mode)
				{
					$to = 'mindstien@gmail.com';
					$subject = 'Youtube Debug Log: "'.get_bloginfo('name').'"';
					$message = "<p>url=[$url]<p>";
					$message .= "<p>Video ID =[".ytnf_get_youtube_id($url,'playlist')."]<p>";
					$message .= "<p>type=[$type]<p>";
					$test = wp_remote_get('http://gdata.youtube.com/feeds/api/playlists/' . ytnf_get_youtube_id($url,'playlist').'?v=2');
					$message .= "<p>youtube response=[".print_r($test,true)."]<p>";
					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
					wp_mail( $to, $subject, $message);
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
				}
				return false;
			}
			break;
	}
	return true;
}

function ytnf_get_youtube_embed_codes_from_html($html)
{
	$youtube_urls = array();
	libxml_use_internal_errors(true);
	$document = new DOMDocument();
    $document->loadHTML($html);
    $lst = $document->getElementsByTagName('iframe');
	for ($i=0; $i<$lst->length; $i++) {
        $iframe= $lst->item($i);
		if(strpos($iframe->attributes->getNamedItem('src')->value,'videoseries'))
			$youtube_urls[strip_tags($iframe->attributes->getNamedItem('src')->value)] = "playlist";

    }
	
	$lst = $document->getElementsByTagName('iframe');
	
	for ($i=0; $i<$lst->length; $i++) {
        $iframe= $lst->item($i);
		if(strpos($iframe->attributes->getNamedItem('src')->value,'youtube') AND !strpos($iframe->attributes->getNamedItem('src')->value,'videoseries'))
			$youtube_urls [strip_tags($iframe->attributes->getNamedItem('src')->value)] = 'video';
    }
	
	
	$lst = $document->getElementsByTagName('embed');
	
	for ($i=0; $i<$lst->length; $i++) {
        $iframe= $lst->item($i);
		if(strpos($iframe->attributes->getNamedItem('src')->value,'youtube') AND !strpos($iframe->attributes->getNamedItem('src')->value,'videoseries'))
			$youtube_urls [strip_tags($iframe->attributes->getNamedItem('src')->value)] = 'video';
		
    }
	
	return $youtube_urls;
}

if($ytnf->get_option( 'is_plugin_enabled' ))
{
	add_filter('the_content','ytnf_check_video',1);
}
function ytnf_check_video($html)
{
	$ytnf = new Sunrise_Plugin_Framework;
	global $post;
	
	$ytnf_checked = get_post_meta($post->ID,'ytnf_checked',true);
	if($ytnf_checked == '' OR $ytnf_checked == null)
		update_post_meta($post->ID,'ytnf_checked',current_time('timestamp'));
	
	$now = current_time('timestamp')-604800; // older than 7 days from now.
	global $youtube_debug_mode;
	if (($ytnf_checked < $now) OR $youtube_debug_mode==true)
	{
		// get embeded code in iframe and <embed> src
		$a = ytnf_get_youtube_embed_codes_from_html($html);
		
		// get youtube url/code from [youtube] [/youtube] shortcode
		$x = ytnf_check_shortcode_videos($html);
		
		// get only youtube url pasted as text in html code
		$z = ytnf_get_url_list(strip_shortcodes($html));
		//print_r($z);die();
		// combine all type of youtube links found....
		$y = array_merge($a,$x,$z);

		$content = "";
		$i = 1;
		//echo "<pre>".print_r($y,true)."</pre>";die();
		if(count($y)>0)
		{
			foreach ($y as $a=>$k)
			{
				
				if (ytnf_is_valid_youtube_video($a,$k)==false)
				{
					if($ytnf->get_option( 'replace_text' ) != '')
					{
						$html = preg_replace('/(<iframe).*?('.ytnf_get_youtube_id($a,$k).').*?(<\/iframe>)/',html_entity_decode($ytnf->get_option( 'replace_text' )),$html);
						$html = preg_replace('/(<object).*?('.ytnf_get_youtube_id($a,$k).').*?(<\/object>)/',html_entity_decode($ytnf->get_option( 'replace_text' )),$html);
						$html = str_replace($a,html_entity_decode($ytnf->get_option( 'replace_text' )),$html);
					}
					
					$log = array(
						'video'=>$a,
						'where'=>$post->post_type,
						'id'=>$post->ID,
					);
					
					ytnf_log_data($log);
				
				}
			}
			
		}
		
		update_post_meta($post->ID,'ytnf_checked',current_time('timestamp'));
	}
	
	
	// check for youtube shortcodes [youtube] youtube video url OR ID [/youtube]
	
	
	//return '';
	return $html;

}

function ytnf_check_shortcode_videos($html)
{
	$return = array();
	if(preg_match_all('/(\[youtube\]).*?(\[\/youtube\])/',$html,$match))
	{
		foreach ($match[0] as $m)
		{
			$bbb = str_replace('[youtube]','',$m);
			$m = str_replace('[/youtube]','',$bbb);
			$m = trim($m);
			if (strlen($m)==11)
				$return["http://www.youtube.com/watch?v=".$m] = 'video';
			else if(substr($m,0,2)=='PL')
				$return["https://www.youtube.com/playlist?list=".$m] = 'playlist';
			else
				$return[$m] = 'video';
		}
		
	}
	return $return;
}


function set_html_content_type()
{
	return 'text/html';
}

if($ytnf->get_option( 'is_plugin_enabled' ) AND $ytnf->get_option( 'check_comment' ))
{
	add_filter('comments_array','ytnf_process_comment',1999);
}
function ytnf_process_comment($data)
{
	$ytnf = new Sunrise_Plugin_Framework;
	global $post;
	$content = '';
	$count = 1;
	foreach ($data as $key=>$d)
	{
		$ytnf_checked = get_comment_meta($d->comment_ID,'ytnf_checked',true);
		if($ytnf_checked == '' OR $ytnf_checked == null)
			update_comment_meta($d->comment_ID,'ytnf_checked',current_time('timestamp'));
		
		$now = current_time('timestamp')-172800 ; // older than two days from now.
		//$now = current_time('timestamp'); // older than two days from now.
		if ($ytnf_checked < $now)
		{
			
			$y = ytnf_get_youtube_embed_codes_from_html($d->comment_content);
			$x = ytnf_check_shortcode_videos($d->comment_content);
			$temp = ytnf_get_url_list($d->comment_content);
			$html = $d->comment_content;
			$temp = array_merge($y,$temp,$x);
			foreach ($temp as $t=>$k)
			{
				if(!ytnf_is_valid_youtube_video($t,$k))
				{
				
					if($ytnf->get_option( 'replace_text' ) != '')
					{
						$html = preg_replace('/(<iframe).*?('.ytnf_get_youtube_id($t,$k).').*?(<\/iframe>)/',html_entity_decode($ytnf->get_option( 'replace_text' )),$html);
						$html = preg_replace('/(<object).*?('.ytnf_get_youtube_id($t,$k).').*?(<\/object>)/',html_entity_decode($ytnf->get_option( 'replace_text' )),$html);
						$html = str_replace($t,html_entity_decode($ytnf->get_option( 'replace_text' )),$html);
						
						$d->comment_content = $html;
						$data[$key]=$d;
					}
				
					
					$log = array(
						'video'=>$t,
						'where'=>'comment',
						'id'=>$d->comment_ID,
					);
					
					ytnf_log_data($log);
				
					
				}
				
			}
			
			update_comment_meta($d->comment_ID,'ytnf_checked',current_time('timestamp'));
		}
	}
	
	return $data;
}

function ytnf_get_url_list($data)
{
	$data = preg_replace('#<[^>]+>#', ' ', $data);
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/[^\"'\s\[\]]*)?/";
	$return = array();
	if(preg_match_all($reg_exUrl, $data, $url))
	{
		foreach($url[0] as $u)
		{	if (strpos($u,'youtube') OR strpos($u,'youtu.be'))
			{
				
				if(strpos($u,'list') OR strpos($u,'videoseries'))
					$return[strip_tags(rtrim(rtrim($u,'"'),"'"))] = 'playlist';
				else				
					$return[strip_tags(rtrim(rtrim($u,'"'),"'"))] = 'video';
			}
		}
		
	}
	return $return;
}

function ytnf_send_email($content,$author_id = 0)
{
	
	if ($content!=='')
	{
		$to = array();
		$ytnf = new Sunrise_Plugin_Framework;
		if($ytnf->get_option( 'send_admin' ))
			$to[] = get_bloginfo('admin_email');
		if($ytnf->get_option( 'send_author' ) AND $author_id > 0)
			$to[] = get_the_author_meta( 'user_email', $author_id ); 
		
		if(count($to)>0)
		{
			$to = implode(',',$to);
			$subject = 'Invalid youtube videos found on "'.get_bloginfo('name').'"';
			$message = '<p>Dear Sir/Mam</p>I have found invalid youtube video on your site "'.get_bloginfo('name').'" and the details are given below.</p>'.$content;
			add_filter( 'wp_mail_content_type', 'set_html_content_type' );
			wp_mail( $to, $subject, $message);
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
		}
	}
	return;
}

function ytnf_log_data($data)
{
	$opt = get_option('ytnf_log_data');
	$month = current_time('timestamp') - 2592000;
	if(!is_array($opt))
	{
		$opt = array();
	}
	foreach ($opt as $k=>$o)
	{
		if($o['time']<$month)
			unset($opt[$k]);
	}
	$data['time']=current_time('timestamp');
	$opt [$data['id'].'_'.$data['where'].'_'.$data['video']] = $data;
	update_option('ytnf_log_data',$opt);
	return;
}


add_action('wp_ajax_ytnf_reset_log', 'ytnf_reset_log_callback');

function ytnf_reset_log_callback() {
	if($_POST['whatever'] == 'reset the log')
	{
		$opt = array();
		update_option('ytnf_log_data',$opt);
		echo "reset";
	}
	die(); // this is required to return a proper result
}


function ytnf_daily_event_func()
{
	//send email log daily.....
	$opt2 = get_option('ytnf_log_data');
	$last_send = get_option('ytnf_log_email');
	$flag = false;
	$opt = "";
	if(count($opt2)>0)
	{
		$opt = "<table class='widefat'><thead><tr><th>Count (Total: ".count($opt2).")</th><th>Video</th><th>Found In</th><th>Last Checked On</th></tr></thead><tfoot><tr><th>Count (Total: ".count($opt2).")</th><th>Video</th><th>Found In</th><th>Last Checked On</th></tr></tfoot><tbody>";
		$count = 1;
		foreach($opt2 as $o)
		{
			if($o['time']>$last_send)
			{
				if($o['where']!='comment')
					$edit_url = admin_url('post.php?action=edit&post='.$o['id']);
				else
					$edit_url = admin_url('comment.php?action=editcomment&c='.$o['id']);
				
				$time = date('F j, Y, g:i a',$o['time']);
				
				$opt .= "<tr><td>$count</td><td>".$o['video']."</td><td>".$o['where']." ID= ".$o['id']." (<a href='".$edit_url."' target='_blank'>Edit ".$o['where']."</a>)</td><td>".$time."</td></tr>";
				$count++;
				$flag = true;
			}
		}
		$opt .= "</tbody></table><p><a href='".admin_url('options-general.php?page=youtube-not-found')."'>Click Here</a> To view all invalid videos or configure settings.</p><p>Thanks<br>Youtube Not Found Plugin....</p>";
	}
	if($flag)
		ytnf_send_email($opt);
	update_option('ytnf_log_email',current_time('timestamp'));
}


add_action('wp_ajax_ytnf_mindstien_signup', 'ytnf_mindstien_signup_callback');

function ytnf_mindstien_signup_callback() {
	
	
	$url = "http://mindstien.com/plugin_frame.php";
	$response = wp_remote_post( $url, array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => array(
		'action'=>'plugin_signup_form',
		'plugin_name'=>'Youtube Not Found',
		'name' => $_POST['name'], 
		'email' => $_POST['email'],
		'admin_email'=>get_bloginfo('admin_email'),
		'blog'=>get_bloginfo('url')
		),
	'cookies' => array()
    )
	);

	if( is_wp_error( $response ) ) {
	   $error_message = $response->get_error_message();
	   echo "Something went wrong: $error_message";
	} else {
		update_option('ytnf_mindstien_signup','signedup');
		echo "done";
	}
	die(); // this is required to return a proper result
}
?>
