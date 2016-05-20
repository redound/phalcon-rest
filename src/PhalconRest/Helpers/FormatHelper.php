<?php

namespace PhalconRest\Helpers;

class FormatHelper
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @param $value
     *
     * @return int|null
     */
    public function int($value)
    {
        return $value !== null ? (int)$value : null;
    }

    /**
     * @param $value
     *
     * @return float|null
     */
    public function float($value)
    {
        return $value !== null ? (float)$value : null;
    }

    /**
     * @param $value
     *
     * @return float|null
     */
    public function double($value)
    {
        return $value !== null ? (double)$value : null;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function bool($value)
    {
        return $value !== null ? (bool)$value : null;
    }

    /**
     * @param $value
     *
     * @return null|string
     */
    public function date($value)
    {
        if ($value === null) {
            return null;
        }

        $date = new \DateTime(is_numeric($value) ? '@' . $value : $value);
        return $date->format(self::DEFAULT_DATE_FORMAT);
    }
}
