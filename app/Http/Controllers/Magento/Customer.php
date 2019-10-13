<?php


namespace App\Http\Controllers\Magento;


use App\Magento\MagentoClient;

class Customer extends MagentoClient
{

    public function getall()
    {
        return $this->get('customers?limit=100', []);
    }

    public function getAllEntries($per_page = 20, $page = 1)
    {
        return $this->get("/w2/customers/list?limit=$per_page&page=$page", []);
    }

    public function getAllAddresses($customer_id)
    {

    }

    /**
     * "2": {
     * "entity_id": "2",
     * "website_id": "1",
     * "email": "test@example.com",
     * "group_id": "1",
     * "created_at": "2012-03-22 14:15:54",
     * "disable_auto_group_change": "1",
     * "firstname": "john",
     * "lastname": "Doe",
     * "created_in": "Admin",
     * "prefix": null,
     * "suffix": null,
     * "taxvat": null,
     * "dob": "2001-01-03 00:00:00",
     * "reward_update_notification": "1",
     * "reward_warning_notification": "1",
     * "gender": "1"
     * },
     */
    public function sendCustomer($data)
    {
        return $this->put('w2/customers', $data);
    }

    /**
     * "entity_id": "4",
     * "firstname": "asd",
     * "lastname": "lol",
     * "company": "asdasdas",
     * "city": "asdasdasd",
     * "country_id": "GR",
     * "region": "region",
     * "postcode": "15122",
     * "vat_id": "651445851451",
     * "street": [
     * "sadasdads"
     * ],
     * "is_default_billing": 1,
     * "is_default_shipping": 0
     */
    public function sendAddress($customer_id, $data)
    {
        return $this->put("w2/customers/$customer_id/addresses", $data);
    }

    public function getByField($field, $value)
    {
        $params = "filter[1][attribute]=$field&filter[1][eq]=$value";
        $params = [
            'filter' => [
                'attribute' => $field,
                'eq' => $value
            ]
        ];
        return $this->request('GET', 'customers', $params);
    }
}
