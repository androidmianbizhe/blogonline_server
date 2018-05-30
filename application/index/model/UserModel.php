<?php



namespace app\index\model;

use app\index\model\BaseModel;
use think\Db;

class UserModel extends BaseModel {

  //详情
  public function getDetailUserInfo($uid){

    $data = Db::name('user')->field('uid,nickname,u_icon,u_age,rank_level,user_type,create_time,address,job,sex,visitorSum')->where('uid','=',$uid)->find();

    if($data){
      $user_type_icon = Db::name('member_des')->field('type_icon,type_name')->where('user_type','=',$data['user_type'])->find();
      $user_tag = Db::name('tag')->field('publish_time,content,face')->where('uid','=',$uid)->order('publish_time desc')->limit(1)->find();

      $data['user_type_icon'] = $user_type_icon['type_icon'];
      $data['user_type_name'] = $user_type_icon['type_name'];
      $data['publish_time'] = $user_tag['publish_time'];
      $data['content'] = $user_tag['content'];
      $data['face'] = $user_tag['face'];

    } else {
      $data = null;
    }

    return $data;
  }

  //基本信息
  public function getUserSimpleInfo($uid){

    $data = Db::name('user')->field('uid,nickname,u_icon,rank_level,user_type')->where('uid','=',$uid)->find();

    if($data){

      $user_type_icon = Db::name('member_des')->field('type_icon')->where('user_type','=',$data['user_type'])->find();
      $user_tag = Db::name('tag')->field('content')->where('uid','=',$uid)->order('publish_time desc')->limit(1)->find();

      $data['user_type_icon'] = $user_type_icon['type_icon'];
      $data['content'] = $user_tag['content'];

    } else {
      $data = null;
    }

    return $data;
  }

  //更改用户图像路径
  public function updateIconOfUser($uid,$path){
    Db::name('user')->where('uid',$uid)->update(['u_icon'=>$path]);
  }

  //更改用户昵称
  public function updateNicknameOfUser($uid,$nickname){
    return Db::name('user')->where('uid',$uid)->update(['nickname'=>$nickname]);
  }

  //更改性别
  public function updateSexOfUser($uid,$sex){
    return Db::name('user')->where('uid',$uid)->update(['sex'=>$sex]);
  }

  //更改生日
  public function updateBirthOfUser($uid,$birth){
    return Db::name('user')->where('uid',$uid)->update(['create_time'=>$birth]);
  }

  //添加签名
  public function addTagOfUser($data){
    return Db::name('tag')->insert($data);
  }

  //接受客户端上传来的数据
  public function uploadBlogOfUser($json=""){

    //解析数据
    $uploadArticle = json_decode($json,true);

    //$sum = 0;
    //$count = Db::name("article")->insertAll($uploadArticle);
    foreach ($uploadArticle as $key => $value) {

      if(is_array($value)){

        $count = Db::name('article')->where('article_link',$value['article_link'])->find();

        if($count == null){
          //$sum ++;
          Db::name("article")->insert($value);
        }

      }

     }
    //echo $sum;
    return 1;
  }

  public function uploadPreferOfUser($prefer_json){

    //解析
    $prefer = json_decode($prefer_json,true);
    //添加到数据酷

    $uid = $prefer["uid"];

    $viewcount_mobile = $prefer["prefer"];

    foreach ($viewcount_mobile as $key => $value) {
      
      //判断当前用户对应的viewCount是否存在
      $rows = Db::name('user_prefer')->where('uid', $uid)->where("blog_type_id",$value['type_id'])->where("from_id",$value['from_id'])->setInc('view_count',$value["viewCount"]);
      if($rows == 0){
         Db::name('user_prefer')->insert(
          [
            "uid"=>$uid,"blog_type_id"=>$value['type_id'],"view_count"=>$value["viewCount"],"from_id"=>$value['from_id']
          ]);
      }
    }

    return 1;
  }

  function getBlogOfUser($page=1,$uid,$from_id=1){

    //查询 uid 所对应的 大数据/运计算 和 移动开发 所对应的观察次数
    if($page == null){
      $page = 1;
    }

    //输出数据
    $data  = array();
    //uid对应的count
    $prefer = Db::name('user_prefer')->field('blog_type_id,view_count')->where('uid',$uid)
    ->where('from_id',$from_id)->select();

    //总的viewCount
    $allCount = Db::name('user_prefer')->where('uid',$uid)->where('from_id',$from_id)->sum('view_count');

    //每次加载20条数据
    $itemCount = 30;

    if($allCount == 0){

      $allCount = 2;

      //通过获取前两条
      $loginModel = new LoginModel();
      $baseTypes = $loginModel->getTypeById($from_id,2);

      $baseTypes = $baseTypes['article_type'];

      foreach ($baseTypes as $key => $value) {

        $typesItem = ['blog_type_id'=>$value['type_id'],'view_count'=>1,'from_id'=>$from_id];

        array_push($prefer,$typesItem);

      }

    }

    //比率
    $rate = 1.0 / $allCount*$itemCount;

    foreach ($prefer as $key => $value) {
      # code...
      $blogItem = Db::name("article")->alias('a')->join('article_type t','a.article_type_id = t.type_id')
      ->field('a.article_link as link,a.article_title as title,a.article_type_id as type_id,a.article_auther as username,t.article_type_name as type,a.article_from_id as from_id')
      ->where("a.article_type_id",$value['blog_type_id'])->order('id desc')
      ->where("a.article_from_id",$from_id)
      ->limit(intval(($page-1)*$rate*$value['view_count']+0.5),intval($rate*$value['view_count']+0.5))
      ->select();

      //加入到数组中
      $data = array_merge($data, $blogItem);
    }
    
    return $data;
  }

  function shuffle_assoc($list) {
    if (!is_array($list)) return $list;

    $keys = array_keys($list);
    shuffle($keys);
    $random = [];
    foreach ($keys as $key)
      array_push($random,$list[$key]);

    return $random;
  }

}

 ?>
