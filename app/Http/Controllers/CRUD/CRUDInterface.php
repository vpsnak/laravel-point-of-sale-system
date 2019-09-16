<?php

interface CRUDInterface
{

    public function create($model, $data);

    public function get($model, $identifier, $column = 'id');

    public function update($model, $data);

    public function restore($model, $identifier, $column = 'id');

    public function delete($model);

    public function destroy($model, $identifier, $column = 'id');
}
