<style>
.preview { margin: 0 2em 0 2em; width: 85%; }
.preview th {  }
.preview td { background: #555; color: #eee; padding: 3px 7px; border: 1px solid #999; border-collapse: collapse; font-weight: lighter; }
.preview td:last-child { text-align: center; width: 8em; }
</style>

<h2><?php e($title); ?></h2>

<p><?php e($description); ?></p>

<br/>

<?php if (validation_errors()) :?>
<div class="alert alert-error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<?php echo form_open(current_url(), 'class="form-horizontal"'); ?>

<?php echo $form; ?>

	<?php if (isset($preview)) :?>

		<h3>Files to be Created</h3>
		<?php echo $preview ?>

		<div class="form-actions">
			<input type="submit" name="form-submit-generate" class="btn btn-primary" value="Generate It" />
			&nbsp;&nbsp;or&nbsp;&nbsp;
			<a href="<?php echo site_url(SITE_AREA .'/developer/builder/generate') ?>">Cancel</a>
		</div>

	<?php elseif (isset($status)) :?>

		<h3>Generation Results</h3>

		<pre><?php
			foreach ($status as $file => $success)
			{
				if ($success == 'success')
					echo 'CREATED '. $file ."\n";
				else
					echo 'FAILED '. $file ."\n";
			}
		?></pre>

		<div class="form-actions">
			<a href="<?php echo site_url(SITE_AREA .'/developer/builder/generate') ?>">Back to Code Builder</a>
		</div>

	<?php else: ?>
		<div class="form-actions">
			<input type="submit" name="form-submit-preview" class="btn btn-primary" value="Preview" />
			&nbsp;&nbsp;or&nbsp;&nbsp;
			<a href="<?php echo site_url(SITE_AREA .'/developer/builder/generate') ?>">Cancel</a>
		</div>
	<?php endif; ?>



<?php echo form_close(); ?>