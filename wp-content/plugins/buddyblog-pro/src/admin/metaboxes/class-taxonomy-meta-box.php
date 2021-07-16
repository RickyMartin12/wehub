<?php
/**
 * BuddyBlog Pro Form edit page helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin/Metaboxes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin\Metaboxes;

use BuddyBlog_Pro\Core\Terms_Checklist_Walker;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Taxonomy meta box helper.
 */
class Taxonomy_Meta_Box extends BBL_Meta_Box {

	/**
	 * Saves Taxonomy meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {
		$post_id   = $post->ID;
		$post_type = $this->input( 'post-type', 'post' );

		update_post_meta( $post_id, '_buddyblog_enable_taxonomy', $this->input( 'enable-taxonomy', 0 ) );
		//update_post_meta( $post_id, '_buddyblog_enabled_taxonomies', $this->input( 'enabled-taxonomies') );

		// for taxonomies.
		//bbl-input--category
		$enabled_taxonomies        = $this->input( 'enabled-taxonomies', array() );
		$tax                       = array();
		$post_supported_taxonomies = $post_type ? get_object_taxonomies( $post_type ) : array();

		foreach ( $enabled_taxonomies as $taxonomy ) {
			if ( ! $taxonomy || ! $post_supported_taxonomies || ! in_array( $taxonomy, $post_supported_taxonomies ) ) {
				continue;// not valid.
			}

			$default_term = $this->input( 'taxonomy-default-' . $taxonomy, 0 );
			if ( $default_term < 0 ) {
				$default_term = 0;
			}

			$tax[ $taxonomy ] = array(
				'taxonomy'     => $taxonomy,
				'include'      => $this->input( 'taxonomy-included-' . $taxonomy ),
				'exclude'      => $this->input( 'taxonomy-excluded-' . $taxonomy ),
				'default'      => $default_term,
				'allow_create' => $this->input( 'taxonomy-allow-create-' . $taxonomy ),
				'view'         => $this->input( 'taxonomy-view-' . $taxonomy, 'checkbox' ),
			);
		}

		update_post_meta( $post_id, '_buddyblog_enabled_taxonomies', $tax );
	}

	/**
	 * Show details meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {

		$post    = get_post( $post );
		$post_id = $post->ID;

		$selected_post_type = get_post_meta( $post_id, '_buddyblog_post_type', true );
		$post_type_saved    = true;

		if ( empty( $selected_post_type ) ) {
			$selected_post_type = 'post';
			$post_type_saved    = false;
		}

		if ( $post_type_saved ) {
			$taxonomies = get_object_taxonomies( $selected_post_type );
		} else {
			$taxonomies = array();
		}

		if ( isset( $taxonomies['post_format'] ) ) {
			unset( $taxonomies['post_format'] );
		}

		$enabled_tax_options = get_post_meta( $post_id, '_buddyblog_enabled_taxonomies', true );

		if ( ! $enabled_tax_options ) {
			$enabled_tax = array();
		} else {
			$enabled_tax = array_keys( $enabled_tax_options );
		}

		$tax         = array();
		$terms_count = array();

		foreach ( $taxonomies as $taxonomy ) {
			$tax_object               = get_taxonomy( $taxonomy );
			$terms_count[ $taxonomy ] = wp_count_terms( $taxonomy );
			$tax[ $taxonomy ]         = $tax_object->labels->name;
		}

		$enable_taxonomy = get_post_meta( $post_id, '_buddyblog_enable_taxonomy', true );

		?>
        <div id="bbl-tax-settings-wrapper" class="bbl-form-fields bbl-tax-settings-wrapper" data-form-id="<?php echo esc_attr( $post_id ); ?>">

            <div class="bbl-field-section bbl-field-section-tax-settings" id="bbl-field-section-tax-settings">

                <div class="bbl-row bbl-row-tax-settings bbl-row-tax-settings-enable-taxonomy">
                    <label class="bbl-label bbl-label-tax-settings bbl-col-left">
						<?php _e( 'Enable Taxonomy:', 'buddyblog-pro' ); ?>
                        <span class="bbl-required">*</span>
                    </label>
                    <div class="bbl-col-right">
						<?php $this->selectbox(
							array(
								'name'     => 'bbl-input-enable-taxonomy',
								'options'  => array(
									1 => __( 'Yes', 'buddyblog-pro' ),
									0 => __( 'No', 'buddyblog-pro' ),
								),
								'selected' => $enable_taxonomy,
							)
						);
						?>
                    </div>
                </div><!-- end of row -->
				<?php if ( ! $post_type_saved ) : ?>
                    <div class="bbl-row bbl-row-tax-settings bbl-row-tax-settings-enable-taxonomy">
                        <div class="bbl-notice">
                            <p><?php _e( 'You have not saved the form settings. Please save the form after selecting post type to see taxonomy options.', 'buddyblog-pro' ); ?></p>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ( $enable_taxonomy && $post_type_saved && $tax ): ?>

                    <div class="bbl-row bbl-row-tax-settings bbl-row-enable-taxonomy">
                        <label class="bbl-label bbl-label-post-settings bbl-col-left">
							<?php _e( 'Enabled Taxonomies:', 'buddyblog-pro' ); ?>
                            <span class="bbl-required">*</span>
                        </label>
                        <div class="bbl-col-right">
							<?php $this->checkbox(
								array(
									'name'     => 'bbl-input-enabled-taxonomies',
									'options'  => $tax,
									'selected' => $enabled_tax,
								)
							);
							?>
                        </div>
                    </div><!-- end of row -->
					<?php foreach ( $tax as $taxonomy => $label ): ?>
						<?php
						// There are no terms in the tax currently. Skip it.
						if ( empty( $terms_count[ $taxonomy ] ) ) {
							continue;
						}

						$tax_class         = in_array( $taxonomy, $enabled_tax ) ? 'bbl-tax-visible' : 'bbl-tax-hidden';
						$allow_term_create = isset( $enabled_tax_options[ $taxonomy ] ) && isset( $enabled_tax_options[ $taxonomy ]['allow_create'] ) ? absint( $enabled_tax_options[ $taxonomy ]['allow_create'] ) : 0;

						?>
                        <div class="bbl-row  bbl-row-tax-settings-tax bbl-row-tax-settings-<?php echo esc_attr( $taxonomy ); ?> <?php echo $tax_class; ?> bbl-section-post-settings-taxonomy-<?php echo esc_attr( $taxonomy ); ?>">
                            <h4><?php echo $tax[ $taxonomy ]; ?></h4>
                            <div class="bbl-row bbl-row-tax-settings">
                                <label class="bbl-label bbl-label-tax-settings bbl-col-left">
									<?php printf( __( 'Limit to %s', 'buddyblog-pro' ), $label ); ?>
                                </label>
                                <div class="bbl-col-right">
									<?php bblpro_form_terms_checklist(
										array(
											'taxonomy'              => $taxonomy,
											'name'                  => 'bbl-input-taxonomy-included-' . $taxonomy,
											'walker'                => new Terms_Checklist_Walker(),
											'show_category_heading' => false,
											'selected'              => isset( $enabled_tax_options[ $taxonomy ] ) && isset( $enabled_tax_options[ $taxonomy ]['include'] ) ? $enabled_tax_options[ $taxonomy ]['include'] : array(),
										)
									); ?>
                                </div>
                            </div><!-- end of row -->
                            <div class="bbl-row bbl-row-tax-settings">
                                <label class="bbl-label bbl-label-tax-settings bbl-col-left">
									<?php printf( __( 'Excluded %s', 'buddyblog-pro' ), $label ); ?>
                                </label>
                                <div class="bbl-col-right">
									<?php bblpro_form_terms_checklist(
										array(
											'taxonomy'              => $taxonomy,
											'name'                  => 'bbl-input-taxonomy-excluded-' . $taxonomy,
											'walker'                => new Terms_Checklist_Walker(),
											'show_category_heading' => false,
											'selected'              => isset( $enabled_tax_options[ $taxonomy ] ) && isset( $enabled_tax_options[ $taxonomy ]['exclude'] ) ? $enabled_tax_options[ $taxonomy ]['exclude'] : array(),
										)
									); ?>
                                </div>
                            </div><!-- end of row -->
                            <div class="bbl-row bbl-row-tax-settings">
                                <label class="bbl-label bbl-label-tax-settings bbl-col-left">
									<?php _e( 'Default Term', 'buddyblog-pro' ); ?>
                                </label>
                                <div class="bbl-col-right">
									<?php
									$default_term = isset( $enabled_tax_options[ $taxonomy ] ) && isset( $enabled_tax_options[ $taxonomy ]['default'] ) ? absint( $enabled_tax_options[ $taxonomy ]['default'] ) : 0;

									wp_dropdown_categories( array(
										'name'             => 'bbl-input-taxonomy-default-' . $taxonomy,
										'taxonomy'         => $taxonomy,
										'show_option_none' => __( 'Select term', 'buddyblog-pro' ),
										'selected'         => $default_term,
									) );
									?>
                                </div>
                            </div><!-- end of row -->

                           <!-- <div class="bbl-row bbl-row-tax-settings">
                                <label class="bbl-label bbl-label-tax-settings bbl-col-left">
									<?php _e( 'Allow users to create term', 'buddyblog-pro' ); ?>
                                </label>
                                <div class="bbl-col-right">
                                    <label>
                                        <input type="radio"
                                               name="bbl-input-taxonomy-allow-create-<?php echo $taxonomy; ?>"
                                               value="1" <?php checked( 1, $allow_term_create ); ?>/> <?php _e( 'Yes', 'buddyblog-pro' ); ?>
                                    </label>

                                    <label>
                                        <input type="radio" name="bbl-input-taxonomy-allow-create-<?php echo $taxonomy; ?>" value="0" <?php checked( 0, $allow_term_create ); ?> /> <?php _e( 'No', 'buddyblog-pro' ); ?>
                                    </label>

                                </div>
                            </div> --><!-- end of row -->

                        </div><!-- end of section -->
					<?php endforeach; ?>

				<?php endif; ?>
            </div><!-- section end-->

        </div>
		<?php
	}
}
