<?php

$rating_result = $rating_entry['rating_result'];
$review_comment = $rating_entry['comment'];
$title2 = $rating_entry['title'];
$name = $rating_entry['name'];
$comment = $rating_entry['comment'];
$entry_date = $rating_entry['entry_date'];
$user_id = $rating_entry['user_id'];
$post_id = $rating_entry['post_id'];
$rating_form_id = $rating_entry['rating_form_id'];
$email = $rating_entry['email'];
$post_obj = get_post( $post_id );

if ( $layout == 'table' ) {
	?>
	<tr>
		<td class="mrp-rating-entry-meta">
		<?php
			if ( $show_avatar ) {
				echo get_avatar( $email );
			}

			if ( $show_name ) {
				?>
				<div class="mrp name">
					<?php
					if ( strlen( trim( $name ) ) == 0 || $add_author_link == false ) {
						echo "$before_name";
					} else {
						echo "$before_name" . '<a href="' . get_author_posts_url( $user_id ) . '">';
					}

					if ( strlen( trim( $name ) ) == 0 ) {
						echo __( 'Anonymous', 'multi-rating-pro' );
					} else {
						echo esc_html( $name );
					}

					if ( strlen( trim( $name ) ) != 0  || $add_author_link == false ) {
						echo '</a>';
					}
					echo "$after_name";
					?>
				</div>
				<?php
			}

			if ( $show_date ) {
				?>
				<div class="entry-date mrp"><?php	echo "$before_date" . mysql2date( get_option( 'date_format' ), $entry_date ) . "$after_date"; ?></div>
				<?php
			}
			?>
		</td>

		<td class="mrp-rating-entry-details">
	<?php

} else {
	?>
	<div class="mrp-rating-entry-details">
	<?php
}

/**
 * Title
 */
if ( $show_title && strlen( $title2 ) > 0 ) {
	?>
	<div class="mrp mrp-title"><?php echo $title2; ?></div>
	<?php
}

/**
 * Comment
 */
if ( $show_comment && strlen( $comment ) > 0 ) {
	?>
	<div class="mrp-comment mrp"><?php echo "$before_comment" .  wp_kses_post( nl2br( $comment ) ) . "$after_comment"; ?></div>
	<?php
}

if ( $show_overall_rating ) {
	?>
	<div class="mrp mrp-overall-rating">
		<?php
		mrp_get_template_part( 'rating-result', null, true, array(
				'no_rating_results_text' => '',
				'ignore_count' => true,
				'show_title' => false,
				'before_title' => null,
				'after_title' => null,
				'show_date' => false,
				'show_count' => false,
				'result_type' => $result_type,
				'class' => $class . ' rating-result-' . $rating_form_id . '-' . $post_id,
				'rating_result' => $rating_result,
				'post_id' => $post_id,
				'rating_form_id' => $rating_form_id,
				'preserve_max_option' => false,
				'before_date' => $before_date,
				'after_date' => $after_date
		) );
		?>
	</div>
	<?php

}

/**
 * Rating Items
 */
if ( $show_rating_items ) {

	foreach ( $rating_entry['rating_item_values'] as $rating_item_id => $value ) {

		if ( $value != -1 ) { // only show if applicable
			$rating_item = $rating_items[$rating_item_id];
			?>
			<div class="mrp rating-item-result">
				<label class="description"><?php echo esc_html( $rating_item['description'] ); ?></label>

				<span class="rating-result">
					<?php
					$type = $rating_item['type'];
					$max_option_value = $rating_item['max_option_value'];

					if ( $type == 'star_rating' ) {

						$template_part_name = 'star-rating';
						if ( $use_custom_star_images ) {
							$template_part_name = 'custom-star-images';
						}

						mrp_get_template_part( 'rating-result', $template_part_name, true, array(
							'max_stars' => $max_option_value,
							'star_result' => $value
						) );

					} else if ( $type == 'thumbs' ) {

						mrp_get_template_part( 'thumbs-value', null, true, array(
								'value' => $value
						) );

					} else {

						$option_value_text_lookup = MRP_Utils::get_option_value_text_lookup( $rating_item['option_value_text'] );
						$value_text = isset( $option_value_text_lookup[$value] ) ? $option_value_text_lookup[$value] : $value;
						$value_text = apply_filters( 'mrp_value_text', $value_text, $type, $value, $max_option_value );

						?>
						<span class="value-text"><?php echo esc_html( $value_text ); ?></span>
						<?php

					} ?>
				</span>
			</div>
			<?php
		}
	}
}

/**
 * Custom fields
 */
if ( $show_custom_fields ) {

	foreach ( $rating_entry['custom_field_values'] as $custom_field_id => $value ) {

		$custom_field =  $custom_fields[$custom_field_id];

		mrp_get_template_part( 'custom-field-value', null, true, array(
				'custom_field' => $custom_field,
				'value_text' => $value
		) );
	}

}

do_action( 'mrp_rating_entry_details_after_custom_fields', $rating_entry );

if ( $show_permalink ) {
	?>
	<div class="mrp permalink"><a class="post-permalink" href="<?php echo get_the_permalink( $post_id ); ?>"><?php echo esc_html( $post_obj->post_title ); ?></a></div>
	<?php
}

if ( $layout == 'table' ) {
	?>
		</td>
	</tr>
	<?php
} else {
	?>
	</div>
	<div class="mrp-rating-entry-meta">
		<?php
		if ( $show_avatar ) {
			echo get_avatar( $email );
		}

		if ( $show_name ) {
			?>
			<div class="mrp name">
				<?php
				if ( strlen( trim( $name ) ) == 0 || $add_author_link == false ) {
					echo "$before_name";
				} else {
					echo "$before_name" . '<a href="' . get_author_posts_url( $user_id ) . '">';
				}

				if ( strlen( trim( $name ) ) == 0 ) {
					echo __( 'Anonymous', 'multi-rating-pro' );
				} else {
					echo esc_html( $name );
				}

				if ( strlen( trim( $name ) ) != 0  || $add_author_link == false ) {
					echo '</a>';
				}
				echo "$after_name";
				?>
			</div>
			<?php
		}

		if ( $show_date ) {
			?>
			<div class="entry-date mrp"><?php	echo "$before_date" . mysql2date( get_option( 'date_format' ), $entry_date ) . "$after_date"; ?></div>
			<?php
		}
		?>
	</div>
	<?php
}
