<?php
use PHPUnit\Framework\TestCase;
use FrankAndOak\MagStripe\MagStripe;
use FrankAndOak\MagStripe\Exception\MagStripeException;

class CardReadTest extends TestCase
{
    /**
     * @dataProvider validCCStrings
     */
    public function testValidCCStrings($dataString, $account, $expYear, $expMonth, $name, $track1, $track2, $track3)
    {
        $magStripe = new MagStripe($dataString);
        $this->assertEquals($account, $magStripe->getAccount());
        $this->assertEquals($expYear, $magStripe->getExpYear());
        $this->assertEquals($expMonth, $magStripe->getExpMonth());
        $this->assertEquals($name, $magStripe->getName());
        $tracks = $magStripe->getTracks();
        $this->assertEquals($track1, $tracks[0]);
        $this->assertEquals($track2, $tracks[1]);
        if(!is_null($track3)) {
            $this->assertEquals($track3, $tracks[2]);
        } else {
            $this->assertArrayNotHasKey(2, $tracks);
        }
    }

    /**
     * @dataProvider invalidCCStrings
     */
    public function testInvalidCCStrings($dataString)
    {
        $this->expectException(MagStripeException::class);
        new MagStripe($dataString);
    }

    public function validCCStrings()
    {
        return [
            ['%B4242424242424242^SURNAME/FIRSTNAME I^15052011000000000000?;4242424242424242=15052011000000000000?',
                '4242424242424242', '15', '05', 'FIRSTNAME I SURNAME', 'B4242424242424242^SURNAME/FIRSTNAME I^15052011000000000000', '4242424242424242=15052011000000000000', null],
            ['%B4242424242424242^SURNAME/FIRSTNAME I^15052011000000000000?;4242424242424242=15052011000000000000?+123?',
                '4242424242424242', '15', '05', 'FIRSTNAME I SURNAME', 'B4242424242424242^SURNAME/FIRSTNAME I^15052011000000000000', '4242424242424242=15052011000000000000', '123'],
            ['%B4242424242424242^FIRSTNAME SURNAME^15052011000000000000?;4242424242424242=15052011000000000000?',
                '4242424242424242', '15', '05', 'FIRSTNAME SURNAME', 'B4242424242424242^FIRSTNAME SURNAME^15052011000000000000', '4242424242424242=15052011000000000000', null]
        ];
    }

    public function invalidCCStrings()
    {
        return [
            [''],
            ['%B5242424242424242^SURNAME/FIRSTNAME I^15052011000000000000?;5242424242424242=15052011000000000000?'],
            [';45645645645646456=4792?'],
            [';5646456464564565656=12491010000000000?'],
            ['%63400445654646456=000078089000000000?;3454353453453545345=000078089000000000?+345345345353453434=345345345345435345345?'],
            ['%B212562477074168^ABCD/A MR^P 1501M                                         ^?;35345345345345345=2323?'],
            ['%LC/MR/ABCDEFG/A/ABCDE?;45454545454=112015?'],
            ['%  AA 00 00 00 A  RN^ABCDEFG ABCD ABCDE         ^                           ?'],
            [';92101707137827464=2456?'],
            [';00007399=?'],
            ['%B456475756755675^ABCDE/A MR^P 1407M                                        ^?;34534534534534534=7878?'],
            [';00000000==201100100900083753?'],
        ];
    }
}