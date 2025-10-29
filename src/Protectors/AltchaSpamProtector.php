<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Protectors;

use Atwx\SilverstripeAltchaSpamprotection\Forms\AltchaField;
use SilverStripe\SpamProtection\SpamProtector;

class AltchaSpamProtector implements SpamProtector
{
    public function getFormField($name = null, $title = null, $value = null): AltchaField
    {
        return AltchaField::create($name, $title, $value);
    }

    /**
     * Not used.
     *
     * @codeCoverageIgnore
     */
    public function setFieldMapping($fieldMapping)
    {
    }
}