<?php

namespace Atwx\SilverstripeAltchaSpamprotection\Forms;

use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\Challenge;
use AltchaOrg\Altcha\ChallengeOptions;
use AltchaOrg\Altcha\Hasher\Algorithm;
use SilverStripe\Control\Director;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\FormField;

class AltchaField extends FormField
{
    /**
     * HMAC key for Altcha
     * @config AltchaField.hmac_key
     */
    private static ?string $hmac_key = null;

    /**
     * Hashing algorithm to use (`SHA-1`, `SHA-256`, `SHA-512`, default: `SHA-256`).
     * @config AltchaField.algorithm
     */
    private static string $algorithm = 'SHA-256';

    /**
     * Default maximum number for the Altcha random number generator (default: 1,000,000)
     * @config AltchaField.default_max_number
     */
    private static int $default_max_number = ChallengeOptions::DEFAULT_MAX_NUMBER;

    /**
     * Default salt length for the Altcha challenges (default: 12)
     * @config AltchaField.default_salt_length
     */
    private static int $default_salt_length = 12;

    /**
     * Default expiration interval for the Altcha challenges (default: 20 seconds)
     * @config AltchaField.expires_interval
     */
    private static string $expires_interval = 'PT20S';

    /**
     * Endpoint to fetch challenge options from
     * @config AltchaField.challenge_endpoint
     */
    private static string $challenge_endpoint = '/_altchaspamprotection/challengeoptions';

    /**
     * Debug mode for Altcha
     * @config AltchaField.debug
     */
    private static ?bool $debug = null;

    public function __construct($name, $title = null, $value = '')
    {
        parent::__construct($name, $title, $value);
        $hmac_key = $this->config()->get('hmac_key');
        if(!$hmac_key) {
            throw new \InvalidArgumentException('AltchaField.hmac_key configuration is required');
        }
        $this->altcha = new Altcha($hmac_key);
    }


    public function generateChallenge(): Challenge
    {
        $algorithm = Algorithm::from($this->config()->get('algorithm'));
        $options = new ChallengeOptions(
            algorithm: $algorithm,
            maxNumber: $this->config()->get('default_max_number'),
            expires: (new \DateTimeImmutable())->add(new \DateInterval($this->config()->get('expires_interval'))),
            saltLength: $this->config()->get('default_salt_length')
        );

        return $this->altcha->createChallenge($options);
    }

    public function Field($properties = []): string
    {
        $challengeUrl = $this->config()->get('challenge_endpoint');
        $fieldName = $this->getName();
        $defaults = [
            'ChallengeUrl' => $challengeUrl,
            'ID' => $fieldName,
            'Name' => $fieldName,
            'Debug' => $this->isDebugModeEnabled()
        ];

        $data = array_merge($defaults, is_array($properties) ? $properties : []);
        return $this->customise($data)->renderWith('Atwx\AltchaSpamprotection\Forms\AltchaField');
    }

    protected function isDebugModeEnabled(): bool
    {
        if($this->config()->get('debug') === null) {
            return Director::isDev();
        }
        return (bool) $this->config()->get('debug');
    }

    public function validate(): ValidationResult
    {
        $result = ValidationResult::create();
        $value = $this->value;
        if(!$value || $this->altcha->verifySolution($value, true) === false) {
            $result->addError('Altcha Captcha validation failed');
        }
        return $result;
    }
}