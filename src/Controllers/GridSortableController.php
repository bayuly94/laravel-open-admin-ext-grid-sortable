<?php

namespace OpenAdminCore\Admin\GridSortable\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GridSortableController extends Controller
{
    public function sort(Request $request)
    {
        $sorts = collect($request->get('_sort'));

        /**
         * Result contoh:
         * [
         *   15 => 0,
         *   9  => 1,
         *   3  => 2,
         * ]
         */
        $orders = $sorts
            ->pluck('key')
            ->values();

        $status = true;
        $message = trans('admin.save_succeeded');
        $modelClass = $request->get('_model');

        try {
            foreach ($orders as $index => $id) {
                /** @var \Illuminate\Database\Eloquent\Model $model */
                $model = $modelClass::find($id);

                if (!$model) {
                    continue;
                }

                $column = data_get($model->sortable, 'order_column_name', 'order_column');

                $model->{$column} = $index;
                $model->save();
            }
        } catch (Exception $exception) {
            $status = false;
            $message = $exception->getMessage();
        }

        return response()->json(compact('status', 'message'));
    }
}