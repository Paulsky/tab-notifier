<?php

/**
 * Handles all admin view rendering for the plugin.
 *
 * @since      1.0.0
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/admin
 */
class Tab_Return_Notifier_Admin_View {

	/**
	 * Render the settings page.
	 *
	 * @param string $active_tab The currently active tab.
	 * @param array $options Plugin options array.
	 * @param array $post_types Available post types.
	 * @param array $taxonomies Available taxonomies.
	 *
	 * @since      1.0.0
	 */
	public function render_settings_page( string $active_tab, array $options, array $post_types, array $taxonomies ): void {
		?>
        <div class="health-check-header">
            <div class="health-check-title-section">
                <h1><?php esc_html_e( 'Tab Return Notifier', 'tab-return-notifier' ); ?></h1>
            </div>

            <div class="health-check-title-section site-health-progress-wrapper hide-if-no-js orange"></div>

            <nav class="health-check-tabs-wrapper hide-if-no-js tab-count-2"
                 aria-label="<?php esc_attr_e( 'Secondary menu', 'tab-return-notifier' ); ?>">
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>"
                   class="health-check-tab <?php echo $active_tab === 'settings' ? 'active' : ''; ?>">
					<?php esc_html_e( 'Settings', 'tab-return-notifier' ); ?>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'preview' ) ); ?>"
                   class="health-check-tab <?php echo $active_tab === 'preview' ? 'active' : ''; ?>">
					<?php esc_html_e( 'Preview', 'tab-return-notifier' ); ?>
                </a>
            </nav>
        </div>

        <hr class="wp-header-end">

		<?php settings_errors( 'tab_return_notifier_messages' ); ?>

        <div class="health-check-body health-check-status-tab hide-if-no-js">
            <form method="post" action="">
				<?php settings_fields( 'tab_return_notifier_settings' ); ?>
				<?php wp_nonce_field( 'tab_return_notifier_settings' ); ?>

				<?php if ( $active_tab === 'settings' ) : ?>
					<?php $this->render_settings_tab( $options, $post_types, $taxonomies ); ?>
				<?php elseif ( $active_tab === 'preview' ) : ?>
					<?php $this->render_preview_tab(); ?>
				<?php endif; ?>
            </form>
        </div>
		<?php
	}

	/**
	 * Render the settings tab content.
	 *
	 * @param array $options Plugin options array.
	 * @param array $post_types Available post types.
	 * @param array $taxonomies Available taxonomies.
	 *
	 * @since      1.0.0
	 */
	protected function render_settings_tab( array $options, array $post_types, array $taxonomies ): void {
		?>
        <h2><?php esc_html_e( 'Settings', 'tab-return-notifier' ); ?></h2>
        <p><?php esc_html_e( 'Configure the tab return notification settings.', 'tab-return-notifier' ); ?></p>

        <div class="site-health-issues-wrapper">
            <h3 class="site-health-issue-count-title"><?php esc_html_e( 'Global Settings', 'tab-return-notifier' ); ?></h3>
            <div class="health-check-accordion">
				<?php
				$content = '<table class="form-table" role="presentation">' .
				           '<tbody>' .
				           $this->render_checkbox_row(
					           'general',
					           $options['general']['enabled'] ?? false,
					           __( 'Enabled', 'tab-return-notifier' ),
					           '',
					           __( 'Enable messages', 'tab-return-notifier' )
				           ) .
				           $this->render_select_row( 'general', $options['general']['animation'] ?? 'rotating', __( 'Animation', 'tab-return-notifier' ), [
					           'rotating'  => __( 'Rotating', 'tab-return-notifier' ),
					           'scrolling' => __( 'Scrolling', 'tab-return-notifier' )
				           ], 'animation', __( 'The animation of the messages', 'tab-return-notifier' )) .
				           $this->render_number_row( 'general', $options['general']['speed'] ?? 500, __( 'Speed', 'tab-return-notifier' ), 'speed', __( 'Animation speed in milliseconds. Most browsers perform better with values of 500 or higher.', 'tab-return-notifier' )) .
				           $this->render_messages_row(
					           'general',
					           $options['general']['messages'] ?? [],
					           '',
					           ''
				           ) .
				           '</tbody>' .
				           '</table>';
				echo $this->render_accordion_item(
					'general',
					__( 'Settings', 'tab-return-notifier' ),
					$content,
					true
				);
				?>
            </div>
        </div>

		<?php if ( ! empty( $post_types ) ) : ?>
            <div class="site-health-issues-wrapper">
                <h3 class="site-health-issue-count-title"><?php esc_html_e( 'Post Types', 'tab-return-notifier' ); ?></h3>
                <p><?php esc_html_e( 'Configure notifications for specific post types.', 'tab-return-notifier' ); ?></p>
                <div class="health-check-accordion">
					<?php foreach ( $post_types as $post_type ) : ?>
						<?php
						$content = '<table class="form-table" role="presentation">' .
						           '<tbody>' .
						           $this->render_checkbox_row(
							           'post_types',
							           $options['post_types'][ $post_type->name ]['enabled'] ?? false,
							           __( 'Enabled', 'tab-return-notifier' ),
							           $post_type->name,
							           sprintf(
								           __( 'Enable for %s', 'tab-return-notifier' ),
								           esc_html( strtolower( $post_type->label ) )
							           )
						           ) .
						           $this->render_messages_row(
							           'post_types',
							           $options['post_types'][ $post_type->name ]['messages'] ?? [],
							           $post_type->name,
							           sprintf(
								           __( 'Leave messages empty to use default messages for %s', 'tab-return-notifier' ),
								           esc_html( strtolower( $post_type->label ) )
							           )
						           ) .
						           '</tbody>' .
						           '</table>';

						echo $this->render_accordion_item(
							$post_type->name,
							$post_type->label,
							$content
						);
						?>
					<?php endforeach; ?>
                </div>
            </div>
		<?php endif; ?>

		<?php if ( ! empty( $taxonomies ) ) : ?>
            <div class="site-health-issues-wrapper">
                <h3 class="site-health-issue-count-title"><?php esc_html_e( 'Taxonomies', 'tab-return-notifier' ); ?></h3>
                <p><?php esc_html_e( 'Configure notifications for specific taxonomies.', 'tab-return-notifier' ); ?></p>
                <div class="health-check-accordion">
					<?php foreach ( $taxonomies as $taxonomy ) : ?>
						<?php
						$content = '<table class="form-table" role="presentation">' .
						           '<tbody>' .
						           $this->render_checkbox_row(
							           'taxonomies',
							           $options['taxonomies'][ $taxonomy->name ]['enabled'] ?? false,
							           __( 'Enabled', 'tab-return-notifier' ),
							           $taxonomy->name,
							           sprintf(
								           __( 'Enable for %s', 'tab-return-notifier' ),
								           esc_html( strtolower( $taxonomy->label ) )
							           )
						           ) .
						           $this->render_messages_row(
							           'taxonomies',
							           $options['taxonomies'][ $taxonomy->name ]['messages'] ?? [],
							           $taxonomy->name,
							           sprintf(
								           __( 'Leave blank to use default messages for %s', 'tab-return-notifier' ),
								           esc_html( strtolower( $taxonomy->label ) )
							           )
						           ) .
						           '</tbody>' .
						           '</table>';
						echo $this->render_accordion_item(
							$taxonomy->name,
							$taxonomy->label,
							$content
						);
						?>
					<?php endforeach; ?>
                </div>
            </div>
            <emoji-picker style="display: none;" class="light"></emoji-picker>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the preview tab content.
	 * @since      1.0.0
	 */
	protected function render_preview_tab(): void {
		?>
        <h2><?php esc_html_e( 'Preview', 'tab-return-notifier' ); ?></h2>
        <p><?php esc_html_e( 'Please see the preview below. Note that only the general messages are displayed, and all variables are placeholder data.', 'tab-return-notifier' ); ?></p>

        <div class="site-health-issues-wrapper">
            <div class="trn-tab-preview">
                <img id="trn-tab-favicon" alt="Favicon" />
                <span id="trn-tab-title"></span>
                <button class="trn-tab-close">×</button>
            </div>
        </div>
		<?php
	}

	/**
	 * Render an accordion item.
	 *
	 * @param string $name Unique identifier for the accordion item.
	 * @param string $title Title to display.
	 * @param string $content HTML content to display in the accordion body.
	 * @param bool $expanded Whether the item should be expanded by default.
	 *
	 * @return string HTML for the accordion item.
	 * @since      1.0.0
	 *
	 */
	public function render_accordion_item( string $name, string $title, string $content, bool $expanded = false ): string {
		ob_start(); ?>
        <h4 class="health-check-accordion-heading">
            <button aria-expanded="<?php echo $expanded ? 'true' : 'false'; ?>"
                    class="health-check-accordion-trigger"
                    aria-controls="health-check-accordion-block-<?php echo esc_attr( $name ); ?>"
                    type="button">
                <span class="title"><?php echo esc_html( $title ); ?></span>
                <span class="icon"></span>
            </button>
        </h4>
        <div id="health-check-accordion-block-<?php echo esc_attr( $name ); ?>"
             class="health-check-accordion-panel" <?php echo $expanded ? '' : 'hidden="hidden"'; ?>>
			<?php echo $content; ?>
            <div class="site-health-accordion-actions">
				<?php submit_button(); ?>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a checkbox row.
	 *
	 * @param string $name Field name prefix.
	 * @param bool $checked Whether the checkbox is checked.
	 * @param string $label Label text.
	 * @param string $array_key Optional array key for nested fields.
	 * @param string $description Optional description text.
	 *
	 * @return string HTML for the checkbox row.
	 * @since      1.0.0
	 *
	 */
	public function render_checkbox_row( string $name, bool $checked, string $label, string $array_key = '', string $description = '' ): string {
		$field_id   = esc_attr( $name ) . ( ! empty( $array_key ) ? '_' . esc_attr( $array_key ) : '' ) . '_enabled';
		$field_name = ! empty( $array_key )
			? 'tab_return_notifier_options[' . esc_attr( $name ) . '][' . esc_attr( $array_key ) . '][enabled]'
			: 'tab_return_notifier_options[' . esc_attr( $name ) . '][enabled]';

		ob_start();
		?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
            </th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo esc_html( $label ); ?></span>
                    </legend>
                    <label for="<?php echo esc_attr( $field_id ); ?>">
                        <input name="<?php echo esc_attr( $field_name ); ?>" type="checkbox"
                               id="<?php echo esc_attr( $field_id ); ?>"
                               value="1" <?php checked( $checked ); ?>>
						<?php if ( $description ) : ?>
							<?php echo wp_kses_post( $description ); ?>
						<?php endif; ?>
                    </label>
                </fieldset>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a messages input row.
	 *
	 * @param string $name Field name prefix.
	 * @param array $value Array of message values.
	 * @param string $array_key Optional array key for nested fields.
	 * @param string $description Optional description text.
	 *
	 * @return string HTML for the messages row.
	 * @since      1.0.0
	 *
	 */
	public function render_messages_row( string $name, array $value, string $array_key = '', string $description = '' ): string {
		$field_id   = esc_attr( $name ) . ( ! empty( $array_key ) ? '_' . esc_attr( $array_key ) : '' ) . '_messages';
		$field_name = ! empty( $array_key )
			? 'tab_return_notifier_options[' . esc_attr( $name ) . '][' . esc_attr( $array_key ) . '][messages]'
			: 'tab_return_notifier_options[' . esc_attr( $name ) . '][messages]';

		ob_start();
		?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Messages', 'tab-return-notifier' ); ?></label>
            </th>
            <td>
				<?php echo $this->render_messages_element( $field_name, $value, $description ); ?>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render messages element.
	 *
	 * @param string $field_name Name attribute for the input fields.
	 * @param array $messages Array of message strings.
	 * @param string $description Optional description text.
	 *
	 * @return string HTML for the messages element.
	 * @since      1.0.0
	 *
	 */
	public function render_messages_element( string $field_name, array $messages = [], string $description = '' ): string {
		ob_start();
		?>
        <div class="trn-messages-wrapper">
            <div class="trn-messages-container">
				<?php if ( empty( $messages ) ) : ?>
					<?php echo $this->render_message_input_group( $field_name ); ?>
				<?php else : ?>
					<?php foreach ( $messages as $index => $message ) : ?>
						<?php echo $this->render_message_input_group( $field_name, $message ); ?>
					<?php endforeach; ?>
				<?php endif; ?>
                <button type="button"
                        class="button trn-add-message"><?php esc_html_e( 'Add another message', 'tab-return-notifier' ); ?></button>
            </div>
			<?php if ( $description ) : ?>
                <p class="description"><?php echo wp_kses_post( $description ); ?></p>
			<?php endif; ?>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render message input group.
	 *
	 * @param string $field_name Name attribute for the input field.
	 * @param string $message Current message value.
	 *
	 * @return string HTML for the message input group.
	 * @since      1.0.0
	 *
	 */
	public function render_message_input_group( string $field_name, string $message = '' ): string {
		$name_attribute    = $field_name . '[]';
		$formatted_message = preg_replace( '/\{\{(.*?)\}\}/', '&ZeroWidthSpace;<code class="variable">{{$1}}</code>&ZeroWidthSpace;', esc_html( $message ) );
		ob_start();
		?>
        <div class="trn-message-input-group">
            <span type="button" class="trn-drag-handle" aria-label="<?php esc_attr_e('Drag to reorder', 'tab-return-notifier'); ?>">
                <span class="dashicons dashicons-menu"></span>
            </span>
            <input type="hidden" name="<?php echo esc_attr( $name_attribute ); ?>"
                   value="<?php echo esc_attr( $message ); ?>">
            <div class="trn-editable-input" contenteditable="true"
                 data-field-name="<?php echo esc_attr( $name_attribute ); ?>"><?php echo $formatted_message; ?></div>
            <div class="trn-input-group-actions button-group">
                <button type="button"
                        class="button trn-insert-emoji"><?php esc_html_e( 'Insert emoji', 'tab-return-notifier' ); ?></button>
                <button type="button"
                        class="button trn-insert-variable"><?php esc_html_e( 'Insert variable', 'tab-return-notifier' ); ?></button>
                <button type="button"
                        class="button trn-remove-message"><?php esc_html_e( 'Remove', 'tab-return-notifier' ); ?></button>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a select dropdown row.
	 *
	 * @param string $name         Field name prefix.
	 * @param string $selected     Selected value.
	 * @param string $label        Label text.
	 * @param array  $options      Select options (value => label).
	 * @param string $array_key    Optional array key for nested fields.
	 * @param string $description  Optional description text.
	 * @param bool   $multiple     Whether to allow multiple selections.
	 * @since      1.0.0
	 *
	 * @return string HTML for the select row.
	 */
	public function render_select_row(string $name, string $selected, string $label, array $options, string $array_key, string $description = '', bool $multiple = false): string {
		$field_id = esc_attr($name) . (!empty($array_key) ? '_' . esc_attr($array_key) : '') . '_select';
		$field_name = 'tab_return_notifier_options[' . esc_attr($name) . '][' . esc_attr($array_key) . ']';

		if ($multiple) {
			$field_name .= '[]';
		}

		ob_start();
		?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?></label>
            </th>
            <td>
                <select name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_id); ?>"
					<?php echo $multiple ? 'multiple' : ''; ?>>
					<?php foreach ($options as $value => $option_label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"
							<?php selected(
								$multiple
									? in_array($value, (array)$selected)
									: $value === $selected
							); ?>>
							<?php echo esc_html($option_label); ?>
                        </option>
					<?php endforeach; ?>
                </select>
				<?php if ($description) : ?>
                    <p class="description"><?php echo wp_kses_post($description); ?></p>
				<?php endif; ?>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a number input row.
	 *
	 * @param string $name         Field name prefix.
	 * @param int    $value        Current value.
	 * @param string $label        Label text.
	 * @param string $array_key    Optional array key for nested fields.
	 * @param string $description  Optional description text.
	 * @since      1.0.0
	 *
	 * @return string HTML for the number input row.
	 */
	public function render_number_row(string $name, int $value, string $label, string $array_key, string $description = ''): string {
		$field_id = esc_attr($name) . (!empty($array_key) ? '_' . esc_attr($array_key) : '') . '_number';
		$field_name = 'tab_return_notifier_options[' . esc_attr($name) . '][' . esc_attr($array_key) . ']';

		ob_start();
		?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?></label>
            </th>
            <td>
                <input name="<?php echo esc_attr($field_name); ?>" type="number" id="<?php echo esc_attr($field_id); ?>"
                       value="<?php echo esc_attr($value); ?>" min="1" />
				<?php if ($description) : ?>
                    <p class="description"><?php echo wp_kses_post($description); ?></p>
				<?php endif; ?>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}


}