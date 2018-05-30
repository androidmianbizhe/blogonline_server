<?php

namespace app\admin\controller;

use app\admin\controller\Base;
use think\Db;
use think\Loader;

class Article extends Base {
	//显示列表
	public function lst(){

		//查询数据 如果两个表有重复的字段
		//就指定想要的查询字段
		$articleres = Db::name('article')->alias('a')->join('cate c','a.cateid = c.id')->field('a._id,a.title,a.keywords,a.des,a.content,a.cateid,a.pic,a.click,a.time,c.id,c.catename')->paginate(3);
		//分配数据到模板
		$this->assign('articleres',$articleres);
		return $this->fetch('lst');
	}

	//添加界面
	public function add(){

		//如果是提交数据过来的
		if(request()->isPost()){
			//获取数据
			$data = [
				'title' => input('title'),
				'keywords' => input('keywords'),
				'des' => input('des'),
				'content' => input('content'),
				'cateid' => input('cateid'),
				'time'=> time(),
			];

			//接受上传的文件
			if($_FILES['pic']['tmp_name']){
				// 获取表单上传文件 例如上传了001.jpg
			    $file = request()->file('pic');
			    
			    // 移动到框架应用根目录/public/uploads/ 目录下
			    $info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');

			    if($info){
			        // 成功上传后 获取上传信息
			        $data['pic'] = '/uploads/'.date('Ymd').'/'.$info->getFilename(); 

			    }else{
			        // 上传失败获取错误信息
			        return $this->error($file->getError());
			    }
			} 
			//验证数据
			$validate = Loader::validate('ArticleValidate');

			if($validate->check($data)){
				//数据符合
				if(Db::name('article')->insert($data)){
					//添加成功
					return $this->success('添加数据成功','lst');
				} else {
					//添加失败
					return $this->error('添加数据失败!');
				}	
			} else {
				//验证失败
				return $this->error($validate->getError());
			}

			return;
		}
		//初始化界面之前 获取栏目类型
		$cateres = Db::name('cate')->select();
		//分配数据到模板
		$this->assign('cateres',$cateres);
		return $this->fetch('add');
	}

	//文章的修改
	public function edit(){
		//如果是提交数据过来的
		if(request()->isPost()){
			//获取数据
			$data = [
				'_id' => input('_id'),
				'title' => input('title'),
				'keywords' => input('keywords'),
				'des' => input('des'),
				'content' => input('content'),
				'cateid' => input('cateid'),
				'time'=> time(),
			];

			//接受上传的文件
			if($_FILES['pic']['tmp_name']){
				// 获取表单上传文件 例如上传了001.jpg
			    $file = request()->file('pic');
			    
			    // 移动到框架应用根目录/public/uploads/ 目录下
			    $info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');

			    if($info){
			        // 成功上传后 获取上传信息
			        $data['pic'] = '/uploads/'.date('Ymd').'/'.$info->getFilename(); 

			    }else{
			        // 上传失败获取错误信息
			        return $this->error($file->getError());
			    }
			} 
			//验证数据
			$validate = Loader::validate('ArticleValidate');

			if($validate->check($data)){
				//数据符合
				if(Db::name('article')->update($data)){
					//添加成功
					return $this->success('更新文章成功','lst');
				} else {
					//添加失败
					return $this->error('更新文章失败!');
				}	
			} else {
				//验证失败
				return $this->error($validate->getError());
			}

			return;
		}

		//获取post过来的id
		$id = input('_id');
		//查询相应的数据
		$article = Db::name('article')->where('_id',$id)->find();
		$cate = Db::name('cate')->select();
		//分配到文章中
		$this->assign('article',$article);
		$this->assign('cate',$cate);
		
		return $this->fetch('edit');
	}


	//删除文章
	public function del(){

		$_id = input('_id');

		if(Db::name('article')->delete($_id)){
			return $this->success('删除文章成功！','lst');
		} else {
			return $this->error('删除文章失败！');
		}

	}

}





?>