<?php

declare(strict_types=1);

namespace Shart;

use Illuminate\Contracts\Auth\Authenticatable;
use Ramsey\Uuid\Uuid;

class Authority
{
    public const ANONYMOUS = 'system:anonymous';

    public const APPLICATION = 'system:application';

    public const CONSOLE = 'system:console';

    public const INSTALLER = 'system:installer';

    public const PREFERRED = 'system:preferred';

    /**
     * @var mixed
     */
    private $auth;

    /**
     * @var mixed
     */
    private static $selected;

    /**
     * @var mixed
     */
    private $session;

    /**
     * @param Authenticatable $auth
     */
    public function __construct(? Authenticatable $auth)
    {
        $this->auth = $auth;
        $this->session = Uuid::uuid4()->toString();
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        switch (static::$selected) {
            case static::APPLICATION:
                return static::APPLICATION;
                break;
            case static::CONSOLE:
                return static::CONSOLE;
                break;
            case static::INSTALLER:
                return static::INSTALLER;
                break;
            case static::ANONYMOUS:
                return static::ANONYMOUS;
                break;
            case static::PREFERRED:
            default:
                return null === $this->auth ? static::ANONYMOUS : sprintf('user:%s', $this->auth->getAuthIdentifier());
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        switch (static::$selected) {
            case static::APPLICATION:
            case static::CONSOLE:
            case static::INSTALLER:
            case static::ANONYMOUS:
                return $this->session;
                break;
            case static::PREFERRED:
            default:
                return null === $this->auth ? $this->session : $this->auth->get('session') ?: $this->session;
                break;
        }
    }

    public static function setSelected(string $selected)
    {
        static::$selected = $selected;
    }
}
