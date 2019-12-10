<select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" data-action="<?php echo $action; ?>" class="sc-select2-ajax" <?php echo ($multi ? 'multiple="multiple"' : ''); ?>>
	<?php if( $current_value && $current_value !== '' ) { ?>
		<option value="<?php echo $current_id; ?>"><?php echo $current_value; ?></option>
	<?php } ?>
</select>