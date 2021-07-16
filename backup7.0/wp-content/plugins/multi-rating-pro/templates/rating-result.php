<?php
$count_entries = isset( $rating_result['count_entries'] ) ? $rating_result['count_entries'] : 0;
?>
<span class="rating-result <?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?>">
	<?php

	if ( ( $count_entries == null || $count_entries == 0 ) && ( ! isset( $ignore_count ) || ( isset( $ignore_count ) && $ignore_count == false ) ) ) {
		// ignore count is used to not show this block
		$no_rating_results_text = apply_filters( 'mrp_no_rating_results_text', $no_rating_results_text );
		?>
		<span class="no-rating-results-text"><?php echo esc_html( $no_rating_results_text ); ?></span>
		<?php
	} else {

		$post_obj = get_post( $post_id);

		if ( $show_title == true ) {
			?>
			<a href="<?php echo esc_attr( get_permalink( $post_id ) ); ?>"><?php echo esc_html( $post_obj->post_title ); ?></a>
			<?php
		}

		do_action( 'mrp_rating_result_before_result_type' );

		if ( $result_type == MRP_Multi_Rating::SCORE_RESULT_TYPE ) {

			mrp_get_template_part( 'rating-result', 'score', true, array(
					'rating_result' => $rating_result,
					'post_id' => $post_id
			) );

		} else if ( $result_type == MRP_Multi_Rating::PERCENTAGE_RESULT_TYPE ) {

			mrp_get_template_part( 'rating-result', 'percentage', true, array(
					'rating_result' => $rating_result,
					'post_id' => $post_id
			) );

		} else { // star rating

			$general_settings = (array) get_option( MRP_Multi_Rating::GENERAL_SETTINGS );
			$max_stars = $general_settings[MRP_Multi_Rating::STAR_RATING_OUT_OF_OPTION];

			$star_result = $rating_result['adjusted_star_result'];

			if ( isset( $preserve_max_option ) && $preserve_max_option ) {
				$max_stars = $rating_result['total_max_option_value'];
				$star_result = $rating_result['adjusted_score_result'];
			}

			$template_part_name = 'star-rating';
			if ( $use_custom_star_images ) {
				$template_part_name = 'custom-star-images';
			}

			mrp_get_template_part( 'rating-result', $template_part_name, true, array(
				'max_stars' => mrp_number_format( $max_stars ),
				'star_result' => $star_result,
				'post_id' => $post_id
			) );

		}

		do_action( 'mrp_rating_result_after_result_type' );

		if ( $show_count ) {

			$before_count = apply_filters( 'mrp_rating_result_before_count', $before_count, $count_entries );
			$after_count = apply_filters( 'mrp_rating_result_after_count', $after_count, $count_entries );
			?>
			<span class="count">
				<?php
				echo $before_count;
				echo number_format( $count_entries );
				echo $after_count;
				?>
			</span>
			<?php
		}

		if ( $show_date == true && isset( $rating_result['entry_date'] ) ) {

			$before_date = apply_filters( 'mrp_rating_result_before_date', $before_date, $count_entries );
			$after_date= apply_filters( 'mrp_rating_result_after_date', $after_date, $count_entries );

			?>
			<span class="date"><?php echo $before_date . mysql2date( get_option('date_format'), $rating_result['entry_date'] ) . $after_date; ?></span>
			<?php
		}
	}
	?>
</span>

