<?php

namespace Lovata\BaseCode\Classes\Seeders;

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Seeder;
use Lovata\BaseCode\Models\Branches;
use poster\src\PosterApi;


class BranchesTableSeeder extends Seeder
{

    public function run()
    {

        // Очищаем
        Branches::truncate();

        Branches::create([
            'name'          => 'Черноморск',
            'phones'          =>  "+38 (093) 366 28 69, +38 (068) 303 45 51",
            'telegram_chat_id' => '',
            'telegram_bot_id' => '',
            'poster_spot_tablet_id'=> 1,
            'delivery' => '',
        ]);

        Branches::create([
            'name'          => 'Одесса',
            'phones'          => "+38 (093) 045 14 40, +38 (098) 970 37 67",
            'telegram_chat_id' => '',
            'telegram_bot_id' => '',
            'poster_spot_tablet_id'=> 2,
            'delivery' => '',
        ]);
    }
}
