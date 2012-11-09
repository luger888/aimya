<?php   

class Aimya_Cache extends Zend_Controller_Plugin_Abstract{

	public function dispatchLoopShutdown(){
	$frontendOptions = array(
			'lifetime' => 604800,
			'debug_header' => false,
			'default_options' => array(
				'cache' => false
			),
			'regexps' => array(
				'^/news' => array('cache' => true),
				'^/catalog' => array('cache' => true)
			)
        );

  		$backendOptions = array(
		    'cache_dir' => './tmp/',
		);
		 
		
		$cache_id=$_SERVER['REQUEST_URI'];

		$lastSymbol = $cache_id{strlen($cache_id)-1};
		if($lastSymbol=='/'){
			$cache_id = substr($cache_id, 0, -1);
		}
		
		$cache = Zend_Cache::factory('Page',
		                             'File',
		                             $frontendOptions,
		                             $backendOptions);
//
		$cache->start(md5($cache_id));
	}
}


?>  