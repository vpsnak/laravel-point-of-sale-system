<?php

namespace App\Http\Controllers;

use App\PostalCode;
use Illuminate\Http\Request;

class TimeslotController extends Controller
{
    public function getTimeslots(Request $request)
    {
        $validatedData = $request->validate([
            'postcode' => 'required|exists:postalcode,postalcode',
            'date' => 'required'
        ]);

        $result = [];
        $postcode = PostalCode::where('postalcode', $validatedData['postcode'])->first();
        if (empty($postcode) || empty($postcode->grid) || empty($postcode->grid->group) || empty($postcode->grid->group->timeslotGrid)) {
            response($result, 200);
        }
        $area_cost = $postcode->grid->group->group_price;
        foreach ($postcode->grid->group->timeslotGrid as $grid) {
            if (!$grid->timeslot->status) {
                continue;
            }
            $result[] = [
                'label' => $grid->timeslot->title,
                'cost' => $area_cost
            ];
        }
        return response($result, 200);
    }
}
