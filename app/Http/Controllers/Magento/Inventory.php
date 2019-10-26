<?php


namespace App\Http\Controllers\Magento;


use App\Magento\MagentoClient;

class Inventory extends MagentoClient
{
    public function updateStockData($data)
    {
        return $this->put('stockitems/' . $data['item_id'], $data);
    }

    public function getStockData($item_id)
    {
        return $this->get('stockitems/' . $item_id, []);
    }

    public function getStock()
    {
        return $this->get('stockitems', []);
    }
}
