<?php $triggable = ( $option['triggable'] ) ? ' data-triggable="' . $option['triggable'] . '" class="sunrise-plugin-triggable hide-if-js"' : ''; ?>

<?php

?>

<tr<?php echo $triggable; ?>>
	<th scope="row"><label for="sunrise-plugin-field-<?php echo $option['id']; ?>"><?php echo $option['name']; ?></label></th>
	<td>
		<input type="text" value="asdf<?php echo $settings[$option['id']]; ?>" name="<?php echo $option['id']; ?>" id="sunrise-plugin-field-<?php echo $option['id']; ?>" class="regular-text" />
		<input type='button' name='button' value='Select URL' id='mywplink'>
		<p class="description"><?php echo $option['desc']; ?></p>
		
		<?php add_action('admin_footer','mygeo_admin_footer'); ?>
		
	</td>
</tr>