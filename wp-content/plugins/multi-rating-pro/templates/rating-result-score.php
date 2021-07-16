<?php
/**
 * Rating result score rating template
 */
?>
<span class="score-result">
	<?php
	$out_of_text = apply_filters( 'mrp_out_of_text', '/' );
	echo mrp_number_format( $rating_result['adjusted_score_result'] );
	echo esc_html( $out_of_text );
	echo mrp_number_format( $rating_result['total_max_option_value'] );
	?>
</span>
