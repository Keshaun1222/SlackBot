<?php
namespace SlackBot;

class APICall {
    private $alliancesAPI = "https://politicsandwar.com/api/alliances/";
    private $allianceAPI = "https://politicsandwar.com/api/alliance/id=";
    private $nationsAPI = "https://politicsandwar.com/api/nations/";
    private $nationAPI = "https://politicsandwar.com/api/nation/id=";
    private $tradeAPI = "https://politicsandwar.com/api/tradeprice/resource=";
    private $cityAPI = "https://politicsandwar.com/api/city/id=";
    private $warAPI = "http://cloudnation.koso.com.br/cnalliancewar/active_wars_api";

    public function call($name, $id = null) {
        $name .= "API";
        $call = $this->$name;
        if ($id)
            $call .= $id;
        $json_string = file_get_contents($call);
        return json_decode($json_string, true);
    }

    public function callFromWeb($url) {
        $data = file_get_contents($url);
        return strip_tags($data);
    }
}