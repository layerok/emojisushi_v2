<?php namespace Layerok\TgMall\Classes;

class Constants
{
    public const UPDATE_CART_TOTAL = 'update_cart_total';
    public const UPDATE_CART_TOTAL_IN_CATEGORY = 'update_cart_total_in_category';
    public const ADD_PRODUCT_IN_CATEGORY = 'add_product_in_category';

    public const NOOP = "noop";

    public const STEP_PHONE = 1;
    public const STEP_PAYMENT = 2;
    public const STEP_PAYMENT_CASH = 3;
    public const STEP_DELIVERY = 4;
    public const STEP_DELIVERY_COURIER = 5;
    public const STEP_DELIVERY_SELF = 6;
    public const STEP_COMMENT = 7;
    public const STEP_CONFIRM_ORDER = 100;
}
