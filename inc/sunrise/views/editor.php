<?php $triggable = ( $option['triggable'] ) ? ' data-triggable="' . $option['triggable'] . '" class="sunrise-plugin-triggable hide-if-js"' : ''; ?>
<tr<?php echo $triggable; ?>>
	<th scope="row"><label for="sunrise-plugin-field-<?php echo $option['id']; ?>"><?php echo $option['name']; ?></label></th>
	<td><?php /*
		<textarea name="<?php echo $option['id']; ?>" id="sunrise-plugin-field-<?php echo $option['id']; ?>" class="regular-text sunrise-plugin-textarea" rows="<?php echo ( isset( $option['rows'] ) ) ? $option['rows'] : 5; ?>"><?php echo stripslashes( $settings[$option['id']] ); ?>editor test</textarea> 
		<?php
		*/
			//the_editor($settings[$option['id']], 'sunrise-plugin-field-'.$option['id'], $prev_id = 'title', $media_buttons = true, $tab_index = 2)
			$attrb = array(
				"textarea_name"=>$option['id'],
				"wpautop"=>false,
			);
			//echo "<pre>".print_r($settings,true)."</pre>";
			wp_editor( stripslashes($settings[$option['id']]), 'sunrise-plugin-field-'.$option['id'], $attrb ); 
		?>
		<p class="description"><?php echo $option['desc']; ?></p>
	</td>
</tr>