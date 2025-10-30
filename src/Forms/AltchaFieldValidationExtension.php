<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Forms;

use SilverStripe\Core\Extension;

class AltchaFieldValidationExtension extends Extension
{
    public function updateValidationResult(&$result, &$validator)
    {
        $value = $this->owner->value;
        if(!$value || $this->owner->altcha->verifySolution($value, true) === false) {
            $validator->validationError($this->owner->getName(), 'Altcha Captcha validation failed', 'error');
        }
    }
}
