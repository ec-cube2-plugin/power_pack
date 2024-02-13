<?php

/**
 * 会員検索用クラス
 *
 * @package PowerPack
 */
class plg_PowerPack_SC_CustomerList extends SC_CustomerList
{
    public function __construct($array, $mode = '')
    {
        parent::__construct($array, $mode);

        PowerPack::hook('SC_CustomerList::__construct', array($this, $array, $mode));
    }

    public function getList()
    {
        parent::getList();

        PowerPack::hook('SC_CustomerList::getList', array($this));

        return $this->getSql(2);
    }
}
