<?php namespace Lovata\BaseCode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Backend\Facades\BackendMenu;
use Backend\Facades\Backend;
use OFFLINE\Mall\Components\QuickCheckout;
use Symfony\Component\HttpFoundation\Session\Session;
use OFFLINE\Mall\Controllers\Variants;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Product;
use OFFLINE\Mall\Models\Variant;
use System\Classes\PluginBase;
use OFFLINE\Mall\Controllers\Categories;
use OFFLINE\Mall\Controllers\Products;
//Console commands
use Lovata\BaseCode\Classes\Console\ResetAdminPassword;
use Validator;
use View;
use Cache;

/**
 * Class Plugin
 *
 * @package Lovata\BaseCode
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    public $require = ['Offline.Mall'];
    /**
     * Register plugin components
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\BaseCode\Components\SiteSettings' => 'SiteSettings',
            'Lovata\Basecode\Components\QuickCheckout' => 'quickCheckout'
        ];
    }


    /**
     * Register settings
     * @return array
     */
    public function registerSettings()
    {
        return [
            'config'    => [
                'label'       => 'lovata.basecode::lang.menu.settings',
                'description' => '',
                'icon'        => 'icon-cogs',
                'class'       => 'Lovata\BaseCode\Models\Settings',
                'permissions' => ['lovata-site-settings'],
                'order'       => 100,
            ],
            'branches' => [
                'label' => "Заведения",
                'description' => "Управлять заведениями",
                'icon'   => 'icon-globe',
                'url'    => Backend::url('lovata/basecode/branches/index'),
                'keywords'  => 'заведения заведение филиал'
            ]
        ];
    }

    /**
     * Plugin boot method
     */
    public function boot()
    {

        QuickCheckout::extend(function ($component){
            $component->addDynamicMethod('onSubmit', function() use ($component) {
                //do something

                return [];
            });
        });

        Event::listen('backend.form.extendFields', function ($widget) {

            if (!$widget->getController() instanceof Categories &&
                !$widget->getController() instanceof Products  &&
                !$widget->getController() instanceof Variants) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof Category &&
                !$widget->model instanceof Product  &&
                !$widget->model instanceof Variant) {
                return;
            }

            // Add an extra birthday field
            $widget->addFields([
                'poster_id' => [
                    'label'   => 'offline.mall::lang.category.poster_id',
                    'span' => 'left',
                    'type' => 'text'
                ]
            ]);

            if ($widget->model instanceof Category) {
                $widget->addFields([
                    'published' => [
                        'label' => 'offline.mall::lang.product.published',
                        'span' => 'left',
                        'type' => 'switch'
                    ]
                ]);
            }
        });

        // Extend all backend list usage
        Event::listen('backend.list.extendColumns', function ($widget) {

            if (!$widget->getController() instanceof Categories &&
                !$widget->getController() instanceof Products  &&
                !$widget->getController() instanceof Variants) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof Category &&
                !$widget->model instanceof Product  &&
                !$widget->model instanceof Variant) {
                return;
            }

            $widget->addColumns([
                'poster_id' => [
                    'label' => 'offline.mall::lang.category.poster_id'
                ]
            ]);

            if ($widget->model instanceof Category &&
                $widget->getController() instanceof Categories) {
                $widget->addColumns([
                    'published' => [
                        'label' => 'offline.mall::lang.product.published',
                        'type' => 'partial',
                        'path' => '$/offline/mall/models/product/_published.htm',
                        'sortable' => true
                    ]
                ]);
            }

        });

        Category::extend(function($model){
            $model->fillable[] = 'poster_id';
            $model->fillable[] = 'published';

            $model->casts['published'] = 'boolean';
            $model->rules['published'] = 'boolean';
        });

        Product::extend(function($model){
            $model->fillable[] = 'poster_id';
        });


        Validator::extend('phoneUa', function($attribute, $value, $parameters) {
            $regex = "/^(((\+?)(38))\s?)?(([0-9]{3})|(\([0-9]{3}\)))(\-|\s)?(([0-9]{3})(\-|\s)?
        ([0-9]{2})(\-|\s)?([0-9]{2})|([0-9]{2})(\-|\s)?([0-9]{2})(\-|\s)?
        ([0-9]{3})|([0-9]{2})(\-|\s)?([0-9]{3})(\-|\s)?([0-9]{2}))$/";

            return preg_match($regex, $value);
        });



        $this->addEventListener();
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                // Using an inline closure
                'preg_replace' => function ($subject, $pattern, $replacement) {
                    return preg_replace($pattern, $replacement, $subject);
                },
            ],
        ];
    }


    public function register()
    {
        $this->registerConsoleCommand('basecode:reset_admin_password', ResetAdminPassword::class);
    }

    /**
     * @return array
     */
    public function registerMailTemplates()
    {
        return [];
    }

    /**
     * Add listener
     */
    protected function addEventListener()
    {
        ///
    }


}
