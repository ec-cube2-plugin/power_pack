<?php

/**
 * 商品購入関連のヘルパークラス.
 *
 * @package PowerPack
 */
class plg_PowerPack_SC_Helper_Purchase extends SC_Helper_Purchase
{
    /**
     * 配送商品を設定する.
     *
     * @param  integer $shipping_id      配送先ID
     * @param  integer $product_class_id 商品規格ID
     * @param  integer $quantity         数量
     * @return void
     */
    public function setShipmentItemTemp($shipping_id, $product_class_id, $quantity)
    {
        // 配列が長くなるので, リファレンスを使用する
        $arrItems =& $_SESSION['shipping'][$shipping_id]['shipment_item'][$product_class_id];

        $arrItems['shipping_id'] = $shipping_id;
        $arrItems['product_class_id'] = $product_class_id;
        $arrItems['quantity'] = $quantity;

        $objProduct = new SC_Product_Ex();

        // カート情報から読みこめば済むと思うが、一旦保留。むしろ、カート情報も含め、セッション情報を縮小すべきかもしれない。
        /*
        $objCartSession = new SC_CartSession_Ex();
        $cartKey = $objCartSession->getKey();
        // 詳細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);
        */

        if (empty($arrItems['productsClass'])) {
            $product =& $objProduct->getDetailAndProductsClass($product_class_id);
            $arrItems['productsClass'] = $product;
        }
        $arrItems['price'] = $arrItems['productsClass']['price03'];
        $inctax = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrItems['price'], $arrItems['productsClass']['product_id'],
                                                     $arrItems['productsClass']['product_class_id']);
        $arrItems['total_inctax'] = $inctax * $arrItems['quantity'];
    }
}
