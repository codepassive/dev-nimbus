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
		unset($options[0], $options['id']);
		$this->flag('items', $options);
	}
	
	public function recurse($items){
		foreach ($items as $item => $options) {
			?>
				<li class="item parent">
					<a href="javascript:;"><?php echo $item; ?></a>
					<?php 
						if ($options) {
							echo '<ul class="child">';
							foreach ($options as $option) {
								if ($option != null) {
									echo '<li><a href="javascript:;" class="text">' . $option[0]. '</a>';
									if (isset($option[2])) {
										echo '<a href="javascript:;" class="shortcut">' . $option[2]. '</a>';
									}
									echo '</li>';
									if (is_array($option[1])) {
										$this->recurse($option[1]);
									}
								} else {
									echo '<li class="separator"></li>';
								}
							}
							echo '</ul>';
						}
					?>
				</li>
			<?php
		} 
	}

}

?>