<?php

namespace App\Http\Controllers;

use App\PostalCode;
use Carbon\Carbon;
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
            return response($result, 200);
        }
        $area_cost = $postcode->grid->group->group_price;
        $date = Carbon::parse($validatedData['date']);
        foreach ($postcode->grid->group->timeslotGrid as $grid) {
            $timeslot = $grid->timeslot;
            if (!empty($slot = $this->filterTimeSlot($date, $area_cost, $timeslot))) {
                $result[] = $slot;
            }
            // $result['labels'][] = $grid->timeslot->title;
            // $result['cost'][] = $area_cost;
        }
        return response($result, 200);
    }

    private function filterTimeSlot(Carbon $date, $group_cost, $timeslot)
    {
        if (!$timeslot->status) {
            return [];
        }
        $dateFees = [
            [
                'enabled' => $timeslot->mon,
                'extra_cost' => $timeslot->mon_extra_fee
            ],
            [
                'enabled' => $timeslot->tue,
                'extra_cost' => $timeslot->tue_extra_fee
            ],
            [
                'enabled' => $timeslot->wed,
                'extra_cost' => $timeslot->wed_extra_fee
            ],
            [
                'enabled' => $timeslot->thu,
                'extra_cost' => $timeslot->thu_extra_fee
            ],
            [
                'enabled' => $timeslot->fri,
                'extra_cost' => $timeslot->fri_extra_fee
            ],
            [
                'enabled' => $timeslot->sat,
                'extra_cost' => $timeslot->sat_extra_fee
            ],
            [
                'enabled' => $timeslot->sun,
                'extra_cost' => $timeslot->sun_extra_fee
            ],
        ];

        $dateFeeEntry = $dateFees[$date->dayOfWeek];
        if (!$dateFeeEntry['enabled']) {
            return [];
        }

        return [
            'label' => $timeslot->interval,
            'cost' => $group_cost + $dateFeeEntry['extra_cost']
        ];
    }
}
