<div style="border:1px solid #aaa;margin: 10px 0px -49px;">
<div style="float:right;">
<a target="_blank" href="http://www.mindstien.com/products/"><img src="<?php echo $this->assets( 'images', 'mindstien_tech.jpg' ); ?>"></a>
<br><span style="margin-left:25px"><strong><a target="blank" href="http://www.mindstien.com/products/">Click Here To See Our Latest WP Plugins</a></strong></span>
</div>
<h2 style="padding:10px"><?php echo $this->name; ?></h2>
<div style="clear: both"></div>
</div>


<div id="sunrise-plugin-settings" class="wrap">
	<div id="icon-options-general" class="icon32 hide-if-no-js"><br /></div>
	<h2 id="sunrise-plugin-tabs" class="nav-tab-wrapper hide-if-no-js">
		<?php
			// Show tabs
			$this->render_tabs();
		?>
	</h2>
	<?php
		// Show notifications
		$this->notifications( array(
			'js' => __( 'For full functionality of this page it is reccomended to enable javascript.', $this->textdomain ),
			'reseted' => __( 'Settings reseted successfully', $this->textdomain ),
			'not-reseted' => __( 'There is already default settings', $this->textdomain ),
			'saved' => __( 'Settings saved successfully', $this->textdomain ),
			'not-saved' => __( 'Settings not saved, because there is no changes', $this->textdomain )
		) );
	?>
	<form action="<?php echo $this->admin_url; ?>" method="post" id="sunrise-plugin-options-form">
		<?php
			// Show options
			$this->render_panes();
		?>
		<input type="hidden" name="action" value="save" />
	</form>
	
	<?php 
	if(get_option('ytnf_mindstien_signup',true)!=="signedup") :?>
	<script>
	jQuery(document).ready(function(){

		jQuery('#mindstien_form').submit(function(){
			var data = {
				action: 'ytnf_mindstien_signup',
				name:jQuery('#mindstien_YMP1').val(),
				email:jQuery('#mindstien_YMP0').val()
			};
			jQuery('#mindstien_form_title').html("Please wait....");
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				
				console.log(response);
				if(response=='done')
				{
					
					jQuery('#mindstien_form_title').html("Please check your inbox for Confirmation Link.....");
					setTimeout(function(){
					  jQuery('#mindstien_signup_form').hide(1000);
					}, 3000);
				}
				else
				{
				
				}
			});
			return false;
		});
	
	});
	</script>
	<div id='mindstien_signup_form' style="border:1px solid darkpink" class='updated'>
		<h2 id='mindstien_form_title' style='color:darkred;text-shadow:2px -1px pink;'><img src="<?php echo $this->assets( 'images', 'newsletter.png' ); ?>" style='float:left;margin:5px'> Newsletter Signup...</h2>
		<strong>to get latest product launch and upgrade informations in inbox</strong>
		<form id='mindstien_form'>
			<p>
			<strong>Name : </strong>
			<input type="text" id="mindstien_YMP1" size="20" /> 
			<strong>Email :</strong>
			<input type="text" id="mindstien_YMP0" size="20" /> 
			<input style="margin-bottom:-20px;" type="image" src="<?php echo $this->assets( 'images', 'new_sub_button.png' ); ?>" value="Submit"  />
			</p>
		</form>
	</div>
	
	<?php endif; ?>
	
	<IFRAME SRC="http://www.mindstien.com/plugin_frame.php?id=youtube_not_found" width="100%" height="2000px" style="overflow: hidden;" id="mindstien_frame" marginheight="0" frameborder="0" ></iframe>
</div>