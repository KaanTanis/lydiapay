<?php

namespace DataGrade\LydiaPay;

use Exception;

/**
 *
 */
class Invoice
{
    public int $amount = 0;
    public ?string $uuid = null;
    public ?string $driver = null;
    public int $foreign_id;

    public ?string $card_name;
    public ?string $card_number;
    public ?string $card_cv2;
    public ?int $card_year;
    public ?int $card_month;

    public function __construct()
    {
        $this->uuid = uniqid();
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function details($key, $value = null): static
    {
        $key = is_array($key) ? $key : [$key => $value];

        foreach ($key as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }

    /**
     * @param $uuid
     * @return $this
     */
    public function uuid($uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    /*public function getUuid(): ?string
    {
        return $this->uuid;
    }*/

    /**
     * @throws Exception
     * @return $this
     */
    public function amount($amount): static
    {
        if (! is_numeric($amount)) {
            throw new Exception('Amount value should be a number (integer or float).');
        }
        $this->amount = $amount;

        return $this;
    }

    /*public function getAmount(): int
    {
        return $this->amount;
    }*/

    /**
     * @param string $driver
     * @return $this
     */
    public function driver(string $driver): static
    {
        $this->driver = $driver;
        return $this;
    }

    /*public function getDriver(): ?string
    {
        return $this->driver;
    }*/

    /**
     * @param $foreign_id
     * @return $this
     */
    public function foreign_id($foreign_id): static
    {
        $this->foreign_id = $foreign_id;
        return $this;
    }

    /**
     * @param null $card_name
     * @return $this
     */
    public function card_name($card_name = null): Invoice
    {
        $this->card_name = $card_name;
        return $this;
    }

    /**
     * @param null $card_number
     * @return $this
     */
    public function card_number($card_number = null): Invoice
    {
        $this->card_number = $card_number;
        return $this;
    }

    /**
     * @param null $card_cv2
     * @return $this
     */
    public function card_cv2($card_cv2 = null): Invoice
    {
        $this->card_cv2 = $card_cv2;
        return $this;
    }

    /**
     * @param null $card_year
     * @return $this
     */
    public function card_year($card_year = null): Invoice
    {
        $this->card_year = $card_year;
        return $this;
    }

    /**
     * @param null $card_month
     * @return $this
     */
    public function card_month($card_month = null): Invoice
    {
        $this->card_month = $card_month;
        return $this;
    }
}
