<?php

declare(strict_types=1);

namespace Shart\Services;

use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Shart\Property;

class TokenService
{
    /**
     * @var mixed
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $parser;

    /**
     * @var mixed
     */
    protected $pub;

    /**
     * @var mixed
     */
    protected $signer;

    public function __construct()
    {
        $keychain = new Keychain();
        $this->signer = new Sha256();
        $this->parser = new Parser();
        $this->key = $keychain->getPrivateKey(
            sprintf('file://%s', config(Property::AUTH_PRIVATE)),
            config(Property::AUTH_PHRASE)
        );
        $this->pub = $keychain->getPublicKey(
            sprintf('file://%s', config(Property::AUTH_PUBLIC))
        );
    }

    /**
     * @return mixed
     */
    public function extract(string $token): array
    {
        $data = new ValidationData();
        $attributes = [];
        $parsedToken = $this->parser->parse($token);

        if (true === $parsedToken->verify($this->signer, $this->pub) && true === $parsedToken->validate($data)) {
            $claims = $parsedToken->getClaims();

            foreach ($claims as $key => $value) {
                $attributes[$key] = $value instanceof Claim ? $value->getValue() : $value;
            }
        }

        return $attributes;
    }

    public function generate(DateTimeImmutable $expired, array $payload): string
    {
        $builder = new Builder();
        $builder->expiresAt($expired);
        $builder->canOnlyBeUsedAfter(new DateTimeImmutable());
        $builder->issuedAt(new DateTimeImmutable());

        foreach ($payload as $key => $value) {
            $builder->withClaim($key, $value);
        }

        return (string) $builder->getToken($this->signer, $this->key);
    }
}
