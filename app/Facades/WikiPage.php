<?php namespace jdpike\Facades;

use Illuminate\Support\Facades\Facade;

class WikiPage extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'wikiPageHelpers';
    }
}