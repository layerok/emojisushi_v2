<?php
namespace Lovata\BaseCode\Classes\Helper;
use Carbon\Carbon;
use OFFLINE\Mall\Models\Cart;
use DB;
use Symfony\Component\HttpFoundation\Session\Session;

use Lovata\BaseCode\Models\Branches;

class Utils {
    public static function checkWorkingHours() {

        $start = explode(':', '10:00');
        $finish = explode(':', '22:30');

        $dt = Carbon::now()
            ->timezone('Europe/Kiev');

        $hour = $dt->hour;
        $minute = $dt->minute;


        $closed =
            ($hour < $start[0]  || ($start[0] == $hour && $minute < $start[1])) ||
            ($hour > $finish[0] || ($finish[0] == $hour && $minute > $finish[1] ));

        return $closed;
    }

    public static function removeHiddenProducts($spot_id) {
        $products_to_hide = DB::table('lovata_basecode_hide_products_in_branch')
            ->where('branch_id', '=', $spot_id)->pluck('product_id')->all();

        $categories_to_hide = DB::table('lovata_basecode_hide_categories_in_branch')
            ->where('branch_id', '=', $spot_id)->pluck('category_id')->all();

        $cart = Cart::bySession();
        foreach($cart->products as $pr) {
            if (in_array($pr->product_id, $products_to_hide)) {
                $cart->removeProduct($pr);
            }

            $hidden_category = $pr->product->categories()->whereIn('offline_mall_categories.id', $categories_to_hide)->exists();
            if ($hidden_category) {
                $cart->removeProduct($pr);
            }
        }
    }

    public static function handleBranches() {

        $session = new Session();
        $spots = Branches::all();

        if (!$session->has('activeSpotId')) {
            $session->set('activeSpotId', $spots->first()['id']);
        }

        $exist = 0;
        if(isset($_POST['spot'])) {


            foreach ($spots as $s) {
                if ($_POST['spot'] == $s['id']) {
                    $exist++;
                }
            }

            if ($exist) {
                $session->set('activeSpotId', $_POST['spot']);
                $spot_id = $_POST['spot'];
                self::removeHiddenProducts($spot_id);
            }
        }



        foreach ($spots as $s) {
            if ($session->get('activeSpotId') == $s['id']) {
                $activeSpotName = $s['name'];
                if(empty($s['phones'])) {
                    $phones = [];
                } else {
                    $phones = explode(',',$s['phones']);
                }
                $session->set('activeChatId', $s['chat_id']);
            }
        }



        $activeSpotId = $session->get('activeSpotId');

        return [
            'activeSpotName' => $activeSpotName,
            'activeSpotId'   => $activeSpotId,
            'phones'         => $phones,
            'spots'          => $spots
        ];



    }



}
