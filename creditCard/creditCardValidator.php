<?php

class creditCardValidator {
    private $valid;
    private $cardNumber;
    private $cardMaker;

    public function __construct($cardNumber) {
        $this->cardNumber = $cardNumber;

    }

    public function toString() {
        if($this->valid) {
            echo "Card #$this->cardNumber from $this->cardMaker is Valid";
        } else {
            echo "Invalid Credit Card";
        }
    }

    public function validate() {
        if(!$this->luha_check()) {
            $this->valid = false;
            echo "Credit Card does not pass luha check.";
            return;
        }
        if(!$this->cardMakerCheck()) {
            $this->valid = false;
            echo "Card not issued by accepted Card Maker.";
            return;
        }
        $this ->valid = true;
        echo "Valid Credit Card Number";
    }

    private function cardMakerCheck() {
        $num = $this->cardNumber;
        $len = strlen($num);
        if(preg_match("/^(34|37)\d{13}$/i",$num)) {
            //American Express
            //Length = 15, INN = [34, 37]
            $this->cardMaker = "American Express";
            return true;
        } elseif(preg_match("/^4(\d{12}|\d{15}|\d{18})$/i",$num)) {
            //visa
            //Length = [13,16,19], INN = 4
            $this->cardMaker = "Visa";
            return true;
        } elseif(preg_match("/^\d{16}$/i",$num)) {
            //MasterCard
            //Length = 16, INN = [2221-2720, 51-55]
            $r = (int) substr($num,0,4);
            $r2 = (int) substr($num,0,2);
            if((2221 <= $r && $r <= 2720) || (51 <= $r2 && $r2 <= 55)) {
                $this->caradMaker = "Master Card";
                return true;
            }
        }
        $this->cardMaker = "Invalid Card Number"; 
        return false;
    }

    private function luha_check() {
        $num = $this->cardNumber;
        $length = strlen($num);
        $parity = $length % 2;
        $total = 0;
        for($i=0; $i < $length; $i++) {
            if($i%2 == $parity) {
                $sum = $num[$i] * 2;
                $num[$i] = ($sum > 9) ? $sum-9 : $sum;
            }
                $total += $num[$i];
        }
        if($total % 10 == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}