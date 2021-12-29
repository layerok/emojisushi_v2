<?php namespace Lovata\BaseCode\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;
use OFFLINE\Mall\Models\Product;


class HideCategory extends Model
{
    protected $table = 'lovata_basecode_hide_categories_in_branch';
    protected $primaryKey = 'id';

    public $fillable = ['category_id', 'branch_id'];

}
