<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserGroup
 *
 * @package App
*/
class UserGroup extends Model
{
    protected $table = 'user_group';
    protected $fillable = ['nome','menu_enable_json',];
    protected $hidden = [];
    public $timestamps = false;
    
    
}
