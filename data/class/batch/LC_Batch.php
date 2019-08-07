<?php

/**
 * LC_Batch
 *
 * @package PowerPack
 */
abstract class LC_Batch
{
    /**
     * Batch を初期化する.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Batch のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
    }

    /**
     * Batch のaction.
     *
     * @return void
     */
    public abstract function action();

}
