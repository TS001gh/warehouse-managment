<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

/**
 * Class SupplierCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SupplierCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Supplier::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/supplier');
        CRUD::setEntityNameStrings(trans("backpack::forms.supplier"), trans("backpack::forms.suppliers"));
        // $this->crud->query->active();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.
        CRUD::addClause('withoutGlobalScopes');

        CRUD::column('name')
            ->label(trans('backpack::forms.supplier_name'));

        CRUD::column('phone')
            ->label(trans('backpack::forms.phone'));

        CRUD::column('email')
            ->label(trans('backpack::forms.email'));

        CRUD::column('is_active')->label(trans('backpack::forms.is_active'))->type('boolean')->wrapper(["element" => "span", "class" => "status-cell"]);

        CRUD::addButtonFromView('line', 'toggleActive', 'toggleActive', 'after');


        // With Ajax
        // CRUD::addButtonFromModelFunction('line', 'toggleActive', 'toggleActiveButton', 'end');
        // Widget::add()->type('script')->content('assets/js/toggleButton.js');

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SupplierRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('name')
            ->type('text')
            ->label(trans('backpack::forms.supplier_name'));

        CRUD::field('phone')
            ->type('text')
            ->label(trans('backpack::forms.phone'));

        CRUD::field('email')
            ->type('email')
            ->label(trans('backpack::forms.email'));
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public function toggleActive(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);

        if ($supplier) {
            $supplier->is_active = !$supplier->is_active;
            $supplier->save();

            Alert::success($supplier->is_active ? 'تم تفعيل المورد بنجاح' : 'تم تعطيل المورد بنجاح')->flash();
        } else {
            Alert::error('لم نستطع ايجاد المورد')->flash();
        }

        return redirect()->back();
    }


    // With Ajax
    // public function toggleActive(Request $request)
    // {
    //     $supplier = Supplier::withoutGlobalScope('active')->find($request->id);
    //     if ($supplier) {
    //         $supplier->is_active = !$supplier->is_active;
    //         $supplier->save();
    //         return response()->json([
    //             'success' => true,
    //             'is_active' => $supplier->is_active,
    //             'message' => $supplier->is_active ? 'Supplier activated' : 'Supplier deactivated',
    //         ]);
    //     }

    //     return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
    // }

    // ================================== api actions =====================================
    public function getInbounds($id)
    {

        try {
            $supplier = Supplier::query()->findOrFail($id);

            // Get outbound transactions for the customer and calculate total balance by applying the scope for items
            // $inbounds = $customer->inbounds()
            //     ->with(['item' => function ($query) {
            //         $query->active(); // Apply the 'active' scope to 'item'
            //     }])
            //     ->get();

            // Without
            $inbounds = $supplier->inbounds()->with('item')->get();


            $totalBalance = $inbounds->sum(function ($inbound) {
                return $inbound->item ? $inbound->quantity * $inbound->item->price : 0;
            });

            return response()->json([
                'inbounds' => $inbounds,
                'total_balance' => $totalBalance,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'status' => 400,
            ]);
        }
    }
}


// ======================================== Notes ====================================

// الدالة whereHas في Eloquent تُستخدم لتصفية الاستعلامات بناءً على وجود علاقة مرتبطة، وأيضًا لتحديد شروط معينة على هذه العلاقة
// $inbounds = $supplier->inbounds()->whereHas('item', function ($query) {
//     $query->where('is_active', true);
// })->with('item')->get();
