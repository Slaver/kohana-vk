<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Vk extends Controller {

	public function action_index()
	{
		$user = Vk_Auth::instance()->get_user();
		if ($user === FALSE)
		{
			if (Vk_Auth::instance()->login())
			{
				Request::instance()->redirect('/vk');
			}
		}
		$this->request->response = View::factory('vk')
			->set('config', Vk_Auth::instance()->get_config())
			->set('user', $user);
	}

	public function action_logout()
	{
		if (Vk_Auth::instance()->logout())
		{
			Request::instance()->redirect('/vk');
		}
	}

} // End Vk