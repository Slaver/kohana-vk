<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Auth module for Open API vk.com
 *
 * @package    Vk_Auth
 * @author     Viacheslav Radionov
 * @copyright  (c) 2010 Viacheslav Radionov <radionov@gmail.com>
 * @license    http://kohanaphp.com/license.html
 */
class Vk_Auth {

	// Instances
	protected static $instance;

	/**
	 * Singleton pattern
	 *
	 * @return Vk_Auth
	 */
	public static function instance()
	{
		if ( ! isset(Vk_Auth::$instance))
		{
			// Load the configuration for this type
			$config = Kohana::config('vk');

			// Create a new session instance
			Vk_Auth::$instance = new Vk_Auth($config);
		}

		return Vk_Auth::$instance;
	}

	protected $config;
	protected $session;

	/**
	 * Loads Session and configuration options
	 *
	 * @return  void
	 */
	public function __construct($config = array())
	{
		$this->config = $config;
		$this->session = Session::instance();
	}

	/**
	 * Gets the currently logged in user from the session
	 * Returns FALSE if no user is currently logged in
	 *
	 * @return  mixed
	 */
	public function get_user()
	{
		if ($this->logged_in())
		{
			return $this->session->get($this->config['VK_SESSION_KEY']);
		}

		return FALSE;
	}

	/**
	 * Check if there is an active session
	 *
	 * @return  boolean
	 */
	public function logged_in()
	{
		return (bool) $this->session->get($this->config['VK_SESSION_KEY'], FALSE);
	}

	/**
	 * Check COOKIE data and get full profile information of vk-user
	 */
	public function login()
	{
		if ( $vk_cookie = arr::get($_COOKIE, 'vk_app_'.$this->config['VK_API_ID']))
		{
			if ( ! empty($vk_cookie))
			{
				$cookie_data = array();

				foreach (explode('&', $vk_cookie) as $item)
				{
					$item_data = explode('=', $item);
					$data[$item_data[0]] = $item_data[1];
				}

				// Check Auth SIG
				$string = sprintf("expire=%smid=%ssecret=%ssid=%s%s", $data['expire'], $data['mid'], $data['secret'], $data['sid'], $this->config['VK_API_PASSWORD']);

				if (md5($string) === $data['sig'] && $data['expire'] > time())
				{
					$ch = curl_init();

					if (isset($ch))
					{
						// Save alphabetical order! or use ksort() after
						$request = array(
							'api_id'	=> $this->config['VK_API_ID'],
							'code'		=> 'return {me: API.getProfiles({uids: API.getVariable({key: 1280}), fields: "'.implode(', ',$this->config['VK_API_FILEDS']).'"})[0]};',
							'format'	=> 'JSON',
							'method'	=> 'execute',
							'v'			=> '3.0',
						);

						// Generate API SIG
						// @link http://vkontakte.ru/pages.php?o=-1&p=Взаимодействие приложения с API
						$sig = $data['mid'];
						foreach ($request as $key => $val)
						{
							$sig .= $key.'='.$val;
						}
						$sig .= $data['secret'];
						$request['sig'] = md5($sig);
						$request['sid'] = $data['sid'];

						// Generate request
						$request = $this->config['VK_API_PATH'].'?'.http_build_query($request);

						// Set multiple options for a cURL transfer
						$options = array(
							CURLOPT_URL			=> $request,
							CURLOPT_HEADER		=> FALSE,
							CURLOPT_USERAGENT	=> 'Kohana '.Kohana_Core::VERSION,
							CURLOPT_RETURNTRANSFER	=> TRUE,
							CURLOPT_CONNECTTIMEOUT	=> '30',
						);
						curl_setopt_array($ch, $options);    
						$response = curl_exec($ch);
						curl_close($ch);

						if ( ! empty($response))
						{
							$login = json_decode($response, TRUE);

							$this->session->set($this->config['VK_SESSION_KEY'], $login['response']['me']);

							return TRUE;
						}
					}
				}
			}
		}		
		return FALSE;
	}

	/**
	 * Log out a user by removing the related session and cookie info
	 *
	 * @return  boolean
	 */
	public function logout()
	{
		// Empty cookies
		if (setcookie('vk_app_'.$this->config['VK_API_ID'], '', 0, '/', '.'.$_SERVER['HTTP_HOST']))
		{
			$this->session->delete($this->config['VK_SESSION_KEY']);
			$this->session->regenerate();
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Gets configuration options
	 * 
	 * @return  array
	 */
	public function get_config()
	{
		return $this->config;
	}

} // End Vk_Auth