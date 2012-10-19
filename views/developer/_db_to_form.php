<br/>

<?php if (isset($fields) && is_array($fields) && count($fields)) : ?>

	<table class="table table-condensed table-striped table-bordered table-hover">
		<caption>Table: <?php e($table) ?></caption>
		<thead>
			<tr>
				<th></th>
				<th>Field</th>
				<th>Input Type</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($fields as $field) : ?>
			<tr>
				<td>
					<?php
						$checked_default = !in_array($field->name, array('created_on', 'modified_on', 'deleted', 'deleted_on', 'created_by', 'modified_by', 'deleted_by'));
						if ($field->primary_key)
						{
							$checked_default = false;
						}
					?>
					<input type="checkbox" name="fields[<?php echo $field->name ?>]" value="1" <?php echo set_checkbox('inputs['. $field->name .']', 1, $checked_default); ?> />
				</td>
				<td><?php e($field->name) ?></td>
				<td>
					<?php
						$datetime_default 	= in_array($field->name, array('created_on', 'modified_on', 'deleted_on'));
						$date_default 		= strpos($field->name, 'date') !== false;
						$email_default		= strpos($field->name, 'email') !== false;
						$number_default		= in_array($field->name, array('tinyint', 'bigint', 'int')) || strpos($field->name, '_id') !== false;

						$input_default 		= (!$datetime_default && !$number_default && !$email_default && !$date_default) ? true : false;

					?>
					<select name="inputs[<?php echo $field->name ?>]">
						<option <?php echo set_select('inputs['. $field->name .']', 'Checkbox'); ?>>Checkbox</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Date', $date_default); ?>>Date</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'DateTime', $datetime_default); ?>>DateTime</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Dropdown'); ?>>Dropdown</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Email', $email_default); ?>>Email</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Input', $input_default); ?>>Input</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Month'); ?>>Month</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Number', $number_default); ?>>Number</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Range'); ?>>Range</option>
						<option <?php echo set_select('inputs['. $field->name .']', 'Textarea'); ?>>Textarea</option>
					</select>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

<?php else: ?>

	<div class="alert alert-warning">
		No Fields were found in table: <?php echo $table ?>.
	</div>

<?php endif; ?>