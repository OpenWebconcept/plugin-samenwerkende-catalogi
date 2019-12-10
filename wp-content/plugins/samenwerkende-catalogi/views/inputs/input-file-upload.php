<div class="mh_upload-container">
	<div class="mh_upload-thumbnail">
		<?php if( isset($current_img[0]) ) { ?>
			<img src="<?php echo $current_img[0]; ?>">
		<?php } ?>
	</div>
	
	<a href="#" class="mh_upload-select">
		Selecteer bestand
	</a>
	<a href="#" class="mh_upload-remove">
		Verwijder bestand
	</a>
	<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $current_id; ?>"/>
</div>