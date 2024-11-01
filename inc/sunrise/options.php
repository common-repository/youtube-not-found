<?php
$opt2 = get_option('ytnf_log_data');
if(!$opt2)
	$opt2 = array();
$opt = "";
if(count($opt2)>0)
{
	$opt = "<table class='widefat'><thead><tr><th>Count (Total: ".count($opt2).")</th><th>Video</th><th>Found In</th><th>Last Checked On</th></tr></thead><tfoot><tr><th>Count (Total: ".count($opt2).")</th><th>Video</th><th>Found In</th><th>Last Checked On</th></tr></tfoot><tbody>";
	$count = 1;
	foreach($opt2 as $o)
	{
		$view = "";
		if($o['where']!='comment')
		{
			$edit_url = admin_url('post.php?action=edit&post='.$o['id']);
			$view = "<a href='".get_permalink($o['id'])."' target='_blank'>".get_the_title($o['id'])."</a><br>";
		}
		else
			$edit_url = admin_url('comment.php?action=editcomment&c='.$o['id']);
		
		$time = date('F j, Y, g:i a',$o['time']);
		
		$opt .= "<tr><td>$count</td><td><a href='".$o['video']."' target='_blank'>".$o['video']."</a></td><td>".$view.$o['where']." ID= ".$o['id']." (<a href='".$edit_url."' target='_blank'>Edit ".$o['where']."</a>)</td><td>".$time."</td></tr>";
		$count++;
	}
	$opt .= "</tbody></table>";
}
else
{
	$opt = "<div class='updated'><h3>Congratulations ! There is no invalid youtube videos we have found yet... Please visit back after few days...</h3></div>";
}





	/** Plugin options */
	$options = array(
		array(
			'name' => __( 'Options', $this->textdomain ),
			'type' => 'opentab'
		),array(
			'name' => __( 'Enable Plugin', $this->textdomain ),
			'desc' => __( 'Tick this to enable this plugin on frontend', $this->textdomain ),
			'std' => 'on',
			'id' => 'is_plugin_enabled',
			'type' => 'checkbox',
			'label' => __( 'Enable Plugin', $this->textdomain )
		),array(
			'name' => __( 'Email To Admin', $this->textdomain ),
			'desc' => __( 'Tick this to enable email sending site Administrator', $this->textdomain ),
			'std' => 'on',
			'id' => 'send_admin',
			'type' => 'checkbox',
			'label' => __( 'Email To Admin', $this->textdomain )
		),array(
			'name' => __( 'Check comments also for invalid youtube video', $this->textdomain ),
			'desc' => __( 'Tick this to text user comments also for invalid youtube links', $this->textdomain ),
			'std' => 'on',
			'id' => 'check_comment',
			'type' => 'checkbox',
			'label' => __( 'Check Comment', $this->textdomain )
		),
		array(
			'name' => __( 'Replace Text', $this->textdomain ),
			'desc' => __( 'Display this text instead of invalid video embed code to visitor, Leave blank to not to replace', $this->textdomain ),
			'std' => '',
			'id' => 'replace_text',
			'type' => 'textarea'
		),
		array(
			'type' => 'closetab'
		),
		array(
			'name' => __( 'Invalid Videos (Log)', $this->textdomain ),
			'type' => 'opentab'
		),
		array(
			'html' => '<p>This plugin keeps only 30 days log, Older log gets removed automatically</p></p><div id="ytnf_log_msg">&nbsp</div><div id="ytnf_log">'.$opt.'</div><p> &nbsp; </p><p><input type="button" id="ytnf_reset_log" value="Reset Log"  class="button-primary"></p>',
			'type' => 'html'
		),
		array(
			'type' => 'closetab'
		),
	);
?>