<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Forms;

use SilverStripe\Core\Extension;

class AltchaFieldValidationExtension extends Extension
{
    /**
     * Validation for SS5
     *
     * @param mixed &$result
     * @param mixed &$validator
     * @return void
     */
    public function updateValidationResult(&$result, &$validator)
    {
        $value = $this->owner->value;
        if(!$value || $this->owner->altcha->verifySolution($value, true) === false) {
            $validator->validationError($this->owner->getName(), 'Altcha Captcha validation failed', 'error');
        }
    }

    /**
     * Validation for SS6
     *
     * @param mixed &$result
     * @return void
     */
    public function updateValidate(&$result)
    {
        $value = $this->owner->value;
        if(!$value || $this->owner->altcha->verifySolution($value, true) === false) {
            $result->addError('Altcha Captcha validation failed', 'error', $this->owner->getName());
        }
    }
}
