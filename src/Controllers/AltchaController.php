<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Controllers;

use Atwx\SilverstripeAltchaSpamprotection\Forms\AltchaField;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Middleware\HTTPCacheControlMiddleware;

class AltchaController extends Controller {


    private static $allowed_actions = [
        'challengeoptions',
    ];

    private static $url_handlers = [
        'GET challengeoptions' => 'challengeoptions'
    ];

    public function index() {
        return '';
    }

    public function challengeoptions() {
        HTTPCacheControlMiddleware::singleton()->disableCache();
        $field = AltchaField::create('AltchaField');
        $challenge = $field->generateChallenge();
        return $this
            ->getResponse()
            ->addHeader('Content-Type', 'application/json')
            ->setBody(json_encode($challenge));
    }

}