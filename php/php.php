<?php

define('ROOT', __DIR__);

date_default_timezone_set('Asia/Shanghai');

class PHP
{
	//文件内容
	public $data = [];
	public $tokens = [];

	public static $_instance = NULL;

	/**
	 * @return PHP
	 */
	public static function instance()
	{
		if (is_null(PHP::$_instance))
		{
			PHP::$_instance = new PHP();
		}
		return PHP::$_instance;
	}

	public function __construct()
	{
		//判断是否存在缓存文件
		if (file_exists(ROOT.'/tokens.json'))
		{
			//根据md5判断hosts文件是否一致
			$this->tokens = json_decode(file_get_contents(ROOT.'/tokens.json'), TRUE);
		}
		else
		{
			//判断是否存在缓存文件
			if (file_exists(ROOT.'/data.json'))
			{
				//根据md5判断hosts文件是否一致
				$this->data = json_decode(file_get_contents(ROOT.'/data.json'), TRUE);
			}

			foreach ($this->data['data'] as $type => $list)
			{
				foreach ($list['elements'] as $id => $function)
				{
					foreach ($function['tokens'] as $token)
					{
						if (!isset($this->tokens[$token]))
						{
							$this->tokens[$token] = [];
						}
						$this->tokens[$token][] = [
							'type'			=> $type,
							'id'			=> $id,
							'name'			=> $function['name'],
							'length'		=> strlen($function['name']),
							'description'	=> $function['description'],
						];
					}
				}
			}
			file_put_contents(ROOT.'/tokens.json', json_encode($this->tokens));
		}
	}
	
	public function search($string)
	{
		$result = [];
		foreach ($this->tokens as $token => $data)
		{
			if (stripos($token, $string) !== FALSE)
			{
				foreach ($data as $function)
				{
					if (!isset($result[$function['id']]))
					{
						$result[$function['id']] = $function;
					}
				}
			}
		}

		//按length从低到高
		uasort($result, function($a, $b){
			if ($a['length'] == $b['length']) {
				return 0;
			}
			return ($a['length'] < $b['length']) ? -1 : 1;
		});
		return $result;
	}
}