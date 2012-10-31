<?php if (validation_errors()) : ?>
<div class="alert alert-error">
	<h4 class="alert-heading">Please fix the following errors :</h4>
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">
	<h3>{module_ucw}</h3>

	<?php echo form_open(current_url(), 'class="form-horizontal"'); ?>

	{form_fields}

	<div class="form-actions">
		<br/>
		<input type="submit" name="save" class="btn btn-primary" value="{action} {module}" />
		&nbsp;&nbsp;or&nbsp;&nbsp;
		<a href=""><?php echo lang('bf_action_cancel'); ?></a>
	</div>

	<?php echo form_close(); ?>
</div>