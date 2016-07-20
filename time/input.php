<?php

date_default_timezone_set('Asia/Shanghai');

class Input
{
	public $data = [];
	public $uid = 0;
	public $string = '';

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
			$this->help();
		}
		else
		{
			$this->exchange();
		}
	}

	public function exchange()
	{
		$time = strtotime($this->string);
		if ($time !== FALSE)
		{
			//可以被转为时间戳
			//时间戳
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'yes',
				'autocomplete'	=> '',
				'arg'			=> $time,
				'title'			=> $time,
				'subtitle'		=> $this->string.'对应时间戳',
				'icon'			=> 'images/time.png',
			];
		}
		else
		{
			//不可以
			if (is_numeric($this->string))
			{
				//日期
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'yes',
					'autocomplete'	=> '',
					'arg'			=> date('Y-m-d', $this->string),
					'title'			=> date('Y-m-d', $this->string),
					'subtitle'		=> $this->string.'对应日期',
					'icon'			=> 'images/date.png',
				];

				//时间
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'yes',
					'autocomplete'	=> '',
					'arg'			=> date('H:i:s', $this->string),
					'title'			=> date('H:i:s', $this->string),
					'subtitle'		=> $this->string.'对应时间',
					'icon'			=> 'images/icon.png',
				];

				//完整时间
				$this->data[] = [
					'uid'			=> ++$this->uid,
					'valid'			=> 'yes',
					'autocomplete'	=> '',
					'arg'			=> date('Y-m-d H:i:s', $this->string),
					'title'			=> date('Y-m-d H:i:s', $this->string),
					'subtitle'		=> $this->string.'对应日期时间',
					'icon'			=> 'images/datetime.png',
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
				'title'			=> '无法理解您输入的含义',
				'subtitle'		=> '',
				'icon'			=> 'images/icon.png',
			];
			$this->help();
		}
		else
		{
			$this->to_xml();
		}
	}

	public function help()
	{
		//时间戳
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'yes',
			'autocomplete'	=> '',
			'arg'			=> time(),
			'title'			=> time(),
			'subtitle'		=> '当前时间戳',
			'icon'			=> 'images/time.png',
		];

		//日期
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'yes',
			'autocomplete'	=> '',
			'arg'			=> date('Y-m-d', time()),
			'title'			=> date('Y-m-d', time()),
			'subtitle'		=> '当前日期',
			'icon'			=> 'images/date.png',
		];

		//时间
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'yes',
			'autocomplete'	=> '',
			'arg'			=> date('H:i:s', time()),
			'title'			=> date('H:i:s', time()),
			'subtitle'		=> '当前时间',
			'icon'			=> 'images/icon.png',
		];

		//完整时间
		$this->data[] = [
			'uid'			=> ++$this->uid,
			'valid'			=> 'yes',
			'autocomplete'	=> '',
			'arg'			=> date('Y-m-d H:i:s', time()),
			'title'			=> date('Y-m-d H:i:s', time()),
			'subtitle'		=> '当前日期时间',
			'icon'			=> 'images/datetime.png',
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

		echo '<items>'.$str.'</items>';
	}
}