<?php namespace Lovata\BaseCode\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;
use OFFLINE\Mall\Models\Product;


class Branches extends Model
{
    use Validation;
    protected $table = 'lovata_basecode_branches';
    protected $primaryKey = 'id';

    public $timestamps = true;

    public $rules = [
        'name' => 'required',
        'poster_spot_tablet_id' => [
            'required',
            'integer'
        ]
    ];

    public $belongsToMany = [
        'hideProductsInBranch'          => [
            Product::class,
            'table'    => 'lovata_basecode_hide_products_in_branch',
            'key'      => 'branch_id',
            'otherKey' => 'product_id',
        ]
    ];

    public function getChatId()
    {
        return $this->telegram_chat_id;
    }

    public function getTabletId()
    {
        return $this->poster_spot_tablet_id;
    }

}
