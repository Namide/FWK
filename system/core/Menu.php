<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menu
 *
 * @author Damien
 */
class Menu
{
    private static $_instance = null;

    public function __construct()
    {
        
    }
    
    


    public static function getInstance()
    {
        if( is_null(self::$_instance) )
        {
            self::$_instance = new Menu();
        }
        return self::$_instance;
    }
}
