<?php

namespace App\Classes\BST;

/**
 * Class ApiPlaceClass
 * @package App\Classes\BSD;
 */
class ApiPlaceClass
{
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $graphQl;
    /**
     * @var string
     */
    protected $login;
    /**
     * @var mixed
     */
    protected $user;
    /**
     * @var mixed
     */
    protected $password;
    /**
     * @var
     */
    protected $token;

    /**
     * ApiPlaceClass constructor.
     */
    public function __construct()
    {
        $this->url = 'https://geometry-invest.bst.digital';
        $this->graphQl = $this->url.'/graphql';
        $this->login = $this->url.'/accounts/login/';
        $this->user = env('BSD_USER');
        $this->password = env('BSD_PASSWORD');
    }

    /**
     * @return bool
     */
    public function getAuthToken()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->login);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'username' => $this->user,
            'password' => $this->password
        ]);
        $out = curl_exec($ch);
        $response = json_decode($out, true);

        if (isset($response['data']) && isset($response['data']['tokenAuth'])) {
            $token = $response['data']['tokenAuth']['token'];
            $this->setToken($token);

            return $token;
        }

        return false;
    }

    /**
     * @param int $limit
     * @param int $offset
     */
    public function getMarkets($limit = 10, $offset = 0)
    {
        $query = "{places(model: \"Market\",typeOfPage: \"markets_list\",limit:\"{$limit}\", offset:\"{$offset}\", filters: {is_deleted: false}, orderBy: \"-id\")}";

        return $this->request($query);
    }

    /**
     * @param int $marketId
     * @return mixed
     */
    public function getGeoEnv(int $marketId)
    {
        $query = "{market(id:{$marketId}){geoEnv}}";

        return $this->request($query);
    }

    /**
     * @param int $marketId
     * @return mixed
     */
    public function getCoordinates(int $marketId)
    {
        $query = "{market(id:{$marketId}){lat,lon}}";
        $response = $this->request($query);

        if (isset($response['data']['market'])) {
            return [
                $response['data']['market']['lat'],
                $response['data']['market']['lon']
            ];
        }

        return [];
    }

    /**
     * @param array $firstPoint
     * @param array $secondPoint
     * @return mixed
     */
    public function getLayersDailyUsers(array $firstPoint, array $secondPoint)
    {
        $firstPointStr = implode(',', $firstPoint);
        $secondPointStr = implode(',', $secondPoint);

        $query = "{layers(bbox:[[{$firstPointStr}],[{$secondPointStr}]],model:\"Locomizer\",column:\"traffic\",context:\"data_type=Pedestrian users\")}";
        $response = $this->request($query);

        if (isset($response['data']['layers']['features'][0])) {
            $feature = $response['data']['layers']['features'][0];
            return $feature['properties']['traffic'];
        }

        return 0;
    }

    /**
     * @param array $firstPoint
     * @param array $secondPoint
     * @return mixed
     */
    public function getResidentPeople(array $firstPoint, array $secondPoint)
    {
        $firstPointStr = implode(',', $firstPoint);
        $secondPointStr = implode(',', $secondPoint);

        $query = "{layers(bbox:[[{$firstPointStr}],[{$secondPointStr}]],model:\"GPSv3\",column:\"resident_people\",context:\"track_type=all,interval=all_day\")}";
        $response = $this->request($query);

        if (isset($response['data']['layers']['features'][0])) {
            $feature = $response['data']['layers']['features'][0];
            return $feature['properties']['resident_people'];
        }

        return 0;
    }

    /**
     * @param string $lon
     * @param string $lat
     * @param string $address
     * @param string $city
     * @return mixed
     */
    public function createMarket(string $lon, string $lat, string $address, string $city)
    {
        $params = [
            'market' => [
                'lon' => $lon,
                'lat' => $lat,
                'address' => $address,
                'city' => $city,
            ],
        ];

        $post = [
            'variables' => json_encode($params),
            'operationName' => 'createMarket'
        ];

        $query = "mutation createMarket(\$market:MarketCreateInput!){createMarket(market:\$market){data}}";

        $response = $this->request($query, $post);

        if (isset($response['data']['createMarket']['data']['id'])) {
            return $response['data']['createMarket']['data']['id'];
        }

        return null;
    }

    /**
     * @param int $placeId
     * @param string $lon
     * @param string $lat
     * @param string $address
     * @return mixed|null
     */
    public function updateMarket(int $placeId, string $lon, string $lat, string $address)
    {
        $query = "mutation {changeGeoLocation(placeId:{$placeId},placeType: \"Market\", lon: {$lon}, lat: {$lat}, address: \"{$address}\"){result}}";
        $response = $this->request($query);

        if (isset($response['data']['changeGeoLocation']['result']['updated'])) {
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function updateStatistic(int $id)
    {
        $query = "mutation{doGeoAnalytics(model:\"Market\",instanceId:{$id}){result}}";

        return $this->request($query);
    }

    /**
     * @param array $firstPoint
     * @param array $secondPoint
     * @param string $column
     * @param string|null $hint
     * @return mixed
     */
    public function bstGetHitMaps(array $firstPoint, array $secondPoint, string $column, ?string $hint = null)
    {
        $firstPointStr = implode(',', $firstPoint);
        $secondPointStr = implode(',', $secondPoint);

        $query = "{layers(bbox:[[{$firstPointStr}],[{$secondPointStr}]],model:\"InvestModel\",column:\"{$column}\"";

        if (!empty($hint)) {
            $query .= ",hint:\"{$hint}\"";
        }

        $query .= ")}";

        $response = $this->request($query);

        if (isset($response['data']['layers']['features'][0])) {
            $feature = $response['data']['layers']['features'][0];

            if (isset($feature['properties'][$column])) {
                return $feature['properties'][$column];
            }
        }

        return 0;
    }

    /**
     * @param string $query
     * @param array $additionalParams
     * @return mixed
     */
    protected function request(string $query, $additionalParams = [])
    {
        $post = [
            'query' => $query,
        ];

        if (!empty($additionalParams)) {
            $post = array_merge($post, $additionalParams);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->graphQl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'authorization: JWT '.$this->getAuthToken(),
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $out = curl_exec($ch);

        $response = json_decode($out, true);

        return $response;
    }

        /**
     * @param string $token
     */
    protected function setToken(string $token)
    {
        $this->token = trim($token);
    }

    /**
     * @return string
     */
    protected function getToken(): string
    {
        return $this->token;
    }
}

