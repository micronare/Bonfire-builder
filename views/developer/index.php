<?php if (!$writeable): ?>
	<div class="alert alert-error">
		<p><?php echo lang('mb_not_writeable_note'); ?></p>
	</div>
<?php endif;?>

<p class="intro"><?php echo lang('builder.intro') ?></p>


<?php if (isset($generators) && is_array($generators) && count($generators)) : ?>
<dl class="dl-horizontal">

	<?php foreach ($generators as $name => $generator) : ?>

		<dt><a href="<?php echo site_url(SITE_AREA .'/developer/builder/generate/'. $name) ?>"><?php echo $generator['title'] ?></a></dt>
		<dd><?php echo $generator['description'] ?></dd>

	<?php endforeach; ?>
</dl>

<?php else: ?>


<?php endif; ?>
