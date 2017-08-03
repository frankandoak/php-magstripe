<?php

namespace FrankAndOak\MagStripe;

use FrankAndOak\MagStripe\Exception\MagStripeException;
use Inacho\CreditCard;

class MagStripe
{

    /** @var  string */
    private $dataString;

    /** @var string */
    private $account;

    /** @var  int */
    private $expYear;

    /** @var  int */
    private $expMonth;

    /** @var  string */
    private $name;

    /** @var  string[] */
    private $tracks;

    /**
     * MagStripe constructor. Receives the raw data string and parses it.
     *
     * @param $dataString
     */
    public function __construct($dataString)
    {
        $this->dataString = $dataString;
        $this->parseTracks();
        $this->extractTrackInfo();
    }

    /**
     * Parse the data string into the tracks following the ISO7811 format. For more information
     * about the data string, check the link below.
     * @link http://www.card-device.com/files/201603/20160309030103777.pdf
     * @throws MagStripeException
     */
    private function parseTracks()
    {
        preg_match_all('/%(.+?)\?;(.+?)\?(\+(.+?)\?)?/', $this->dataString, $this->tracks, PREG_SET_ORDER);
        if (empty($this->tracks)) {
            throw new MagStripeException('Invalid format for data string');
        }
        $this->tracks = $this->tracks[0];
        if (!empty($this->tracks[3])) {
            $this->tracks[3] = $this->tracks[4];
            unset($this->tracks[4]);
        }
        array_splice($this->tracks, 0, 1);
    }

    /**
     * Parse each track individually to extract the info from the card. Do validation
     * on the credit card number in each track,  expiry dates and check for inconsistencies.
     * @throws MagStripeException
     */
    private function extractTrackInfo()
    {
        $track1 = $this->tracks[0];
        if ($track1[0] != 'B') {
            throw new MagStripeException('Wrong format for first track');
        }
        $track1 = explode('^', substr($track1, 1));
        if (count($track1) != 3) {
            throw new MagStripeException('Wrong format for first track');
        }
        $this->account = $track1[0];
        $name = explode('/', $track1[1]);
        $data = $track1[2];
        $this->expYear = substr($data, 0, 2);
        $this->expMonth = substr($data, 2, 2);

        $this->name = count($name) > 1 ? sprintf('%s %s', trim($name[1]), trim($name[0])) : trim($name[0]);

        $track2 = explode('=', $this->tracks[1]);
        if (count($track2) != 2) {
            throw new MagStripeException('Wrong format for third track');
        }
        $data = $track2[1];
        if ($this->expYear != substr($data, 0, 2) || $this->expMonth != substr($data, 2, 2)) {
            throw new MagStripeException('Expiration dates from both tracks do not match');
        }

        // validate the card numbers
        if ($this->account != $track2[0]) {
            throw new MagStripeException('Credit card number mismatch in tracks.');
        }
        $this->validateCardNumber($this->account);
        $this->validateCardNumber($track2[0]);
    }

    /**
     * Delegate the card number validation
     * @param $number
     * @throws MagStripeException
     */
    private function validateCardNumber($number)
    {
        $number = preg_replace('[^0-9]', '', $number);
        $validation = CreditCard::validCreditCard($number);
        if (empty($validation['valid'])) {
            throw new MagStripeException('Invalid credit card number.');
        }
    }

    /**
     * @return string
     */
    public function getDataString()
    {
        return $this->dataString;
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return int
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * @return int
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \string[]
     */
    public function getTracks()
    {
        return $this->tracks;
    }
}
