<?php namespace Lovata\BaseCode\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;
use OFFLINE\Mall\Models\Product;


class HideProduct extends Model
{
    protected $table = 'lovata_basecode_hide_products_in_branch';
    protected $primaryKey = 'id';

    public $fillable = ['product_id', 'branch_id'];

}
