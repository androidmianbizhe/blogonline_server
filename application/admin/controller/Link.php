<?php

namespace app\admin\controller;

use app\admin\controller\Base;
use think\Db;
use think\Loader;

class Link extends Base {

	public function lst(){

		$links = Db::name('link')->select();
		//分配给模板
		$this->assign('links',$links);
		return $this->fetch('lst');
	}

	public function add(){

		if(request()->isPost()){
			//接受数据
			$data = [
				'title' => input('title'),
				'des' => input('des'),
				'url' => input('url'),
			];
			//验证数据
			$validate = Loader::validate('LinkValidate');
			if($validate->check($data)){
				//数据符合规则
				if(Db::name('link')->insert($data)){

					return $this->success('添加友情链接成功!','lst');
				} else {

					return $this->error('添加友情链接失败!');
				}
			} else {

				//不符合规则
				return $this->error($validate->getError());
			}
		}

		return $this->fetch('add');
	}

	//修改
	public function edit(){

		if(request()->isPost()){
			//接受数据
			$data = [
				'id' => input('id'),
				'title' => input('title'),
				'des' => input('des'),
				'url' => input('url'),
			];
			//验证数据
			$validate = Loader::validate('LinkValidate');
			if($validate->check($data)){
				//数据符合规则
				if(Db::name('link')->update($data)){

					return $this->success('更新友情链接成功!','lst');
				} else {

					return $this->error('更新友情链接失败!');
				}
			} else {

				//不符合规则
				return $this->error($validate->getError());
			}
		}

		//查询数据
		$id = input('id');
		$link = Db::name('link')->where('id',$id)->find();

		$this->assign('link',$link);
		
		return $this->fetch('edit');

	}

	//删除
	public function del(){

		$id = input('id');
		if(Db::name('link')->delete($id)){
			return $this->success('删除链接成功!','lst');
		} else {
			return $this->error('删除链接失败!');
		}
	}

}










?>