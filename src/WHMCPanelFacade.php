<?php

namespace Motwreen\WHMCPanel;

use Illuminate\Support\Facades\Facade;

class WHMCPanelFacade extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'whmcpanel';
    }
    
}
