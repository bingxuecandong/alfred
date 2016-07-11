<?php

require_once "hosts_switch.php";

class Action
{
	public $message = '';
	/**
	 * Hosts constructor.
	 * @param $string
	 */
	public function __construct($string)
	{
		$param = explode(' ', $string);
		switch ($param[0])
		{
			case 'password':
				$this->password($param[1]);
				break;
			case 'change':
				$this->change($param[1]);
				break;
			case 'add':
				$this->add($param[1], $param[2], $param[3]);
				break;
			case 'del':
				$this->del($param[1], $param[2]);
				break;
		}
		echo $this->message;
	}

	//设置密码
	public function password($password)
	{
		exec('security add-generic-password -a "hosts_switch" -s "tk.bxcd.hosts_switch" -w "'.$password.'" -T "/Applications/Alfred 2.app" -U');
		$this->message = 'ROOT密码已存储';
	}

	//切换分组
	public function change($group)
	{
		Hosts_Switch::instance()->change($group);
		exec('echo `security find-generic-password -w -s "tk.bxcd.hosts_switch"` | sudo -S cp -f "'.ROOT.'/hosts" /etc/hosts');
		$this->message = '已切换分组为'.$group;
	}

	//新增
	public function add($ip, $host, $group)
	{
		Hosts_Switch::instance()->add($ip, $host, $group);
		if ($group == Hosts_Switch::instance()->group)
		{
			$this->change($group);
		}
		$this->message =  '已向'.$group.'分组中添加映射: '.$ip.' '.$host;
	}

	//删除
	public function del($host, $group)
	{
		Hosts_Switch::instance()->del($host, $group);
		if ($group == Hosts_Switch::instance()->group)
		{
			$this->change($group);
		}
		$this->message =  '已从'.$group.'分组中删除'.$host.'的映射';
	}
}