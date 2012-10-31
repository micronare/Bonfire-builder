<div class="admin-box">
	<h3>{module_ucw}</h3>
	<?php echo form_open(current_url()); ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<?php if ($this->auth->has_permission('{delete_permission}') && isset($records) && is_array($records) && count($records)) : ?>
					<th class="column-check"><input class="check-all" type="checkbox" /></th>
					<?php endif;?>
					{table_header}
				</tr>
			</thead>

			<?php if (isset($records) && is_array($records) && count($records)) : ?>
			<tfoot>
				<?php if ($this->auth->has_permission('{delete_permission}')) : ?>
				<tr>
					<td colspan="{cols_total}">
						<?php echo lang('bf_with_selected') ?>
						<input type="submit" name="delete" id="delete-me" class="btn btn-danger" value="<?php echo lang('bf_action_delete') ?>" onclick="return confirm('<?php echo lang('{module_lower}_delete_confirm'); ?>')">
					</td>
				</tr>
				<?php endif;?>
			</tfoot>
			<?php endif; ?>

			<tbody>
			<?php if (isset($records) && is_array($records) && count($records)) : ?>
				<?php foreach ($records as $record) : ?>
					<tr>
						<?php if ($this->auth->has_permission('{delete_permission}')) : ?>
						<td><input type="checkbox" name="checked[]" value="<?php echo $record->{$primary_key_field} ?>" /></td>
						<?php endif;?>
						{table_records}
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="{cols_total}">No records found that match your selection.</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	<?php echo form_close(); ?>
</div>