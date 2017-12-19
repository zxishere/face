<?php

namespace App\Vbot;

use Illuminate\Support\Facades\Storage;
use App\Models\Friend;

class Observer
{
    public static function setQrCodeObserver($qrCodeUrl)
    {
        vbot('console')->log('二维码链接：'.$qrCodeUrl, '自定义消息');
    }

    public static function setLoginSuccessObserver()
    {
        vbot('console')->log('登录成功', '自定义消息');
    }

    public static function setReLoginSuccessObserver()
    {
        vbot('console')->log('免扫码登录成功', '自定义消息');
    }

    public static function setExitObserver()
    {
        vbot('console')->log('退出程序', '自定义消息');
    }

    public static function setFetchContactObserver(array $contacts)
    {
        $friends = vbot('friends');
        foreach ($contacts['friends'] as $user) {
            $friend = Friend::firstOrCreate(['nick_name' => $user['NickName']]);
            $friend->update([
                'user_name' => $user['UserName'],
                'head_img_url' => $user['HeadImgUrl'],
                'remark_name' => $user['RemarkName']
            ]);
            Storage::disk('public')->put('friends/' . $friend->id .'.jpg', $friends->getAvatar($user['UserName']));
        }
        vbot('console')->log('获取'.count($contacts['friends']).'位好友, 数据库已更新', '自定义消息');
    }

    public static function setBeforeMessageObserver()
    {
        vbot('console')->log('准备接收消息', '自定义消息');
    }

    public static function setNeedActivateObserver()
    {
        vbot('console')->log('准备挂了，但应该能抢救一会', '自定义消息');
    }
}
