<?php

require_once "hosts_switch.php";

class Input
{
	private $string = '';
	private $uid = 0;
	private $data = [];

	/**
	 * Hosts constructor.
	 * @param $string
	 */
	public function __construct($string)
	{
		$string = trim($string);
		$this->string = $string;
		//参数相关
		if ($string == '')
		{
			//输出帮助信息
			$this->all();
		}
		elseif ($string == '-')
		{
			$this->help();
		}
		else
		{
			$method = substr($string, 0, 2);
			switch ($method)
			{
				case '-a':
					//新增
					$this->add();
					break;
				case '-d':
					//删除
					$this->del();
					break;
				case '-p':
					//设置密码
					$this->password();
					break;
				case '-l':
					//查看group下hosts列表
					$this->group_list();
					break;
				default:
					//切换分组
					$this->change();
					break;
			}
		}
	}

	/**
	 * 显示所有分组名
	 */
	public function all()
	{
		//当前为什么分组
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '',
			'arg'			=> Hosts_Switch::instance()->group,
			'title'			=> '当前Hosts为'.Hosts_Switch::instance()->group.'分组',
			'subtitle'		=> '',
			'icon'			=> 'images/icon.png',
		];

		//获取当前有多少分组
		$group_list = Hosts_Switch::instance()->group_list();
		foreach ($group_list as $group_name=>$group_count)
		{
			if ($group_name == Hosts_Switch::instance()->group)
			{
				continue;
			}
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'yes',
				'autocomplete'	=> $group_name,
				'arg'			=> 'change '.$group_name,
				'title'			=> 'hosts '.$group_name,
				'subtitle'		=> '切换Hosts至'.$group_name.'('.$group_count.')分组',
				'icon'			=> 'images/icon.png',
			];
		}

		//输出帮助信息
		$this->help();
	}

	public function help()
	{
		//add
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '-a ',
			'arg'			=> '',
			'title'			=> 'hosts -a ip host group',
			'subtitle'		=> '向work分组中插入hosts: hosts -a 127.0.0.1 www.baidu.com work',
			'icon'			=> 'images/add.png',
		];

		//del
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '-d ',
			'arg'			=> '',
			'title'			=> 'hosts -d host group',
			'subtitle'		=> '从work分组中删除hosts: hosts -d www.baidu.com work',
			'icon'			=> 'images/del.png',
		];

		//list
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '-l ',
			'arg'			=> '',
			'title'			=> 'hosts -l group',
			'subtitle'		=> '显示某分组下所有host映射',
			'icon'			=> 'images/list.png',
		];

		//password
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '-p ',
			'arg'			=> '',
			'title'			=> 'hosts -p root密码',
			'subtitle'		=> '由于修改hosts操作需要root权限,请提供root密码,密码将直接保存在您本机的keychain中',
			'icon'			=> 'images/key.png',
		];

		$this->to_xml();
	}

	public function group_list()
	{
		$param = explode(' ', $this->string);
		$count = count($param);
		if ($count == 1)
		{
			//只有-l
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'no',
				'autocomplete'	=> '-l ',
				'arg'			=> '',
				'title'			=> 'hosts -l group',
				'subtitle'		=> '显示group分组下所有host映射',
				'icon'			=> 'images/list.png',
			];
		}
		else
		{
			//判断现有哪个分组里存在当前host
			if ($count == 2 || $count == 3)
			{
				//获取当前有多少分组
				$host_list = Hosts_Switch::instance()->group_host_list($param[1]);
				foreach ($host_list as $data)
				{
					if (($count == 3 && strpos($data['host'], $param[2]) !== FALSE) || $count == 2)
					{
						$this->data[] = [
							'uid'			=> ++$this->uid,
							'valid'			=> 'no',
							'autocomplete'	=> '',
							'arg'			=> '',
							'title'			=> $data['ip'].'    '.$data['host'],
							'subtitle'		=> $data['group'],
							'icon'			=> 'images/icon.png',
						];
					}
				}

				if (empty($this->data))
				{
					$this->data[] = [
						'uid'			=> ++$this->uid,
						'valid'			=> 'no',
						'autocomplete'	=> '',
						'arg'			=> '',
						'title'			=> $param[1].'分组不存在',
						'subtitle'		=> '',
						'icon'			=> 'images/sorry.png',
					];
				}
			}
			else
			{
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'no',
					'autocomplete'	=> '',
					'arg'			=> '',
					'title'			=> 'hosts -l group',
					'subtitle'		=> '无法识别您输入的含义',
					'icon'			=> 'images/sorry.png',
				];
			}
		}

		$this->to_xml();
	}

	/**
	 * 处理新增
	 */
	public function add()
	{
		//判断数据合法性
		$param = explode(' ', $this->string);
		$count = count($param);
		if ($count == 1)
		{
			//只有-a
			$data = [
				'uid'			=> 1,
				'valid'			=> 'no',
				'autocomplete'	=> '-a ',
				'arg'			=> '',
				'title'			=> 'hosts -a ip host group',
				'subtitle'		=> '向work分组中插入hosts: hosts -a 127.0.0.1 www.baidu.com work',
				'icon'			=> 'images/add.png',
			];
		}
		else
		{
			$ip = $param[1];
			$ip_check = ip2long($ip);
			if ($ip_check === FALSE)
			{
				//ip不合法
				$data = [
					'uid'			=> 1,
					'valid'			=> 'no',
					'autocomplete'	=> $this->string,
					'arg'			=> '',
					'title'			=> 'hosts -a ip host group',
					'subtitle'		=> '请输入完整 有效的IP地址',
					'icon'			=> 'images/add.png',
				];
			}
			else
			{
				//ip合法
				if ($count == 2)
				{
					$data = [
						'uid'			=> 1,
						'valid'			=> 'no',
						'autocomplete'	=> $this->string.' ',
						'arg'			=> '',
						'title'			=> 'hosts '.$this->string.' host group',
						'subtitle'		=> '请输入要指向的host',
						'icon'			=> 'images/add.png',
					];
				}
				elseif ($count == 3)
				{
					$data = [
						'uid'			=> 1,
						'valid'			=> 'yes',
						'autocomplete'	=> $this->string,
						'arg'			=> 'add '.$param[1].' '.$param[2].' '.Hosts_Switch::instance()->group,
						'title'			=> 'hosts '.$this->string. ' group',
						'subtitle'		=> '向'.Hosts_Switch::instance()->group.'中插入映射'.$this->string.', 如需其他分组,请继续输入分组名',
						'icon'			=> 'images/add.png',
					];
				}
				elseif ($count == 4)
				{
					$data = [
						'uid'			=> 1,
						'valid'			=> 'yes',
						'autocomplete'	=> $this->string,
						'arg'			=> 'add '.$param[1].' '.$param[2].' '.$param[3],
						'title'			=> 'hosts '.$this->string,
						'subtitle'		=> '向'.$param[3].'分组中插入映射关系'.$param[1].' '.$param[2],
						'icon'			=> 'images/add.png',
					];
				}
				else
				{
					$data = [
						'uid'			=> 1,
						'valid'			=> 'no',
						'autocomplete'	=> $this->string,
						'arg'			=> '',
						'title'			=> 'hosts -a ip host group',
						'subtitle'		=> '无法识别您输入的含义',
						'icon'			=> 'images/sorry.png',
					];
				}
			}
		}

		$this->data[] = $data;
		$this->to_xml();
	}

	/**
	 * 处理删除
	 */
	public function del()
	{
		//判断数据合法性
		$param = explode(' ', $this->string);
		$count = count($param);
		if ($count == 1)
		{
			//只有-d
			$this->data[] = [
				'uid'			=> 1,
				'valid'			=> 'no',
				'autocomplete'	=> '-d ',
				'arg'			=> '',
				'title'			=> 'hosts -d host group',
				'subtitle'		=> '从work分组中删除hosts: hosts -d www.baidu.com work',
				'icon'			=> 'images/del.png',
			];
		}
		else
		{
			//判断现有哪个分组里存在当前host
			if ($count == 2)
			{
				$group_names = Hosts_Switch::instance()->get_host_group($param[1]);
			}
			elseif ($count == 3)
			{
				$group_names = Hosts_Switch::instance()->get_host_group($param[1], NULL);
			}

			if (!empty($group_names))
			{
				foreach ($group_names as $value)
				{
					$this->data[] = [
						'uid'			=> 1,
						'valid'			=> 'yes',
						'autocomplete'	=> '-d '.$value['host']. ' '.$value['group'],
						'arg'			=> 'del '.$value['host']. ' '.$value['group'],
						'title'			=> 'hosts -d '.$value['host']. ' '.$value['group'],
						'subtitle'		=> '从'.$value['group'].'中删除'.$value['host'],
						'icon'			=> 'images/del.png',
					];
				}
			}
			else
			{
				$this->data[] = [
					'uid'			=> 1,
					'valid'			=> 'no',
					'autocomplete'	=> $this->string,
					'arg'			=> '',
					'title'			=> 'hosts -d host group',
					'subtitle'		=> '未找到您要删除的host',
					'icon'			=> 'images/sorry.png',
				];
			}
		}

		$this->to_xml();
	}

	/**
	 * 设置密码
	 */
	public function password()
	{
		//判断数据合法性
		$param = explode(' ', $this->string);
		$count = count($param);
		if ($count == 1)
		{
			//只有-p
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'no',
				'autocomplete'	=> '-p ',
				'arg'			=> '',
				'title'			=> 'hosts -p root密码',
				'subtitle'		=> '由于修改hosts操作需要root权限,请提供root密码,密码将直接保存在您本机的keychain中',
				'icon'			=> 'images/key.png',
			];
		}
		else
		{
			//判断现有哪个分组里存在当前host
			if ($count == 2)
			{
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'yes',
					'autocomplete'	=> $this->string,
					'arg'			=> 'password '.$param[1],
					'title'			=> 'hosts -p '.$param[1],
					'subtitle'		=> '设置root密码为'.$param[1],
					'icon'			=> 'images/key.png',
				];
			}
			else
			{
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'no',
					'autocomplete'	=> '-p ',
					'arg'			=> '',
					'title'			=> 'hosts -p root密码',
					'subtitle'		=> '无法识别您输入的含义',
					'icon'			=> 'images/key.png',
				];
			}
		}

		$this->to_xml();
	}

	//切换分组
	public function change()
	{
		$group_list = Hosts_Switch::instance()->group_list($this->string);
		foreach ($group_list as $group_name=>$group_count)
		{
			if ($group_name == Hosts_Switch::instance()->group)
			{
				continue;
			}
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'yes',
				'autocomplete'	=> $group_name,
				'arg'			=> 'change '.$group_name,
				'title'			=> 'hosts '.$group_name,
				'subtitle'		=> '切换Hosts至'.$group_name.'('.$group_count.')分组',
				'icon'			=> 'images/icon.png',
			];
		}
		if (empty($this->data))
		{
			$this->data[] = [
				'uid'			=> 1,
				'valid'			=> 'no',
				'autocomplete'	=> $this->string,
				'arg'			=> '',
				'title'			=> 'hosts '.$this->string,
				'subtitle'		=> $this->string != Hosts_Switch::instance()->group ? '您输入的分组不存在' : '您当前的Hosts是'.Hosts_Switch::instance()->group.'分组,无需切换',
				'icon'			=> 'images/icon.png',
			];
		}
		$this->to_xml();
	}

	public function to_xml()
	{
		$str = '';
		foreach ($this->data as $item)
		{
			$str .= '<item autocomplete="'.$item['autocomplete'].'" valid="'.$item['valid'].'" uid="'.$item['uid'].'" arg="'.$item['arg'].'"><title>'.$item['title'].'</title><subtitle>'.$item['subtitle'].'</subtitle><icon>'.$item['icon'].'</icon></item>';
		}

		echo '<items>'.$str.'</items>';
	}
}