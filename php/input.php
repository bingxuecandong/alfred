<?php

require_once "php.php";

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
		$this->search();
	}

	/**
	 * 显示所有函数名
	 */
	public function search()
	{
		$list = PHP::instance()->search($this->string);
		foreach ($list as $function)
		{
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'yes',
				'autocomplete'	=> '',
				'arg'			=> $function['id'],
				'title'			=> $function['name'],
				'subtitle'		=> $function['description'],
				'icon'			=> '',
			];
		}

		if (empty($this->data))
		{
			$this->data[] = [
				'uid'			=> ++$this->uid,
				'valid'			=> 'no',
				'autocomplete'	=> '',
				'arg'			=> '',
				'title'			=> $this->string.' Not Found',
				'subtitle'		=> $this->string.' maybe is not a function',
				'icon'			=> '',
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