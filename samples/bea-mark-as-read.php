<?php
function bea_the_content_filter( $content ) { 
	if ( is_singular('post') ) {
		$shortcode = '<p style="text-align:right;">'.do_shortcode('[bea-mas]').'</p>';
		$content = $shortcode . $content  . $shortcode;
	}

	return $content;
}
add_filter( 'the_content', 'bea_the_content_filter' );