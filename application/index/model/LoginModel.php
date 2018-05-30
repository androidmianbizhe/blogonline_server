<?php

namespace app\index\model;

use think\Model;
use think\Db;
use think\Session;


use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

class LoginModel extends Model {

  public function login($username,$passwd){

    //判断多少位
    if(strlen($username) != 11){
      
      return 3;
    }

      //电话号码
    $user = Db::name('user')->field('uid,nickname,passwd,phone_num')->where('phone_num','=',$username)->find();

    if($user){
      if($user['passwd'] == md5($passwd)){

        //保存Session
        Session::set('uid',$user['uid']);
        Session::set('nickname',$user['nickname']);

        return 1;//登陆成功
      } else {
        return 2;//用户或密码错误
      }
    } else {
      //不存在
      return 3;
    }
  }

  public function setPwd($phone,$code){

    if($phone == "" || $code == ""){
          return;
    }

    $insert_data = [

      'nickname'=>$phone,
      'passwd'=>$code,
      'create_time'=>time(),
      'phone_num'=>$phone
    ]; 

    $uid = Db::name('user')->insertGetId($insert_data);

    return $uid;

  }

  public function getUidSession(){
    return Session("uid");
  }

  public function register($nickname="",$openId="",$loginWay=-1,$phone_num=""){

    //1.判断是哪种登陆方式
    $data = Db::name('third_login_info')->where('login_way','=',$loginWay)->where('login_key','=',$openId)->find();

    //2.判断该登录方式是否含有openid
    if($data){
      //3.若含有openid则返回登录成功后的信息
      return $data['uid'];
    }else {
      //4.若不含有openid，则说明是新用户
      //并且为新用户注册帐号
      $insert_data = [
        'nickname'=>$nickname,
        'passwd'=>'',
        'create_time'=>time(),
        'phone_num'=>$phone_num
      ];

      $uid = Db::name('user')->insertGetId($insert_data);

      $data = [
        'uid'=>$uid,
        'login_key'=>$openId,
        'login_way'=>$loginWay
      ];

      Db::name('third_login_info')->insert($data);

      return $uid;
    }

  }

  /**
   * 发送短信
   * @return stdClass
   */
  public function sendSms($phone) {

    require_once APP_PATH.'/Api/api_sdk/vendor/autoload.php';
    Config::load();             //加载区域结点配置

    //产品名称:云通信流量服务API产品,开发者无需替换
    $product = "Dysmsapi";

    //产品域名,开发者无需替换
    $domain = "dysmsapi.aliyuncs.com";

    // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
    $accessKeyId = "LTAIpSbGMq1R8gCw"; // AccessKeyId

    $accessKeySecret = "N32DN9Z9W09xj4701Kw15TVQWuLq0g"; // AccessKeySecret

    // 暂时不支持多Region
    $region = "cn-hangzhou";

    // 服务结点
    $endPointName = "cn-hangzhou";

    //初始化acsClient,暂不支持region化
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

    // 增加服务结点
    DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

    // 初始化AcsClient用于发起请求
    $acsClient = new DefaultAcsClient($profile);

    // 初始化SendSmsRequest实例用于设置发送短信的参数
    $request = new SendSmsRequest();

    // 必填，设置短信接收号码
    $request->setPhoneNumbers($phone);

    // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $request->setSignName("多源博客");

    // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $request->setTemplateCode("SMS_116591726");

    $code = rand (100000,999999)."";
    // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
    $request->setTemplateParam(json_encode(Array(  // 短信模板中字段的值
        "code"=>$code,
        "product"=>"dsd"
    )));

    // 可选，设置流水号
    $request->setOutId("abcdefgh");

    // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
    $request->setSmsUpExtendCode("1234567");

    // 发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);

    //把 手机号 和 验证码保存到Session 中 过期时间30分钟
    Session::set('phone_num',$phone);
    Session::set('check_code',$code);
    Session::set('send_time',intval(time()));
    
    return $acsResponse;
  }

  public function getFrom(){

    $froms = Db::name('article_from')->field('fid,f_name,f_url,f_page_max_item')->select();
    return $froms;

  }

  public function getTypeById($from_id=1,$size=2){

    //查询max——page——item
    $max_page_items = Db::name('article_from')->field('f_page_max_item,article_body')->where('fid',$from_id)->find();

    if($size == -1){

        $types =  Db::name('article_type')->where('from_id',$from_id)->select();
    } else{
        $types =  Db::name('article_type')->where('from_id',$from_id)->limit($size)->select();
    }

    $res = ["article_type"=>$types];
    $res = array_merge($res,$max_page_items);

    return $res;
  
  }

  public function getRegsById($from_id=1){

    return Db::name("article_regx")->where("from_id",$from_id)->find();

  }

}








 ?>
