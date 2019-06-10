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
    private $parameters;

    /**
     * @var integer
     */
    private $nbLaunches;

    /**
     * @var integer
     */
    private $showRocketName;

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
    public function check()
    {
        foreach (self::MANDATORY_PARAMETERS as $mandatoryParameter) {
            if (!isset($this->parameters[$mandatoryParameter])) {
                throw new MissingParameterException();
            }
        }

        if ($this->parameters['launches'] < 1 || $this->parameters['launches'] > 5) {
            $this->nbLaunches = 1;
        } else {
            $this->nbLaunches = (int)$this->parameters['launches'];
        }

        if (!in_array($this->parameters['rocket'], [0, 1])) {
            $this->showRocketName = 0;
        } else {
            $this->showRocketName = (int)$this->parameters['rocket'];
        }
    }

    /**
     * @return int
     */
    public function getNbLaunches(): int
    {
        return $this->nbLaunches;
    }

    /**
     * @return int
     */
    public function getShowRocketName(): int
    {
        return $this->showRocketName;
    }
}
