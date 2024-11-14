<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;

/**
 * Class CustomerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomerCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Customer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/customer');
        CRUD::setEntityNameStrings(trans("backpack::forms.customer"), trans("backpack::forms.customers"));
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
            ->label(trans('backpack::forms.customer_name'));

        CRUD::column('phone')
            ->label(trans('backpack::forms.phone'));

        CRUD::column('email')
            ->label(trans('backpack::forms.email'));

        CRUD::column('is_active')->label(trans('backpack::forms.is_active'))->type('boolean')->wrapper(["element" => "span", "class" => "status-cell"]);


        CRUD::addButtonFromModelFunction('line', 'toggleActive', 'toggleActiveButton', 'end');

        Widget::add()->type('script')->content('assets/js/toggleButton.js');


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
        CRUD::setValidation(CustomerRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('name')
            ->type('text')
            ->label(trans('backpack::forms.customer_name'));

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
        $customer = Customer::withoutGlobalScope('active')->find($request->id);
        if ($customer) {
            $customer->is_active = !$customer->is_active;
            $customer->save();


            return response()->json([
                'success' => true,
                'is_active' => $customer->is_active,
                'message' => $customer->is_active ? 'Customer activated' : 'Customer deactivated',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
    }



    // ================================= api actions ==========================================
    public function getOutbounds($id)
    {
        $customer = Customer::query()->withoutGlobalScope('active')->findOrFail($id);

        // Get outbound transactions for the customer and calculate total balance
        $outbounds = $customer->outbounds()->with(['item' => function ($query) {
            $query->withoutGlobalScope('active');
        }])->get();

        $totalBalance = $outbounds->sum(function ($outbound) {
            return $outbound->quantity * $outbound->item->price;
        });

        return response()->json([
            'outbounds' => $outbounds,
            'total_balance' => $totalBalance,
        ]);
    }
}
