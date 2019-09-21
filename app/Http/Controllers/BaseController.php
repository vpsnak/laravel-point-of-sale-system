<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    /**
     * @var \App\BaseModel
     */
    protected $model = null;

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function all()
    {
        if (!isset($this->model)) {
            return response($this->model::with(['stores', 'price'])->get(), 404);
        }

        return response($this->model::allData(), 200);
    }
}
