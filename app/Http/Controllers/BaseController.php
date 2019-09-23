<?php

namespace App\Http\Controllers;

use App\BaseModel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class BaseController extends Controller
{
    /**
     * @var BaseModel
     */
    protected $model = null;

    /**
     * @return ResponseFactory|Response
     */
    public function all()
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        return response($this->model::allData(), 200);
    }

    public function get($id)
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        return response($this->model::getOne($id), 200);
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
            'keyword' => 'required|string',
            'per_page' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ]);

        $columns = Schema::getColumnListing((new $this->model)->getTable());
        return $this->searchResult($columns, $validatedData['keyword'], $validatedData['per_page'],
            $validatedData['page']);
    }

    public function searchResult($columns, $keyword, $perPage = 20, $page = 1)
    {
        if (empty($columns) || empty($keyword)) {
            return response('Did not found columns or keyword to search', 404);
        }
        $page = !empty($page) ? $page : 1;
        $perPage = !empty($perPage) ? $perPage : 20;

        $query = $this->model::query();
        foreach ($columns as $column) {
            $query->orWhere($column, 'like', "%$keyword%");
        }

        return response($query->paginate($perPage, ['*'], 'page', $page), 200);
    }

    public function delete($id)
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        $deleted = $this->model::deleteData($id);
        return response($deleted, 200);
    }
}
