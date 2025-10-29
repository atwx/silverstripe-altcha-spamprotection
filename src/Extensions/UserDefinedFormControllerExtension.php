<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class UserDefinedFormControllerExtension extends Extension {
    public function onAfterInit() {
        //Requirements::javascript('atwx/silverstripe-altcha-spamprotection:client/dist/main.js');
        Requirements::javascript('https://altchaplugin.ddev.site/_resources/packages/silverstripe-altcha-spamprotection/client/dist/main.js');
    }
}