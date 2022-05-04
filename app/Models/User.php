<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property int $karma_score
 * @property string $username
 * @property int $image_id
 */

class User extends Model
{
    use HasFactory;

    protected $with = ['image'];

    protected $fillable = ['*'];

    protected $guarded = [];

    public $timestamps = false;

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
