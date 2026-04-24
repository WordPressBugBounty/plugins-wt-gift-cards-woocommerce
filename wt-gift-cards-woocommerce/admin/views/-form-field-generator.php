<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( is_array( $args ) ) {
	$wbte_allowed_html = Wbte_Woocommerce_Gift_Cards_Free_Common::get_allowed_html();

	foreach ( $args as $wbte_value ) {
		$wbte_tr_id    = ( isset( $wbte_value['tr_id'] ) ? ' id="' . esc_attr( $wbte_value['tr_id'] ) . '" ' : '' );
		$wbte_tr_class = ( isset( $wbte_value['tr_class'] ) ? $wbte_value['tr_class'] : '' );

		$type             = ( isset( $wbte_value['type'] ) ? $wbte_value['type'] : 'text' );
		$wbte_field_group_attr = ( isset( $wbte_value['field_group'] ) ? ' data-field-group="' . esc_attr( $wbte_value['field_group'] ) . '" ' : '' );
		$wbte_tr_class        .= ( isset( $wbte_value['field_group'] ) ? ' wt_gc_field_group_children ' : '' ); // add an extra class to tr when field grouping enabled

		$wbte_after_form_field_html = ( isset( $wbte_value['after_form_field_html'] ) ? $wbte_value['after_form_field_html'] : '' ); /* after form field `td` */
		$wbte_after_form_field      = ( isset( $wbte_value['after_form_field'] ) ? $wbte_value['after_form_field'] : '' ); /* after form field */
		$wbte_before_form_field     = ( isset( $wbte_value['before_form_field'] ) ? $wbte_value['before_form_field'] : '' );


		if ( 'field_group_head' === $type ) {
			$wbte_visibility = ( isset( $wbte_value['show_on_default'] ) ? $wbte_value['show_on_default'] : 0 );
			?>
			<tr <?php echo wp_kses_post( $wbte_tr_id . $wbte_field_group_attr ); ?> class="<?php echo esc_attr( $wbte_tr_class ); ?>">
				<td colspan="3" class="wt_gc_field_group">
					<div class="wt_gc_field_group_hd">
						<?php echo wp_kses_post( isset( $wbte_value['head'] ) ? ( $wbte_value['head'] ) : '' ); ?>
						<div class="wt_gc_field_group_toggle_btn" data-id="<?php echo esc_attr( isset( $wbte_value['group_id'] ) ? $wbte_value['group_id'] : '' ); ?>" data-visibility="<?php echo esc_attr( $wbte_visibility ); ?>"><span class="dashicons dashicons-arrow-<?php echo esc_attr( 1 === absint( $wbte_visibility ) ? 'down' : 'right' ); ?>"></span></div>
					</div>
					<div class="wt_gc_field_group_content">
						<p class="wt_gc_field_group_description"><?php echo wp_kses_post( isset( $wbte_value['description'] ) ? ( $wbte_value['description'] ) : '' ); ?></p>
						<table><!-- Content will be added by JS --></table>
					</div>
				</td>
			</tr>
			<?php
		} else {

			if ( isset( $wbte_value['field_name'] ) ) {
				$wbte_field_name = $wbte_value['field_name'];
			} elseif ( isset( $wbte_value['parent_option'] ) ) {
				$wbte_field_name = $wbte_value['parent_option'] . '[' . $wbte_value['option_name'] . ']';
			} else {
				$wbte_field_name = $wbte_value['option_name'];
			}

			$wbte_field_id = isset( $wbte_value['field_id'] ) ? $wbte_value['field_id'] : $wbte_field_name;

			$wbte_fld_attr   = ( isset( $wbte_value['attr'] ) ? $wbte_value['attr'] : '' );
			$wbte_css_class  = ( isset( $wbte_value['css_class'] ) ? esc_attr( $wbte_value['css_class'] ) : '' );
			$wbte_field_only = ( isset( $wbte_value['field_only'] ) ? $wbte_value['field_only'] : false );
			$wbte_non_field  = ( isset( $wbte_value['non_field'] ) ? $wbte_value['non_field'] : false );
			$wbte_mandatory  = (bool) ( isset( $wbte_value['mandatory'] ) ? $wbte_value['mandatory'] : false );

			if ( $wbte_mandatory ) {
				$wbte_fld_attr    .= ' required="required"';
				$wbte_required_msg = ( isset( $wbte_value['required_msg'] ) ? $wbte_value['required_msg'] : '' );
				if ( '' !== $wbte_required_msg ) {
					$wbte_fld_attr .= ' data-required-msg="' . esc_attr( $wbte_required_msg ) . '"';
				}
			}

			$wbte_field_name = esc_attr( $wbte_field_name );
			$wbte_field_id   = esc_attr( $wbte_field_id );

			if ( false === $wbte_field_only ) {
				$wbte_tooltip_html = self::set_tooltip( $wbte_field_name, $base );
				?>
				<tr valign="top" <?php echo wp_kses_post( $wbte_tr_id . $wbte_field_group_attr ); ?> class="<?php echo esc_attr( $wbte_tr_class ); ?>">
					<th scope="row">
						<label style="margin-left:10px;">
							<?php echo wp_kses_post( isset( $wbte_value['label'] ) ? ( $wbte_value['label'] ) : '' ); ?><?php echo wp_kses_post( $wbte_mandatory ? '<span class="wt_gc_required_field">*</span>' : '' ); ?> <?php echo wp_kses_post( $wbte_tooltip_html ); ?>	
						</label>
					</th>
					<td>
				<?php
			}
			if ( true === $wbte_non_field ) {
				if ( 'plaintext' === $type ) {
					echo wp_kses_post( isset( $wbte_value['text'] ) ? $wbte_value['text'] : '' );
				}
			} else {
				echo wp_kses_post( $wbte_before_form_field );

				$wbte_parent_option = ( isset( $wbte_value['parent_option'] ) ? $wbte_value['parent_option'] : '' );

				if ( '' !== $wbte_parent_option ) {
					$wbte_vl = Wbte_Woocommerce_Gift_Cards_Free_Common::get_option( $wbte_parent_option, $base );
					$wbte_vl = ( isset( $wbte_vl[ $wbte_value['option_name'] ] ) ? $wbte_vl[ $wbte_value['option_name'] ] : '' );
				} else {
					$wbte_vl = Wbte_Woocommerce_Gift_Cards_Free_Common::get_option( $wbte_value['option_name'], $base );
				}

				$wbte_vl = is_string( $wbte_vl ) ? stripslashes( $wbte_vl ) : $wbte_vl;
				if ( 'text' === $type || 'number' === $type || 'password' === $type ) {
					?>
					<input type="<?php echo esc_attr( $type ); ?>" <?php echo wp_kses_post( $wbte_fld_attr ); ?> class="<?php echo esc_attr( $wbte_css_class ); ?>" name="<?php echo esc_attr( $wbte_field_name ); ?>" value="<?php echo esc_attr( $wbte_vl ); ?>" />
					<?php

				} elseif ( 'textarea' === $type ) {
					?>
						<textarea <?php echo wp_kses_post( $wbte_fld_attr ); ?> class="<?php echo esc_attr( $wbte_css_class ); ?>" name="<?php echo esc_attr( $wbte_field_name ); ?>"><?php echo esc_textarea( $wbte_vl ); ?></textarea>
					<?php

				} elseif ( 'checkbox' === $type ) {
					$wbte_field_vl       = isset( $wbte_value['field_vl'] ) ? $wbte_value['field_vl'] : '1';
					$wbte_checkbox_label = isset( $wbte_value['checkbox_label'] ) ? $wbte_value['checkbox_label'] : '';
					?>
						<input class="<?php echo esc_attr( $wbte_css_class ); ?>" type="checkbox" value="<?php echo esc_attr( $wbte_field_vl ); ?>" id="<?php echo esc_attr( $wbte_field_id ); ?>" name="<?php echo esc_attr( $wbte_field_name ); ?>" <?php checked( $wbte_field_vl, $wbte_vl ); ?> <?php echo wp_kses_post( $wbte_fld_attr ); ?>>
					<?php
					if ( $wbte_checkbox_label ) {
						?>
						<label for="<?php echo esc_attr( $wbte_field_id ); ?>"><?php echo wp_kses_post( $wbte_checkbox_label ); ?></label>
						<?php
					}
				} elseif ( 'checkbox_list' === $type ) {
					$wbte_checkbox_fields = isset( $wbte_value['checkbox_fields'] ) ? $wbte_value['checkbox_fields'] : array();

					foreach ( $wbte_checkbox_fields as $wbte_checkbox_vl => $wbte_checkbox_label ) {
						$wbte_vl = is_array( $wbte_vl ) ? $wbte_vl : ( ! empty( $wbte_vl ) ? array( $wbte_vl ) : array() );
						?>
						<span class="wt_gc_checkbox_list_item"><input type="checkbox" id="<?php echo esc_attr( $wbte_field_id . '_' . $wbte_checkbox_vl ); ?>" name="<?php echo esc_attr( $wbte_field_name ); ?>[]" class="<?php echo esc_attr( $wbte_css_class ); ?>" value="<?php echo esc_attr( $wbte_checkbox_vl ); ?>" <?php echo wp_kses_post( in_array( $wbte_checkbox_vl, $wbte_vl, true ) ? ' checked="checked"' : '' ); ?> <?php echo wp_kses_post( $wbte_fld_attr ); ?> /> <label for="<?php echo esc_attr( $wbte_field_id . '_' . $wbte_checkbox_vl ); ?>"><?php echo esc_html( $wbte_checkbox_label ); ?></label> </span>
						&nbsp;&nbsp;
						<?php
					}
				} elseif ( 'radio' === $type ) {
					$wbte_radio_fields = isset( $wbte_value['radio_fields'] ) ? $wbte_value['radio_fields'] : array();
					foreach ( $wbte_radio_fields as $wbte_rad_vl => $wbte_rad_label ) {
						?>
						<span class="wt_gc_radio_list_item"><input type="radio" id="<?php echo esc_attr( $wbte_field_id . '_' . $wbte_rad_vl ); ?>" name="<?php echo esc_attr( $wbte_field_name ); ?>" class="<?php echo esc_attr( $wbte_css_class ); ?>" value="<?php echo esc_attr( $wbte_rad_vl ); ?>" <?php checked( $wbte_vl, $wbte_rad_vl ); ?> <?php echo wp_kses_post( $wbte_fld_attr ); ?> /> <label for="<?php echo esc_attr( $wbte_field_id . '_' . $wbte_rad_vl ); ?>"><?php echo esc_html( $wbte_rad_label ); ?></label> </span>
						&nbsp;&nbsp;
						<?php
					}
				}

				if ( 'checkbox' === $type || 'checkbox_list' === $type ) {
					$wbte_hidden_filed_name = $wbte_field_name . '_hidden';
					if ( isset( $wbte_value['parent_option'] ) ) {
						$wbte_hidden_filed_name = $wbte_value['parent_option'] . '[' . $wbte_value['option_name'] . '_hidden]';
					}
					?>
					<input type="hidden" name="<?php echo esc_attr( $wbte_hidden_filed_name ); ?>" value="1" />
					<?php
				}

				echo wp_kses( $wbte_after_form_field, $wbte_allowed_html ); // phpcs:ignore
			}

			if ( isset( $wbte_value['help_text'] ) ) {
				?>
				<span class="wt_gc_form_help"><?php echo wp_kses_post( $wbte_value['help_text'] ); ?></span>
				<?php
			}

			if ( false === $wbte_field_only ) {
				?>
					</td>
					<td>
						<?php echo wp_kses_post( $wbte_after_form_field_html ); ?>
					</td>
				</tr>
				<?php
			}
		}
	}
}