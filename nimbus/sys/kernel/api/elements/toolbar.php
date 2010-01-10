<?php
/** 
 * Nimbus - Manage, Share & Collaborate
 *
 * Nimbus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * see LICENSE for more Copyright goodness.
 *
 * @package:		Nimbus
 * @subpackage:	Nimbus_api
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Window Toolbar
 *
 * @category:   		API/Elements
 */
class toolbar extends Elements implements ElementInterface {
	
	/**
	 * Name of the Element
	 *
	 * @access:	Public
	 */
	public $name = 'toolbar';

	/**
	 * Class constructor
	 *
	 * @access:	Public
	 */
	public function __construct($options = array()){
		$options['id'] = 'toolbar-window-' . generateHash();
		parent::__construct($options);
		$this->flag('as', $options[0]);
		$this->flag('handle', $options['handle']);
		unset($options[0], $options['handle'], $options['id']);
		$this->flag('items', $options);
	}
	
	public function recurse($items, $recursed = false){
		foreach ($items as $item => $options) {
			$cursed = ($recursed == true) ? ' parent-text': '';
			?>
				<li class="item">
					<a href="javascript:;" class="parent"><span><?php echo $item; ?></span></a>
					<?php 
						if ($options) {
							$cursed = ($recursed == true) ? ' parent': '';
							echo '<div class="child' . $cursed . '"><ul>';
							foreach ($options as $option) {
								if ($option != null) {
									$id = 'menuItem-' . generateHash();
									$disabled = '';
									if (strstr($option[0], ":")) {
										$option[0] = explode(":", $option[0]);
										$option[0] = $option[0][0];
										$disabled = ' class="disabled"';
									}
									echo '<li' . $disabled . ' id="' . $id . '"><a href="javascript:;" class="text">' . $option[0]. '</a>';
									$option[2] = (isset($option[2])) ? $option[2]: '&nbsp;';
									echo '<a href="javascript:;" class="shortcut">' . $option[2]. '</a>';
									echo '<div class="clear"</div></li>';
									if (isset($option[1])){
										if (is_array($option[1])) {
											$this->recurse($option[1], true);
										} else {
											if (isset($option[3])) {
												Application::bindEvent('click', $id, $this->flag('handle'), $this->flag('handle') . '[' . $this->flag('handle') . '_instance].' . $option[1] . '(this, \'' . $option[3] . '\')', true);
											} else {
												Application::bindEvent('click', $id, $this->flag('handle'), $this->flag('handle') . '[' . $this->flag('handle') . '_instance].' . $option[1] . '(this)', true);
											}
										}
									}
								} else {
									echo '<li class="separator"><span></span></li>';
								}
							}
							echo '</ul></div>';
						}
					?>
				</li>
			<?php
		} 
	}

}

?>