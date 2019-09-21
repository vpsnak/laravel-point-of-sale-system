<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
            return response('Model not found', 404);
        }

        return response($this->model::allData(), 200);
    }

    public function create(Request $request)
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        $columns = Schema::getColumnListing((new $this->model)->getTable());
        $fieldsToValidate = [];
        foreach ($columns as $column) {
            $fieldsToValidate[$column] = 'required';
        }
        $validatedData = $request->validate($fieldsToValidate);

        return response($this->model::store($validatedData), 201);
    }

    public function search(Request $request)
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = Schema::getColumnListing((new $this->model)->getTable());
        return $this->searchResult($columns, $validatedData['keyword']);
    }

    public function searchResult($columns, $keyword)
    {
        if (empty($columns) || empty($keyword)) {
            return response('Did not found columns or keyword to search', 404);
        }

        $query = $this->model::query();
        foreach ($columns as $column) {
            $query->orWhere($column, 'like', "%$keyword%");
        }

        return response(
            $query->get(),
            200
        );
    }
}
