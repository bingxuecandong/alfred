# hosts switch

	用于简单管理本机hosts
	全局快捷键 ctrl+command+h

## 分组解释

	您可以将常用的几个host映射关系保存为一个分组,方便进行切换
	也可以在每次需要的时候单独添加/删除

	第一次使用时,从您本机的/etc/hosts文件中读取的内容,自动保存为default分组

## 1. 设置本机root密码

	由于修改本机hosts需要管理员权限,因此需要先存储root密码,该密码存储与您本机的keychain中
	命令:
	hosts -p 123456
	参数说明:
	-p		设置密码命令
	pwd		密码

## 2. 增加一条映射关系

	命令:
	hosts -a 127.0.0.1 www.qq.com work
	参数说明:
	-a		增加命令
	ip		合法的ip地址
	host	需要映射的地址
	group	分组,可选参数

## 3. 删除一条映射关系

	命令:
	hosts -d www.qq.com work
	参数说明:
	-d		删除命令
	host	需要删除的地址
	group	从哪个分组中删除,可选参数

## 4. 切换分组

	命令:
	hosts work
	参数说明
	group	需要切换到的分组

## 5. 显示某分组下所有host映射

	命令:
	hosts -l group host
	参数说明
	-l		显示hosts列表命令
	group	要显示某个分组下的hosts
	host	带此关键字的host列表,可选参数