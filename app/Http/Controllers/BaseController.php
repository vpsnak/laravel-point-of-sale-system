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

        $validatedID = $request->validate([
            'id' => 'nullable|exists:' . (new $this->model)->getTable() . ',id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
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
        return $this->searchResult(
            $columns,
            $validatedData['keyword']
        );
    }

    public function searchResult($columns, $keyword, $pagination = false)
    {
        if (empty($columns) || empty($keyword)) {
            return response('Did not found columns or keyword to search', 404);
        }

        $query = $this->model::query();
        foreach ($columns as $column) {
            $query->orWhere($column, 'like', "%$keyword%");
        }

        if ($pagination) {
            return response($query->paginate(), 200);
        } else {
            return response($query->get(), 200);
        }
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
