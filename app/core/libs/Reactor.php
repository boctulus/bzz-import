<?php

namespace boctulus\SW\core\libs;

/*
   	@author Pablo Bozzolo (2023)

	Reacciona a la creacion, modificacion, borrado y restauracion de posts
	incluidos Custom Post Types

	NO funciona con otras tablas como las de Users 

	Para Users extender UserReactor en su lugar
*/

abstract class Reactor
{
	protected $action;
	protected $ignored_types = [];

	function __construct()
	{
		add_action('publish_post',       [$this, 'sync_on_create'], 10, 1 );
		add_action('save_post',          [$this, 'sync_on_update'], 10, 1 );
		add_action('before_delete_post', [$this, 'sync_on_trash'], 10, 1);
		add_action('untrash_post',       [$this, 'sync_on_untrash'], 10, 1);
	}	

	function log($pid){
		$title     = get_the_title($pid);
    	$post_type = get_post_type($pid);

		Logger::dd("$title [$post_type][post_id=$pid]", $this->action);
	}

	function sync_on_create($pid) {
		$post_type = get_post_type($pid);

		if (in_array($post_type, $this->ignored_types)){
			return;
		}

		$this->action = 'create';
	
		$this->__onCreate($pid);
	}

	function sync_on_update($pid) {
		$post_type = get_post_type($pid);

		if (in_array($post_type, $this->ignored_types)){
			return;
		}

		$this->action = 'edit';
	
		$this->__onUpdate($pid);
	}

	function sync_on_trash($pid)
	{
		$post_type = get_post_type($pid);

		if (in_array($post_type, $this->ignored_types)){
			return;
		}

		$this->action = 'trash';
		
		$this->__onDelete($pid);
	}

	function sync_on_untrash($pid)
	{
		$post_type = get_post_type($pid);

		if (in_array($post_type, $this->ignored_types)){
			return;
		}

		$this->action = 'untrash';
		
		$this->__onRestore($pid);
	}

    /*
		Event Hooks
	*/
	
	protected function __onCreate($pid){		
		// if (!empty(get_transient('the_post-'. $pid))){
		// 	return;
		// }
		
		// set_transient('the_post-'. $pid, true, 2);

        $this->onCreate($pid);
	}

	protected function __onUpdate($pid)
	{
		// if (!empty(get_transient('the_post-'. $pid))){
		// 	return;
		// }

		// set_transient('the_post-'. $pid, true, 2);	

        $this->onUpdate($pid);
	}

	protected function __onDelete($pid)
	{
        $this->onDelete($pid);
	}

	protected function __onRestore($pid)
	{	
        $this->onRestore($pid);
	}


	function onCreate ($pid){}
	function onUpdate ($pid){}
	function onDelete ($pid){}
	function onRestore($pid){}

}