<?php

namespace app\admin\controller;

use app\admin\controller\Base;
use think\Loader;
use think\Db;

class Cate extends Base {

	//显示
	public function lst(){

		//通过数据库查询列表
		$catelst = Db::name('cate')->select();
		//分配到模板中
		$this->assign('catelst',$catelst);
		//加载模板
		return $this->fetch();
	}


	//添加栏目
	public function add(){

		if(request()->isPost()){
			
			//接受提交的数据
			$data = [
				'catename' => input('catename'),
				'keywords' => input('keywords'),
				'des' => input('des'),
				'type' => input('type')?input('type'):0,		
			];

			//验证 这里Cate指的是Cate验证器
			$validate = Loader::validate('CateValidate');
			//验证数据
			if($validate->check($data)){
				//成功
				//添加数据				
				if(Db::name('cate')->insert($data)){
					return $this->success('添加栏目成功','lst');//可指定跳转控制器 和 方法
				} else {
					return $this->error('添加栏目失败');
				}

			} else {
				//验证数据失败 提示用户
				return $this->error($validate->getError());
			}
			return;			
		}

		return $this->fetch('add');
	}

	//删除栏目
	public function del(){
		//获取post过来的id
		$id = input('id');
		//删除
		if(db('cate')->delete($id)) {
			//删除成功后跳转
			return $this->success('删除成功','lst');
		} else {
			//删除失败
			return $this->error('删除失败');
		}
	}

	//修改栏目
	public function edit(){

		if(request()->isPost()){
			//获取修改后post来的数据
			$data =[
				'id' => input('id'),
				'catename' => input('catename'),
				'keywords' => input('keywords'),
				'des' => input('des'),
				'type' => input('type'), 
			];
			//验证
			$validate = Loader::validate('CateValidate');
			if($validate->check($data)){
				
				//验证数据符合规则
				if(Db::name('cate')->update($data)){
					//修改成功
					return $this->success('修改栏目成功！','lst');
				} else {
					//修栏目失败
					return $this->error('修改栏目失败！');
				}
				
			} else {
				//验证失败
				$this->error($validate->getError());
			}
		}


		//获取post过来的id
		$id = input('id');
		//查找
		$cateres = db('cate')->where('id',$id)->find();
		//分配
		$this->assign('cateres',$cateres);
		//修改
		return $this->fetch('edit');

	}

}



?>