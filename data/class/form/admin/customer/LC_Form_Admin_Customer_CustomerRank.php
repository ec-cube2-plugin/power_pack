<?php

class LC_Form_Admin_Customer_CustomerRank extends plg_PowerPack_SC_FormParam
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->add('id', 'plain', array(
                'label' => 'ID',
                'max_length' => INT_LEN,
                'convert' => 'n',
                'constraints' => array('MAX_LENGTH_CHECK', 'NUM_CHECK'),
                'input_db' => false,
            ))
            ->add('name', 'text', array(
                'label' => '名称',
                'max_length' => STEXT_LEN,
                'convert' => 'KVa',
                'required' => true,
                'constraints' => array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'),
                'attr' => array('class' => 'box60'),
            ));
    }
}
