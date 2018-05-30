<?php

namespace app\index\controller;

use app\index\model\TrendModel;
use think\Controller;

class Trend extends Controller {

  //发表说说
  public function publishTrend(){

    if(request()->isPost()){

      $returnCode = 0;
      $message_type = input('message_type');
      $uid = input('uid');

      if(strpos($message_type,"2")){//包含图片

        if(!empty($_FILES['pic']['tmp_name'])){
          //获取表单上传文件
          $files = request()->file('pic');
          foreach($files as $file){
              // 移动到框架应用根目录/public/uploads/ 目录下
              $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
              if($info){
                  // 成功上传后 获取上传信息
                  // 输出 jpg
                  echo $info->getExtension();
                  // 输出 42a79759f284b767dfcb2a0197904287.jpg
                  echo $info->getFilename();
              }else{
                  // 上传失败获取错误信息
                  echo $file->getError();
              }
          }
        }

      }else if (strpos($message_type, "3")) {//包含视频
        //视频
        $video = request()->file('video');

        $info = $video->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            echo $info->getExtension();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getFilename();
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
      }



    }


    return $this->fetch('push');

  }
  //获取好友动态列表
  public function getTrendList(){



  }
  //刷新好友列表
  public function getRefresh(){


  }
  //获取更多
  public function getMoreTrend(){




  }
  //评论
  public function comment(){


  }

  //点赞
  public function showGood(){


  }

  //分享到其他应用 sina qq baidu
  public function shareToOther(){

  }


}



 ?>
