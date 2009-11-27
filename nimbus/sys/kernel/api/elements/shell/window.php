<div class="window <?php echo implode(' ', $window['classes']); ?>" id="<?php echo $window['pid']; ?>">
	<div class="window-outer">
		<?php if ($window['resizable'] === true) { ?>
		<div id="nimbus-log-window-resizeSE" class="handleSE resize-handles"></div><div id="nimbus-log-window-resizeE" class="handleE resize-handles"></div><div id="nimbus-log-window-resizeNE" class="handleNE resize-handles"></div><div id="nimbus-log-window-resizeN" class="handleN resize-handles"></div><div id="nimbus-log-window-resizeNW" class="handleNW resize-handles"></div><div id="nimbus-log-window-resizeW" class="handleW resize-handles"></div><div id="nimbus-log-window-resizeSW" class="handleSW resize-handles"></div><div id="nimbus-log-window-resizeS" class="handleS resize-handles"></div>
		<?php } ?>
		<div class="window-title">
			<div class="title-icon">
				<a href="#context-nimbus-log" class="window-context"><img src="<?php echo $window['icon']; ?>" width="16" height="16"/></a>
			</div>
			<div class="title-caption"><?php echo $window['title']; ?></div>
			<div class="title-actions">
				<a href="http://feedback.nimbusdesktop.org/" target="_new" class="window-feedback">Send Feedback</a>
				<?php if ($window['minimizable'] === true) { ?><a href="#minimize-nimbus-log" class="window-minimize"></a><?php } ?>
				<?php if ($window['toggable'] === true) { ?><a href="#toggle-nimbus-log" class="window-toggle"></a><?php } ?>
				<?php if ($window['closable'] === true) { ?><a href="#close-nimbus-log" class="window-close"></a><?php } ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="window-inner">
			<div class="window-content-wrapper">
				<div class="window-content-outer">
					<div class="window-content-inner">
						<div class="window-toolbars"><?php echo $toolbar['menu']; ?></div>
						<?php echo $content; ?><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
						<div class="window-statusbar"><?php echo $toolbar['status']; ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
