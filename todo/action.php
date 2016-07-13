<?php

require_once "todo.php";

class Action
{
	public $message = '';
	/**
	 * @param $string
	 */
	public function __construct($string)
	{
		$param = explode(' ', $string);
		switch ($param[0])
		{
			case 'done':
				$this->done($param[1]);
				break;
			case 'add':
				$this->add($param[1], (isset($param[2]) ? $param[2] : ''));
				break;
		}
		echo $this->message;
	}

	//新增
	public function add($title, $desc = '')
	{
		Todo::instance()->add($title, $desc);
		$this->message = '已向记录TODO事项: '.$title;
	}

	//新增
	public function done($id)
	{
		$data = Todo::instance()->done($id);
		$this->message = $data['title'];
	}
}