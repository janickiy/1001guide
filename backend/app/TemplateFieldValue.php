<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateFieldValue extends Model
{
    protected $fillable = [
    	'field', 'lang', 'page_type', 'value', 'tag_slug_id'
    ];
}
