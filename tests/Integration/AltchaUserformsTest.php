<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Tests\Integration;

use Atwx\SilverstripeAltchaSpamprotection\Forms\AltchaField;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;
use SilverStripe\UserForms\Model\UserDefinedForm;
use SilverStripe\Versioned\Versioned;

class AltchaUserformsTest extends FunctionalTest
{
    protected $usesDatabase = true;
    protected static $fixture_file = '../fixture.yml';

    protected static $required_extensions = [
        UserDefinedForm::class => [
            'Atwx\SilverstripeAltchaSpamprotection\Extensions\UserDefinedFormControllerExtension',
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        AltchaField::config()->set('hmac_key', 'config-test-key');
        FormSpamProtectionExtension::config()->set('default_spam_protector', 'Atwx\SilverstripeAltchaSpamprotection\Protectors\AltchaSpamProtector');
    }

    public function testUserformsFormContainsAltchaField()
    {
        $formPage = $this->objFromFixture(UserDefinedForm::class, 'basic-form-page');
        $formPage->publishRecursive();

        $result = $this->get($formPage->Link());
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('<altcha-widget', $result->getBody());
    }
}
