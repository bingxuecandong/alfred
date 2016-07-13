<?php

define('ROOT', __DIR__);

date_default_timezone_set('Asia/Shanghai');

class Todo
{
	//文件内容
	public $data = [
		'id'	=> 0,
		'todo'	=> [],
		'done'	=> [],
	];

	public static $_instance = NULL;

	/**
	 * @return Todo
	 */
	public static function instance()
	{
		if (is_null(Todo::$_instance))
		{
			Todo::$_instance = new Todo();
		}
		return Todo::$_instance;
	}

	public function __construct()
	{
		//判断是否存在缓存文件
		if (file_exists(ROOT.'/data.json'))
		{
			//根据md5判断hosts文件是否一致
			$this->data = json_decode(file_get_contents(ROOT.'/data.json'), TRUE);
		}
	}

	public function __destruct()
	{
		$this->save();
	}

	public function save()
	{
		//写入文件
		file_put_contents(ROOT.'/data.json', json_encode($this->data));
		return TRUE;
	}

	public function add($title, $desc)
	{
		$id = ++$this->data['id'];
		$this->data['todo'][$id] = [
			'id'		=> $id,
			'title'		=> $title,
			'desc'		=> $desc,
			//'day'		=> date('m-d', time()),
			'created_on'=> time(),
		];
	}

	public function done($id)
	{
		$data = $this->data['todo'][$id];
		unset($this->data['todo'][$id]);
		$this->data['done'][$id] = array_merge($data, ['finished_on' => time()]);
		return $data;
	}

	public function todo_list()
	{
		return $this->data['todo'];
	}

	public function done_list()
	{
		return $this->data['done'];
	}
}