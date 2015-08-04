<?php
/**
 * @author Kadir Emin İslamoğlu <keislamoglu@yandex.com>
 * @date 2015-08-03
 * @url <https://github.com/keislamoglu>
 */

class TcKimlikNoSorgula {
    private $tcKimlikNo;
    private $ad;
    private $soyad;
    private $dogumYili;

    /**
     * TcKimlik No
     * @param $tcKimlikNo
     * @return $this
     */
    public static function tcKimlikNo($tcKimlikNo) {
        $instance = new static;
        $instance->tcKimlikNo = $tcKimlikNo;
        return $instance;
    }

    /**
     * Ad
     * @param $ad
     * @return $this
     */
    public function ad($ad) {
        $this->ad = $this->upperCase($ad);
        return $this;
    }

    /**
     * Soyad
     * @param $soyad
     * @return $this
     */
    public function soyad($soyad) {
        $this->soyad = $this->upperCase($soyad);
        return $this;
    }

    /**
     * Doğum yılı
     * @param $dogumYili
     * @return $this
     */
    public function dogumYili($dogumYili) {
        $this->dogumYili = $dogumYili;
        return $this;
    }

    /**
     * Doğrulama
     * @return bool
     * @throws Exception
     */
    public function dogrula() {
        if (!isset($this->ad) || !isset($this->soyad) || !isset($this->dogumYili) || !isset($this->tcKimlikNo)) {
            throw new \Exception("Doğrulama için T.C. Kimlik No, Ad, Soyad, Doğum Yılı tanımlanmış olması gerekir");
        }
        $toSend =
            '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
                        <TCKimlikNo>' . $this->tcKimlikNo . '</TCKimlikNo>
                        <Ad>' . $this->ad . '</Ad>
                        <Soyad>' . $this->soyad . '</Soyad>
                        <DogumYili>' . $this->dogumYili . '</DogumYili>
                    </TCKimlikNoDogrula>
                </soap:Body>
            </soap:Envelope>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $toSend);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'POST /Service/KPSPublic.asmx HTTP/1.1',
            'Host: tckimlik.nvi.gov.tr',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
            'Content-Length: ' . strlen($toSend)
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        return strip_tags($response) == 'true';
    }

    /**
     * Büyük harflere dönüştürme
     * @param $string
     * @return string
     */
    public function upperCase($string) {
        $string = str_replace(array('i'), array('İ'), $string);
        return mb_convert_case($string, MB_CASE_UPPER, "UTF-8");
    }
} 