<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Tests\Unit\Forms;

use Atwx\SilverstripeAltchaSpamprotection\Forms\AltchaField;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\Challenge;
use AltchaOrg\Altcha\Hasher\Algorithm;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Kernel;
use SilverStripe\Dev\SapphireTest;

class AltchaFieldTest extends SapphireTest
{
    protected function setUp(): void
    {
        parent::setUp();
		AltchaField::config()->set('hmac_key', 'config-test-key');
    }

	public function testGenerateChallengeReturnsChallenge()
	{
		$field = new TestAltchaField('MyAltcha');
		$challenge = $field->generateChallenge();
		$this->assertInstanceOf(Challenge::class, $challenge);
	}

    public function testIsDebugModeUsesEnvironment() {
        Injector::inst()->get(Kernel::class)->setEnvironment(Kernel::DEV);
        $fieldTrue = new TestAltchaField('AltchaTrue');
		$this->assertTrue($fieldTrue->isDebugPublic());

        Injector::inst()->get(Kernel::class)->setEnvironment(Kernel::LIVE);
		$this->assertFalse($fieldTrue->isDebugPublic());
    }

	public function testIsDebugModeUsesConfigValue()
	{
		AltchaField::config()->set('debug', true);
		$fieldTrue = new TestAltchaField('AltchaTrue');
		$this->assertTrue($fieldTrue->isDebugPublic());

		AltchaField::config()->set('debug', false);
		$fieldFalse = new TestAltchaField('AltchaFalse');
		$this->assertFalse($fieldFalse->isDebugPublic());
	}

	public function testValidateAddsErrorWhenVerifyFails()
	{
		$mockAltcha = $this->createMock(Altcha::class);
		$mockAltcha->expects($this->once())
			->method('verifySolution')
			->with('wrong-solution', true)
			->willReturn(false);

		$field = new TestAltchaField('AltchaValidateFail');
		// replace the Altcha instance with our mock
		$field->altcha = $mockAltcha;
		$field->setValue('wrong-solution');

		$result = $field->validate();
		$this->assertFalse($result->isValid(), 'Validation should fail when Altcha::verifySolution returns false');
	}

	public function testValidateSucceedsWhenVerifyPasses()
	{
		$mockAltcha = $this->createMock(Altcha::class);
		$mockAltcha->expects($this->once())
			->method('verifySolution')
			->with('right-solution', true)
			->willReturn(true);

		$field = new TestAltchaField('AltchaValidatePass');
		$field->altcha = $mockAltcha;
		$field->setValue('right-solution');

		$result = $field->validate();
		$this->assertTrue($result->isValid(), 'Validation should pass when Altcha::verifySolution returns true');
	}

	public function testGenerateChallengeRespectsConfigOptions()
	{
		// set config values that AltchaField should honour
		AltchaField::config()->set('hmac_key', 'config-test-key');
		AltchaField::config()->set('algorithm', Algorithm::SHA1->value);
		AltchaField::config()->set('default_max_number', 123);
		AltchaField::config()->set('default_salt_length', 5);
		AltchaField::config()->set('expires_interval', 'PT60S');

		$field = new TestAltchaField('ConfigAltcha');

		$challenge = $field->generateChallenge();

		$this->assertInstanceOf(Challenge::class, $challenge);
		$this->assertEquals(123, $challenge->maxNumber, 'maxNumber should come from config');
		$this->assertEquals(Algorithm::SHA1->value, $challenge->algorithm, 'algorithm should come from config');
	    $salt = explode('?', $challenge->salt)[0];
        // 5 byte in hex
        $this->assertEquals(5 * 2, strlen($salt), 'salt length should come from config');
	}

	public function testChallengeEndpointIsConfigurable()
	{
		AltchaField::config()->set('challenge_endpoint', '/_my_altcha_endpoint');
		AltchaField::config()->set('hmac_key', 'config-test-key');

		$field = new TestAltchaField('EndpointAltcha');

		$this->assertEquals('/_my_altcha_endpoint', $field->getConfigValue('challenge_endpoint'));
	}
}

/**
 * Helper test subclass to expose protected behavior and allow altcha injection.
 */
class TestAltchaField extends AltchaField
{
	// Declare the property so tests can replace the Altcha instance with a mock.
	public Altcha $altcha;

	// Expose and optionally override the protected isDebugModeEnabled() behavior for assertions
	public function isDebugPublic(): bool
	{
		if ($this->debugOverride !== null) {
			return $this->debugOverride;
		}
		return $this->isDebugModeEnabled();
	}

    /**
     * Allow tests to read configured values on the instance without calling the global Config API directly.
     */
    public function getConfigValue(string $key)
    {
        return $this->config()->get($key);
    }
}
