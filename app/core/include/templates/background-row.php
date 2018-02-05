<tr class="form-field hide-if-no-js">
	<th scope="row" valign="top">
		<label><?php _e('Фоновое изображение', 'knife-theme') ?></label>
	</th>

	<?php
		$sizes = [
			'auto' => __('Замостить фон', 'knife-theme'),
			'cover' => __('Растянуть изображение', 'knife-theme'),
			'contain' => __('Подогнать по ширине', 'knife-theme')
		];

		$meta = get_term_meta($term->term_id, $this->meta, true);
	?>

	<td>
		<div id="knife-term-background">
			<div class="knife-actions">
				<button class="knife-select button" type="button"><?php _e('Выбрать изображение', 'knife-theme'); ?></button>
 				<a class="knife-delete" href="#delete-term-background"><?php _e('Удалить', 'knife-theme'); ?></a>
			</div>

			<div class="knife-size">
				<select name="<?php echo esc_attr($this->meta); ?>[size]">
				<?php
					foreach($sizes as $name => $title) {
						printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($meta['size'], $name, false));
					}
				?>
				</select>
			</div>

			<input type="hidden" class="knife-input" name="<?php echo esc_attr($this->meta); ?>[image]" value="<?php echo $meta['image'] ?? ''; ?>">
		</div>
	</td>
</tr>
