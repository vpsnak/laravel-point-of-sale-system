<?php

class CRUDController implements CRUDInterface
{

    public function create($model, $data)
    {
        if ($model->insert($data)) {
            return response('ok', 201);
        }
        return response('error', 500);
    }

    public function get($model, $identifier, $column = 'id')
    {
        if ($result = $model::where($column, $identifier)->get()) {
            return response($result, 200);
        }
        return response('not found', 404);
    }

    public function update($model, $data)
    {
        if ($model->update($data)) {
            return response('ok', 200);
        }
        return response('error', 500);
    }

    public function restore($model, $identifier, $column = 'id')
    {
        if ($model::withTrashed()->where($column, $identifier)->restore()) {
            return response('ok', 200);
        }
        return response('error', 500);
    }

    public function delete($model)
    {
        if ($model->delete()) {
            return response('ok', 200);
        }
        return response('error', 500);
    }

    public function destroy($model, $identifier, $column = 'id')
    {
        if ($model::where($column, $identifier)->forceDelete()) {
            return response('ok', 200);
        }
        return response('error', 500);
    }
}
