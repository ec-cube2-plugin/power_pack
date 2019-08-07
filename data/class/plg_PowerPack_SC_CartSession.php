<?php

/**
 * カートセッション管理クラス
 *
 * @package PowerPack
 */
class plg_PowerPack_SC_CartSession extends SC_CartSession
{

    /**
     * カート内の商品の妥当性をチェックする.
     *
     * エラーが発生した場合は, 商品をカート内から削除又は数量を調整し,
     * エラーメッセージを返す.
     *
     * 1. 商品種別に関連づけられた配送業者の存在チェック
     * 2. 削除/非表示商品のチェック
     * 3. 販売制限数のチェック
     * 4. 在庫数チェック
     *
     * @param  string $productTypeId 商品種別ID
     * @return string エラーが発生した場合はエラーメッセージ
     */
    public function checkProducts($productTypeId)
    {
        $objProduct = new SC_Product_Ex();
        $objDelivery = new SC_Helper_Delivery_Ex();
        $arrDeliv = $objDelivery->getList($productTypeId);
        $tpl_message = '';

        $objCustomer = new SC_Customer_Ex();
        $login = $objCustomer->isLoginSuccess();

        // カート内の情報を取得
        $arrItems = $this->getCartList($productTypeId);
        foreach ($arrItems as &$arrItem) {
            $product =& $arrItem['productsClass'];
            /*
             * 表示/非表示商品のチェック
             */
            if (SC_Utils_Ex::isBlank($product) || $product['status'] != 1) {
                $this->delProduct($arrItem['cart_no'], $productTypeId);
                $tpl_message .= "※ 現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。\n";
            } else {
                /*
                 * 配送業者のチェック
                 */
                if (SC_Utils_Ex::isBlank($arrDeliv)) {
                    $tpl_message .= '※「' . $product['name'] . '」はまだ配送の準備ができておりません。';
                    $tpl_message .= '恐れ入りますがお問い合わせページよりお問い合わせください。' . "\n";
                    $this->delProduct($arrItem['cart_no'], $productTypeId);
                }

                /*
                 * 販売制限数, 在庫数のチェック
                 */
                $limit = $objProduct->getBuyLimit($product);
                if (!is_null($limit) && $arrItem['quantity'] > $limit) {
                    if ($limit > 0) {
                        $this->setProductValue($arrItem['id'], 'quantity', $limit, $productTypeId);
                        $total_inctax = $limit * SC_Helper_TaxRule_Ex::sfCalcIncTax($arrItem['price'],
                            $product['product_id'],
                            $arrItem['id'][0]);
                        $this->setProductValue($arrItem['id'], 'total_inctax', $total_inctax, $productTypeId);
                        $tpl_message .= '※「' . $product['name'] . '」は販売制限(または在庫が不足)しております。';
                        $tpl_message .= "一度に数量{$limit}を超える購入はできません。\n";
                    } else {
                        $this->delProduct($arrItem['cart_no'], $productTypeId);
                        $tpl_message .= '※「' . $product['name'] . "」は売り切れました。\n";
                        continue;
                    }
                }

                /*
                 * 会員限定 チェック
                 */
                if ($product['require_login'] && !$login) {
                    $this->delProduct($arrItem['cart_no'], $productTypeId);
                    $tpl_message .= '※「' . $product['name'] . "」は会員限定商品です。ログインをしてからカートに入れてください。\n";
                    continue;
                }
            }
        }

        return $tpl_message;
    }

    /**
     * getCartList用にcartSession情報をセットする
     *
     * @param  integer $productTypeId 商品種別ID
     * @param  integer $key
     * @return void
     *
     * MEMO: せっかく一回だけ読み込みにされてますが、税率対応の関係でちょっと保留
     */
    public function setCartSession4getCartList($productTypeId, $key)
    {
        $objProduct = new SC_Product_Ex();

        $this->cartSession[$productTypeId][$key]['productsClass']
            =& $objProduct->getDetailAndProductsClass($this->cartSession[$productTypeId][$key]['id']);

        $price = $this->cartSession[$productTypeId][$key]['productsClass']['price03'];
        $this->cartSession[$productTypeId][$key]['price'] = $price;

        $this->cartSession[$productTypeId][$key]['point_rate']
            = $this->cartSession[$productTypeId][$key]['productsClass']['point_rate'];

        $quantity = $this->cartSession[$productTypeId][$key]['quantity'];
        $incTax = SC_Helper_TaxRule_Ex::sfCalcIncTax($price,
            $this->cartSession[$productTypeId][$key]['productsClass']['product_id'],
            $this->cartSession[$productTypeId][$key]['id'][0]);

        $total = $incTax * $quantity;

        $this->cartSession[$productTypeId][$key]['price_inctax'] = $incTax;
        $this->cartSession[$productTypeId][$key]['total_inctax'] = $total;
    }

    /**
     * 商品種別ごとにカート内商品の一覧を取得する.
     *
     * @param  integer $productTypeId 商品種別ID
     * @param  integer $pref_id       税金計算用注文者都道府県ID
     * @param  integer $country_id    税金計算用注文者国ID
     * @return array   カート内商品一覧の配列
     */
    public function getCartList($productTypeId, $pref_id = 0, $country_id = 0)
    {
        $objProduct = new SC_Product_Ex();
        $max = $this->getMax($productTypeId);
        $arrRet = array();
        /*

                $const_name = '_CALLED_SC_CARTSESSION_GETCARTLIST_' . $productTypeId;
                if (defined($const_name)) {
                    $is_first = true;
                } else {
                    define($const_name, true);
                    $is_first = false;
                }

        */
        for ($i = 0; $i <= $max; $i++) {
            if (isset($this->cartSession[$productTypeId][$i]['cart_no'])
                && $this->cartSession[$productTypeId][$i]['cart_no'] != '') {

                // 商品情報は常に取得
                // TODO: 同一インスタンス内では1回のみ呼ぶようにしたい
                // TODO: ここの商品の合計処理は getAllProductsTotalや getAllProductsTaxとで類似重複なので統一出来そう
                /*
                                // 同一セッション内では初回のみDB参照するようにしている
                                if (!$is_first) {
                                    $this->setCartSession4getCartList($productTypeId, $i);
                                }
                */

                $this->cartSession[$productTypeId][$i]['productsClass']
                    =& $objProduct->getDetailAndProductsClass($this->cartSession[$productTypeId][$i]['id']);

                $price = $this->cartSession[$productTypeId][$i]['productsClass']['price03'];
                $this->cartSession[$productTypeId][$i]['price'] = $price;

                $this->cartSession[$productTypeId][$i]['point_rate']
                    = $this->cartSession[$productTypeId][$i]['productsClass']['point_rate'];

                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

                $arrTaxRule = SC_Helper_TaxRule_Ex::getTaxRule(
                    $this->cartSession[$productTypeId][$i]['productsClass']['product_id'],
                    $this->cartSession[$productTypeId][$i]['productsClass']['product_class_id'],
                    $pref_id,
                    $country_id);
                $incTax = $price + SC_Helper_TaxRule_Ex::calcTax($price, $arrTaxRule['tax_rate'], $arrTaxRule['tax_rule'], $arrTaxRule['tax_adjust']);

                $total = $incTax * $quantity;
                $this->cartSession[$productTypeId][$i]['price_inctax'] = $incTax;
                $this->cartSession[$productTypeId][$i]['total_inctax'] = $total;
                $this->cartSession[$productTypeId][$i]['tax_rate'] = $arrTaxRule['tax_rate'];
                $this->cartSession[$productTypeId][$i]['tax_rule'] = $arrTaxRule['tax_rule'];
                $this->cartSession[$productTypeId][$i]['tax_adjust'] = $arrTaxRule['tax_adjust'];

                $arrRet[] = $this->cartSession[$productTypeId][$i];

                // セッション変数のデータ量を抑制するため、一部の商品情報を切り捨てる
                // XXX 上で「常に取得」するのだから、丸ごと切り捨てて良さそうにも感じる。
                $this->adjustSessionProductsClass($this->cartSession[$productTypeId][$i]['productsClass']);
            }
        }

        return $arrRet;
    }

}
