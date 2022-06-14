<select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" <?php echo ($multi ? 'multiple="multiple"' : ''); ?>>
	<option value="">- Selecteer een optie -</option>
	<?php foreach ($options as $key => $value) { ?>
		<option value="<?php echo $key; ?>" <?php selected($current_id, $key); ?>><?php echo $value; ?></option>
	<?php } ?>
</select>