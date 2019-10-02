<?php


namespace App\Http\Controllers\Magento;


use App\Magento\MagentoClient;

class Customer extends MagentoClient
{
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
    public function create($data)
    {
        return $this->post('customers', $data);
    }

    public function getall()
    {
        return $this->get('customers?limit=100', []);
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
        return $this->request2('GET', 'customers', $params);
    }
}
