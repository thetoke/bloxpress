<?php

/**
 *  BloxPress2
 *  ----------------------
 *  Copyright (C) 2006-2007, Kjell Bublitz
 *  www.bloxpress.org / m3nt0r.de@gmail.com
 *
 *  Bloxpress Class
 *  
 *  Responsible for loading enviroment data and register sidebars
 *
*/

class Bloxpress {
	var $layout;
	var $columns;
	var $sidebars;
	var $widgets;
	var $cookie;
	var $setup;
	
	function Bloxpress() {
		
		$this->setup = array(
			'before_widget' => '<li id="block_%1$s" class="block %2$s"><div class="yui-panel yui-dialog shadow">',
			'before_title' => '<div class="hd block_handle">',
			'after_title' => '</div><div class="bd block_content">',
			'after_widget' => '</div><div class="container-close"></div><div class="container-minmax maxImage"></div></div></li>'
		);
		
		$this->registerSidebars();
		$this->loadLayout();
		$this->loadWidgets();
		$this->loadCookie();
	}
	
	
	function getSidebars() {
		$sidebars = array();
		if($this->layout) {
			foreach($this->columns as $sidebar_id) {
				$sidebars[$sidebar_id] = new BloxpressSidebar($this->layout[$sidebar_id], $this->sidebars[$sidebar_id]);
			}
		}
		return $sidebars;
	}
	
	function registerSidebars() {
		global $wp_registered_sidebars;
		$sidebarNames = array("Left Side", "Center", "Right Side");
		foreach ($sidebarNames as $i => $sidebarName){
			$this->columns[$i] = "sidebar-".($i+1);
			$sidebarParams = array_merge(array('id'=>$this->columns[$i],'name'=>$sidebarName), $this->setup);
			register_sidebar( $sidebarParams );
		}
		$this->sidebars = $wp_registered_sidebars;
	}
	
	function loadCookie() {
		if($_COOKIE['bloxpress']) {
			parse_str($_COOKIE['bloxpress'], $this->cookie);
			foreach(array_keys($this->cookie) as $column)
			{			
				for($i = 0; $i < count($this->cookie[$column]); $i++)
				{
					$block = explode('_', $this->cookie[$column][$i]); // explode block ID
					$block_name = $block[1]; //  block_recent-posts_5395
					
					foreach($this->widgets as $widget_name => $widget) // find the name by id
					{
						$cookieLayout[$column][$i] = $block_name; // overwrite the value of the cookie array
					}			
				}
			}
			$this->layout = $cookieLayout;
			$this->columns = array_keys($cookieLayout);
			
		} else {
			$this->cookie = false;	
		}
	}
	
	function loadLayout() {
		$this->layout = get_option('sidebar_widgets');
	}
	
	function loadWidgets() {
		global $wp_registered_widgets;
		wp_widgets_init();
		include(BP_BASEDIR.DS.'widgets'.DS.'custom.php');
		$this->widgets = $wp_registered_widgets;
	}

	function attachRandomId($id) {
		srand((double) microtime() * 500000);
		$random_number = rand();
		return $id.'_'.$random_number;
	}
}
?>