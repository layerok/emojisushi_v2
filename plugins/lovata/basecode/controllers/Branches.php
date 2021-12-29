<?
namespace Lovata\BaseCode\Controllers;

use Backend\Facades\BackendMenu;

class Branches extends \Backend\Classes\Controller
{

    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class
    ];


    public $listConfig = 'list_config.yaml';
    public $formConfig = 'config_form.yaml';


    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Lovata.Basecode', 'branches');
    }

    public function index()    // <=== Action method
    {
        $this->pageTitle = "Заведения";
        $this->vars['myVariable'] = 'value';

        $this->makeLists();
    }
}
