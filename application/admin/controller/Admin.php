<?php

namespace app\admin\controller;

use app\admin\controller\Base;
use think\Db;
use think\Loader;

class Admin extends Base {

	public function lst(){

		//查询数据
		$admins = Db::name('admin')->alias('a')->field('a.id,a.username')->select();
		$this->assign('admins',$admins);
		
		return $this->fetch('lst');
	}

	public function add(){

		if(request()->isPost()){

			$data = [
				'username' => input('username'),
				'password' => input('password'),
			];

			//验证
			$validate = Loader::validate('AdminValidate');
			if($validate->check($data)){
				
				$data['password'] = md5($data['password']);

				if(Db::name('admin')->insert($data)){
					return $this->success('添加管理员成功!','lst');
				} else {
					return $this->error('添加管理员失败!');
				}
			} else {
				return $this->error($validate->getError());
			}
		}

		return $this->fetch('add');
	}

	public function edit(){

		if(request()->isPost()){
			$data = [
				'id' => input('id'),
				'username' => input('username'),
				'password' => input('password'),
			];
			//如果密码为空
			if(empty($data['password'])){
				$password = Db::name('admin')->alias('a')->field('a.password')->where('id',$data['id'])->find();
				$data['password'] = $password['password'];
			}
			//验证
			$validate = Loader::validate('AdminValidate');
			if($validate->check($data)){

				$data['password'] = md5($data['password']);
				
				if(Db::name('admin')->update($data)){
					return $this->success('更新管理员成功!','lst');
				} else {
					return $this->error('更新管理员失败!');
				}
			} else {
				return $this->error($validate->getError());
			}
		}

		$id = input('id');
		$admin = Db::name('admin')->alias('a')->field('a.id,a.username')->where('id',$id)->find();
		$this->assign('admin',$admin);

		return $this->fetch('edit');
	}

	public function del(){
		$id = input('id');
		if(Db::name('admin')->delete($id)){
			return $this->success('删除管理员成功!','lst');
		} else {
			return $this->error('删除管理员失败!');
		}
	}


}





?>