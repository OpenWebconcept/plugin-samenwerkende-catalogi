<input type="text" placeholder="<?php echo $placeholder; ?>" id="address-<?php echo $field_name; ?>" class="gmap-controls regular-text" value="<?php echo $current_title; ?>">
<input type="hidden" name="<?php echo $field_name; ?>_title" id="title-<?php echo $field_name; ?>" value="<?php echo $current_title; ?>">
<input type="hidden" name="<?php echo $field_name; ?>_lat" id="lat-<?php echo $field_name; ?>" value="<?php echo $current_lat; ?>">
<input type="hidden" name="<?php echo $field_name; ?>_long" id="long-<?php echo $field_name; ?>" value="<?php echo $current_long; ?>">
<input type="hidden" name="<?php echo $field_name; ?>_zipcode" id="zipcode-<?php echo $field_name; ?>" value="<?php echo $current_zipcode; ?>">

<div id="map-<?php echo $field_name; ?>" class="map-field"></div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		initMapField('<?php echo $field_name; ?>');
	});
</script>