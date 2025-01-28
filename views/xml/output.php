<?php
	$args = array(
		'post_type'  		=> get_sc_post_types(),
		'posts_per_page'   	=> -1,
		'ignore_sticky_posts' => -1,
		'meta_query' => array(
			array(
				'key'     => 'sc_plugin_product',
				'value'   => 'on',
				'compare' => '=',
			),
		)
	);

	$xml = array('<?xml version="1.0" encoding="UTF-8"?>');
	$xml[] = "<overheidproduct:scproducten xmlns:dcterms=\"http://purl.org/dc/terms/\" xmlns:overheid=\"http://standaarden.overheid.nl/owms/terms/\" xmlns:overheidproduct=\"http://standaarden.overheid.nl/product/terms/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://standaarden.overheid.nl/product/terms/ http://standaarden.overheid.nl/sc/4.0/xsd/sc.xsd\">";

		$query = new WP_Query( $args );
		global $post;
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) { $query->the_post();
				$upn_row 				= false;
				$sc_plugin_aanvragen 	= get_post_meta($post->ID, 'sc_plugin_aanvragen', true);
				$sc_plugin_particulier 	= get_post_meta($post->ID, 'sc_plugin_particulier', true);
				$sc_plugin_ondernemer 	= get_post_meta($post->ID, 'sc_plugin_ondernemer', true);
				$sc_plugin_url 			= get_post_meta($post->ID, 'sc_plugin_url', true);
				$sc_plugin_upn 			= get_post_meta($post->ID, 'sc_plugin_upn', true);
				if( $sc_plugin_upn ) {
					$upn_row = get_sc_upn_by_id($sc_plugin_upn);
				}

				$excerpt 				= (has_excerpt() ? get_the_excerpt() : $post->post_content);

				// Loop trough the different products
				$xml[] = "<overheidproduct:scproduct owms-version=\"4.0\">";
					$xml[] = "<overheidproduct:meta>";
					
						$xml[] = "<overheidproduct:owmskern>";
							$xml[] = "<dcterms:identifier>".get_the_permalink()."</dcterms:identifier>";
							$xml[] = "<dcterms:title>".get_the_title()."</dcterms:title>";
							$xml[] = "<dcterms:language>nl</dcterms:language>";
							$xml[] = "<dcterms:type scheme=\"overheid:Informatietype\">productbeschrijving</dcterms:type>";
							$xml[] = "<dcterms:modified>".get_the_modified_date('Y-m-d')."</dcterms:modified>";
							$xml[] = "<dcterms:spatial scheme=\"overheid:Gemeente\" resourceIdentifier=\"http://standaarden.overheid.nl/owms/terms/Heerenveen_(gemeente)\">Heerenveen</dcterms:spatial>";
							$xml[] = "<overheid:authority scheme=\"overheid:Gemeente\" resourceIdentifier=\"http://standaarden.overheid.nl/owms/terms/Heerenveen_(gemeente)\">Heerenveen</overheid:authority>";
						$xml[] = "</overheidproduct:owmskern>";
						$xml[] = "<overheidproduct:owmsmantel>";
							if( $sc_plugin_particulier == 'on' ) {
								$xml[] = "<dcterms:audience scheme=\"overheid:Doelgroep\">particulier</dcterms:audience>";	
							}
							if( $sc_plugin_ondernemer == 'on' ) {
								$xml[] = "<dcterms:audience scheme=\"overheid:Doelgroep\">ondernemer</dcterms:audience>";
							}
							$xml[] = "<dcterms:abstract>".get_sc_text($excerpt, 290, '...', true)."</dcterms:abstract>";
						$xml[] = "</overheidproduct:owmsmantel>";
						$xml[] = "<overheidproduct:scmeta>";
							$xml[] = "<overheidproduct:productID>".$post->ID."</overheidproduct:productID>";
							$xml[] = "<overheidproduct:onlineAanvragen>".$sc_plugin_aanvragen."</overheidproduct:onlineAanvragen>";

							if( $sc_plugin_aanvragen == 'ja' || $sc_plugin_aanvragen == 'digid' ) {
								$xml[] = "<overheidproduct:aanvraagURL resourceIdentifier=\"".$sc_plugin_url."\"/>";
							}
							
							if( $upn_row && isset($upn_row->label) ) {
								$xml[] = "<overheidproduct:uniformeProductnaam scheme=\"overheid:UniformeProductnaam\" resourceIdentifier=\"".$upn_row->uri."\">".$upn_row->label."</overheidproduct:uniformeProductnaam>";
							}
						$xml[] = "</overheidproduct:scmeta>";
					$xml[] = "</overheidproduct:meta>";
					$xml[] = "<overheidproduct:body></overheidproduct:body>";
				$xml[] = "</overheidproduct:scproduct>";
			}
		}

	$xml[] = "</overheidproduct:scproducten>";

	echo implode("\n", $xml);
