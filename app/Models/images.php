<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $table="images";
    protected $primaryKey="img_id";
    public $timestamps=false;
    protected $guarded=[];
}
