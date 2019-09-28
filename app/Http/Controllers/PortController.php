<?php
namespace App\Http\Controllers;
use App\Tools\Tools;
use Illuminate\Http\Request;
use DB;
class PortController extends Controller
{
    public $tools;
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }
    /**
     * 接收微信发送的消息【用户互动】
     */
    public function envet()
    {
        $xml_string = file_get_contents('php://input');  //获取
        $wechat_log_psth = storage_path('logs/wechat/'.date('Y-m-d').'.log');
        file_put_contents($wechat_log_psth,"///////////开头///////////\n",FILE_APPEND);
        file_put_contents($wechat_log_psth,$xml_string,FILE_APPEND);
        file_put_contents($wechat_log_psth,"\n///////////结尾///////////\n\n",FILE_APPEND);
        $xml_obj = simplexml_load_string($xml_string,'SimpleXMLElement',LIBXML_NOCDATA);
        $xml_arr = (array)$xml_obj;
        \Log::Info(json_encode($xml_arr,JSON_UNESCAPED_UNICODE));
        if($xml_arr['Event'] == 'subscribe' && $xml_arr['MsgType'] == 'event') {
            $point=DB::table('wechat_user')->where(['open_id'=>$xml_arr['FromUserName']])->first();
            $data=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->tools->get_wechat_access_token().'&openid='.$xml_arr['FromUserName'].'&lang=zh_CN');
            $data=json_decode($data,1);
            if(empty($point)){
                DB::table('wechat_user')->insert([
                    'open_id'=>$xml_arr['FromUserName'],
                    'nickname'=>$data['nickname'],
                    'add_time'=>time(),
                    'sex'=>$data['sex']
                ]);
                $message='您好'.$data['nickname'].'当前时间为'.date('Y-m-d H:i:s',time());
                $xml_str = '<xml><ToUserName><![CDATA['.$xml_arr['FromUserName'].']]></ToUserName><FromUserName><![CDATA['.$xml_arr['ToUserName'].']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$message.']]></Content></xml>';
                $wechat_log_psth = storage_path('logs/wechat/'.date('Y-m-d').'.log');
                file_put_contents($wechat_log_psth,"///////////开头///////////\n",FILE_APPEND);
                file_put_contents($wechat_log_psth,$xml_str,FILE_APPEND);
                file_put_contents($wechat_log_psth,"\n///////////结尾///////////\n\n",FILE_APPEND);
                echo $xml_str;
            }else{
                $message='欢迎回来'.$data['nickname'].'当前时间为'.date('Y-m-d H:i:s',time());
                $xml_str = '<xml><ToUserName><![CDATA['.$xml_arr['FromUserName'].']]></ToUserName><FromUserName><![CDATA['.$xml_arr['ToUserName'].']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$message.']]></Content></xml>';
                $wechat_log_psth = storage_path('logs/wechat/'.date('Y-m-d').'.log');
                file_put_contents($wechat_log_psth,"///////////开头///////////\n",FILE_APPEND);
                file_put_contents($wechat_log_psth,$xml_str,FILE_APPEND);
                file_put_contents($wechat_log_psth,"\n///////////结尾///////////\n\n",FILE_APPEND);
                echo $xml_str;
            }
        }