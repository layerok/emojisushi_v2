<?php namespace Lovata\BaseCode\Updates;

use Cache;
use October\Rain\Database\Model;
use October\Rain\Database\Updates\Seeder;
use OFFLINE\Mall\Classes\Registration\BootServiceContainer;
use OFFLINE\Mall\Classes\Registration\BootTwig;
use Lovata\BaseCode\Classes\Seeders\CategoryTableSeeder;
use Lovata\BaseCode\Classes\Seeders\ProductTableSeeder;
use Lovata\BaseCode\Classes\Seeders\ImageSetTableSeeder;

class DatabaseSeeder extends Seeder
{
    public $app;

    use BootTwig;
    use BootServiceContainer;

    public function run()
    {
        $this->app = app();

        $this->registerTwigEnvironment();
        $this->registerServices();

        Model::unguard();
        Cache::clear();


        $this->call(CategoryTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(ImageSetTableSeeder::class);
        $this->call(BranchesSeeder::class);

    }
}
