<?php

define('ROOT', __DIR__);
define('HOSTS', '/etc/hosts');

class Hosts_Switch
{
	//文件内容
	public $data = [];
	//hosts文件md5
	public $md5 = '';
	//当前使用的group
	public $group = 'default';
	//需要写入hosts文件的内容
	public $hosts = [];

	public static $_instance = NULL;

	/**
	 * @return Hosts_Switch
	 */
	public static function instance()
	{
		if (is_null(Hosts_Switch::$_instance))
		{
			Hosts_Switch::$_instance = new Hosts_Switch();
		}
		return Hosts_Switch::$_instance;
	}

	public function __construct()
	{
		//hosts相关

		//判断是否存在缓存文件
		if (file_exists(ROOT.'/data.json'))
		{
			//根据md5判断hosts文件是否一致
			$data = json_decode(file_get_contents(ROOT.'/data.json'), TRUE);

			if ($data['md5'] == md5_file(HOSTS))
			{
				//文件一致 初始化结束
				$this->data = $data;
				$this->md5 = $this->data['md5'];
				$this->group = $this->data['group'];
				$this->hosts = $this->data['hosts'][$this->group];
			}
		}
		$this->init();
	}

	public function __destruct()
	{
		$this->save();
	}

	//从文件初始化
	public function init()
	{
		if (!empty($this->md5))
		{
			return TRUE;
		}
		$this->md5 = md5_file(HOSTS);

		//从hosts文件中读取数据
		$data = file(HOSTS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($data as $item)
		{
			$item = trim($item);
			if (in_array(substr($item, 0, 1), ['#', ';']))
			{
				continue;
			}

			$item = str_replace("\t", ' ', $item);
			$item = trim($item);
			$item = preg_replace("! +!", ' ', $item);
			$result = preg_match("!(.*) (.*)!", $item, $matches);
			if ($result)
			{
				if (!isset($this->hosts[$matches[2]]))
				{
					$this->hosts[$matches[2]] = $matches[1];
				}
			}
		}
		$this->data['md5'] = $this->md5;
		$this->data = [
			'md5'	=> $this->md5,
			'group'	=> 'default',
			'hosts'	=> [
				'default'	=> $this->hosts,
			],
		];

		//备份
		copy(HOSTS, ROOT . '/hosts_back');
		return TRUE;
	}

	public function save()
	{
		//写入文件
		file_put_contents(ROOT.'/data.json', json_encode($this->data));
		return TRUE;
	}

	//新增一条host
	public function add($ip, $host, $group = 'default')
	{
		if (!isset($this->data['hosts'][$group][$host]))
		{
			$this->data['hosts'][$group][$host] = $ip;
		}

		return TRUE;
	}

	//删除一条hosts
	public function del($host, $group = 'default')
	{
		if (isset($this->data['hosts'][$group][$host]))
		{
			unset($this->data['hosts'][$group][$host]);
		}
		return TRUE;
	}
	
	//获取host所在分组
	public function get_host_group($host, $group = NULL)
	{
		$data = [];
		foreach ($this->data['hosts'] as $group_name => $hosts)
		{
			if (!is_null($group))
			{
				if ($group_name != $group)
				{
					continue;
				}
			}

			foreach ($hosts as $used_host=>$ip)
			{
				if (strpos($used_host, $host) !== FALSE)
				{
					$data[] = [
						'group'	=> $group_name,
						'host'	=> $used_host,
					];
				}
			}
		}
		return $data;
	}

	//获取所有分组
	public function group_list($group = NULL)
	{
		$data = [];
		foreach ($this->data['hosts'] as $group_name => $hosts)
		{
			if (!is_null($group))
			{
				if (strpos($group_name, $group) !== FALSE)
				{
					$data[$group_name] = count($hosts);
				}
			}
			else
			{
				$data[$group_name] = count($hosts);
			}
		}

		return $data;
	}

	//切换hosts
	public function change($group = 'default')
	{
		if (!isset($this->data['hosts'][$group]))
		{
			return '您要切换的分组不存在';
		}

		$hosts_file_array = [];
		foreach ($this->data['hosts'][$group] as $host=>$ip)
		{
			$hosts_file_array[] = $ip . "\t" . $host;
		}

		file_put_contents(ROOT.'/hosts', implode("\n", $hosts_file_array));
		$this->md5 = md5_file(ROOT.'/hosts');
		$this->group = $group;
		$this->data['md5'] = $this->md5;
		$this->data['group'] = $this->group;
		return TRUE;
	}
}