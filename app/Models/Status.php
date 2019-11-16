<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //没有定义可以写入的字段，laravel会尝试保护。
    //现定义content字段可以写入
    protected $fillable = ['content'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
