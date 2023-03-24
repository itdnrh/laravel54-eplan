<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\PlanAdjustment;
use App\Models\PlanType;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;

class PlanAdjustmentController extends Controller
{
    public function update()
    {
        try {
            /** Get old data of found plan */
            $oldPlan = PlanItem::with('plan')->where('plan_id', $id)->first();

            /** Update found plan_items table */
            $plan = PlanItem::where('plan_id', $id)->first();
            // $plan->price_per_unit   = $req['price_per_unit'];
            // $plan->unit_id          = $req['unit_id'];
            // $plan->amount           = $req['amount'];
            // $plan->sum_price        = $req['sum_price'];
            // $plan->remain_amount    = $req['amount'];
            // $plan->remain_budget    = $req['sum_price'];

            // if($plan->save()) {
                /** Update is_adjust field of found plans table */
                Plan::find($id)->update(['is_adjust' => 1]);

                /** Create new plan adjustment data */
                $adjustment = new PlanAdjustment;
                $adjustment->plan_id            = $req['plan_id'];
                $adjustment->adjust_type        = $req['adjust_type'];
                $adjustment->in_plan            = $oldPlan->plan->in_plan;
                $adjustment->old_price_per_unit = $oldPlan->price_per_unit;
                $adjustment->old_unit_id        = $oldPlan->unit_id;
                $adjustment->old_amount         = $oldPlan->amount;
                $adjustment->old_sum_price      = $oldPlan->sum_price;
                $adjustment->remark             = $req['remark'];
                $adjustment->save();

                return [
                    'status'    => 1,
                    'message'   => 'Adjust plan data successfully!!',
                    'plan'      => $plan
                ];
            // } else {
            //     return [
            //         'status'    => 0,
            //         'message'   => 'Something went wrong!!'
            //     ];
            // }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function delete()
    {
        try {
            /** Get old data of found plan */
            $oldPlan = PlanItem::with('plan')->where('plan_id', $id)->first();

            /** Update found plan_items table */
            $plan = PlanItem::where('plan_id', $id)->first();
            // $plan->price_per_unit   = $req['price_per_unit'];
            // $plan->unit_id          = $req['unit_id'];
            // $plan->amount           = $req['amount'];
            // $plan->sum_price        = $req['sum_price'];
            // $plan->remain_amount    = $req['amount'];
            // $plan->remain_budget    = $req['sum_price'];

            // if($plan->save()) {
                /** Update is_adjust field of found plans table */
                Plan::find($id)->update(['is_adjust' => 1]);

                /** Create new plan adjustment data */
                $adjustment = new PlanAdjustment;
                $adjustment->plan_id            = $req['plan_id'];
                $adjustment->adjust_type        = $req['adjust_type'];
                $adjustment->in_plan            = $oldPlan->plan->in_plan;
                $adjustment->old_price_per_unit = $oldPlan->price_per_unit;
                $adjustment->old_unit_id        = $oldPlan->unit_id;
                $adjustment->old_amount         = $oldPlan->amount;
                $adjustment->old_sum_price      = $oldPlan->sum_price;
                $adjustment->remark             = $req['remark'];
                $adjustment->save();

                return [
                    'status'    => 1,
                    'message'   => 'Adjust plan data successfully!!',
                    'plan'      => $plan
                ];
            // } else {
            //     return [
            //         'status'    => 0,
            //         'message'   => 'Something went wrong!!'
            //     ];
            // }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }
}
