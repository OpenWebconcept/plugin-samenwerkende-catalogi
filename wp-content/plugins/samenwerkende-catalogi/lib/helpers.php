<?php
	function get_sc_post_types() {
		return array(
			'post'
		);
	}

	function get_sc_text($text = '', $max_len = 50, $rep = '...', $remove_html = false) {
		$text = strip_shortcodes($text);
		
		$text = ($remove_html ? sanitize_text_field($text) : $text);
		$text = html_entity_decode($text);

		$text = preg_replace("/\s/",' ',$text);
		
		$cur_lenght = strlen($text);
		if ( $cur_lenght > $max_len ):
			if( preg_match('/^.{1,'.$max_len.'}\b/s', $text, $match) ):
				$text = rtrim($match[0]) . $rep;
			else:
				$text = rtrim(substr($text, 0, $max_len)) . $rep;
			endif;
		else:
			$text = rtrim($text);
		endif;

		return get_sc_xmlentities($text);

	}

	function get_sc_upn_label_by_id($id) {

		global $wpdb;

		$table_name 	= $wpdb->prefix . 'sc_uniform_product_names';
		$query 			= $wpdb->prepare("SELECT label FROM {$table_name} WHERE id=%d", $id);
		$var 			= $wpdb->get_var( $query );
		return ($var ? $var : '');

	}

	function get_sc_upn_by_id($id) {

		global $wpdb;

		$table_name 	= $wpdb->prefix . 'sc_uniform_product_names';
		$query 			= $wpdb->prepare("SELECT * FROM {$table_name} WHERE id=%d", $id);
		$var 			= $wpdb->get_row( $query );
		return ($var ? $var : '');

	}
		
	function get_sc_xmlentities( $string ) { 
	    $not_in_list = "A-Z0-9a-z\s_-"; 
	    return preg_replace_callback( "/[^{$not_in_list}]/" , 'get_sc_xml_entity_at_index_0' , $string ); 
	} 
	
	function get_sc_xml_entity_at_index_0( $CHAR ) { 
	    if( !is_string( $CHAR[0] ) || ( strlen( $CHAR[0] ) > 1 ) ) { 
	        die( "function: 'get_sc_xml_entity_at_index_0' requires data type: 'char' (single character). '{$CHAR[0]}' does not match this type." ); 
	    } 
	    switch( $CHAR[0] ) { 
	        case "'":    case '"':    case '&':    case '<':    case '>': 
	            return htmlspecialchars( $CHAR[0], ENT_QUOTES );    break; 
	        default: 
	            return $CHAR[0];                break; 
	    }        
	}