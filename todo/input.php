<?php

require_once "todo.php";

class Input
{
	private $data = [];
	private $string = '';
	private $uid = 0;

	/**
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
		else
		{
			$this->add();
		}
	}

	/**
	 * 显示所有分组名
	 */
	public function all()
	{
		//获取当前有多少分组
		$todo_list = Todo::instance()->todo_list();
		foreach ($todo_list as $id=>$data)
		{
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'yes',
				'autocomplete'	=> '',
				'arg'			=> 'done '.$id,
				'title'			=> $data['title'],
				'subtitle'		=> date('m-d', $data['created_on']).' '.$data['desc'],
				'icon'			=> 'images/icon.png',
			];
		}

		$done_list = Todo::instance()->done_list();
		foreach ($done_list as $id=>$data)
		{
			if (date('Y-m-d', time()) == date('Y-m-d', $data['finished_on']))
			{
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'no',
					'autocomplete'	=> '',
					'arg'			=> 'done '.$id,
					'title'			=> $data['title'],
					'subtitle'		=> date('m-d', $data['created_on']).' '.$data['desc'],
					'icon'			=> 'images/done2.png',
				];
			}
		}

		//输出帮助信息
		$this->help();
	}

	public function help()
	{
		//done
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '',
			'arg'			=> '',
			'title'			=> 'done',
			'subtitle'		=> '选择已有TODO事项标记完成',
			'icon'			=> 'images/help.png',
		];

		//add
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'no',
			'autocomplete'	=> '',
			'arg'			=> '',
			'title'			=> 'todo something',
			'subtitle'		=> '继续输入增加TODO事项',
			'icon'			=> 'images/help.png',
		];
		$this->to_xml();
	}

	/**
	 * 处理新增
	 */
	public function add()
	{
		//判断数据合法性
		$param = explode(' ', $this->string, 2);
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'yes',
			'autocomplete'	=> '',
			'arg'			=> 'add '.$this->string,
			'title'			=> $param[0],
			'subtitle'		=> isset($param[1]) ? $param[1] : '',
			'icon'			=> 'images/add.png',
		];

		$this->to_xml();
	}


	public function to_xml()
	{
		$str = '';
		foreach ($this->data as $item)
		{
			$str .= '<item autocomplete="'.$item['autocomplete'].'" valid="'.$item['valid'].'" uid="'.$item['uid'].'" arg="'.$item['arg'].'"><title>'.$item['title'].'</title><subtitle>'.$item['subtitle'].'</subtitle><icon>'.$item['icon'].'</icon></item>';
		}

		file_put_contents(ROOT.'/test', $str);
		echo '<items>'.$str.'</items>';
	}
}