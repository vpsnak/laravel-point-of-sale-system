<?php


namespace App\Http\Controllers\Magento;


use App\Magento\MagentoClient;

class Product extends MagentoClient
{
    public function getall()
    {
        return $this->get('products?limit=100', []);
    }

    public function getAllEntries($per_page = 20, $page = 1)
    {
        return $this->get("/w2/products/list?limit=$per_page&page=$page", []);
    }

    public function getById($magento_id)
    {
        return $this->get('products/' . $magento_id, []);
    }
}
