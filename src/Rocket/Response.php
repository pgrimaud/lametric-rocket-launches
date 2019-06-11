<?php

declare(strict_types=1);

namespace Rocket;

class Response
{
    const ICON_ID = 'i29438';

    /**
     * @param array $data
     *
     * @return string
     */
    public function asJson(array $data = []): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function error(string $value = 'INTERNAL ERROR'): string
    {
        return $this->asJson([
            'frames' => [
                [
                    'index' => 0,
                    'text'  => $value,
                    'icon'  => 'null'
                ]
            ]
        ]);
    }

    /**
     * @param array $launches
     * @param Validator $validator
     *
     * @return string
     *
     * @throws \Exception
     */
    public function data(array $launches, Validator $validator): string
    {
        $frameIndex = 0;
        $frames     = [];

        for ($i = 0; $i < $validator->getNbLaunches(); $i++) {

            $rocket = $launches[$i];

            if ($validator->getShowRocketName()) {

                $frames[] = [
                    'icon'  => self::ICON_ID,
                    'index' => $frameIndex,
                    'text'  => $rocket['name'],
                ];

                $frameIndex++;
            }

            $frames[] = [
                'icon'  => self::ICON_ID,
                'index' => $frameIndex,
                'text'  => $this->dateDiff($rocket['date']),
            ];

            $frameIndex++;
        }

        return $this->asJson([
            'frames' => $frames
        ]);
    }

    /**
     * @param string $date
     * @return string
     *
     * @throws \Exception
     */
    private function dateDiff(string $date): string
    {
        $dateNow = new \DateTime();
        $diff    = $dateNow->diff(new \DateTime($date));

        $stringToReturn = '';

        $hasDay  = false;
        $hasHour = false;
        $hasMin  = false;

        if ($diff->d) {
            $stringToReturn .= $diff->d . 'D ';
            $hasDay         = true;
        }

        if ($diff->h) {
            $stringToReturn .= $diff->h . 'H ';
            $hasHour        = true;
        }

        if ($diff->i && !$hasDay) {
            $stringToReturn .= $diff->i . 'M ';
            $hasMin         = true;
        }

        if (($diff->s || $hasMin) && !$hasDay && !$hasHour) {
            $stringToReturn .= $diff->s . 'S';
        }

        return trim($stringToReturn);

    }
}
