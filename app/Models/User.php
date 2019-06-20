<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use  MustVerifyEmailTrait;
    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        //如果要通知的人是当前用户，就不必通知了
        if ($this->id == Auth::id()) {
            return;
        }
        //只有数据库类型通知才需要提醒，直接发送Email或者其他的都pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notifications_count');
        }
        $this->laravelNotify($instance);
    }

    public function markAsRead()
    {
        $this->notifications_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar'
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    //是否有权限策略
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
