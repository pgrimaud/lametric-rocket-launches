<?php

declare(strict_types=1);

namespace Rocket;

use Rocket\Exception\MissingParameterException;

class Validator
{
    const MANDATORY_PARAMETERS = [
        'launches',
        'rocket',
    ];

    /**
     * @var array
     */
    private array $parameters;

    /**
     * @var int
     */
    private int $nbLaunches;

    /**
     * @var bool
     */
    private bool $showRocketName;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @throws MissingParameterException
     */
    public function check(): void
    {
        foreach (self::MANDATORY_PARAMETERS as $mandatoryParameter) {
            if (!isset($this->parameters[$mandatoryParameter])) {
                throw new MissingParameterException('Missing parameter');
            }
        }

        if ($this->parameters['launches'] < 1 || $this->parameters['launches'] > 5) {
            $this->nbLaunches = 1;
        } else {
            $this->nbLaunches = (int)$this->parameters['launches'];
        }

        $this->showRocketName = (bool)$this->parameters['rocket'];
    }

    /**
     * @return int
     */
    public function getNbLaunches(): int
    {
        return $this->nbLaunches;
    }

    /**
     * @return bool
     */
    public function getShowRocketName(): bool
    {
        return $this->showRocketName;
    }
}
