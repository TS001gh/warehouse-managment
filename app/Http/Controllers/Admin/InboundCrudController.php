<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InboundRequest;
use App\Jobs\AdjustStockJobs\AdjustInboundStockJob;
use App\Models\Inbound;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InboundCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InboundCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {
        destroy as traitDestroy;
    }
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Inbound::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inbound');
        CRUD::setEntityNameStrings(trans("backpack::forms.inbound"), trans("backpack::forms.inbounds"));
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
        CRUD::column('item_id')
            ->type('select')
            ->label(trans('backpack::forms.item_id'))
            ->entity('item')
            ->attribute('name')
            ->model('App\Models\Item');

        CRUD::column('quantity')
            ->label(trans('backpack::forms.quantity'));

        CRUD::column('date')
            ->label(trans('backpack::forms.date'));

        CRUD::column('supplier_id')
            ->type('select')
            ->label(trans('backpack::forms.supplier_id'))
            ->entity('supplier')
            ->attribute('name')
            ->model('App\Models\Supplier');
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
        CRUD::setValidation(InboundRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('item_id')
            ->type('select')
            ->label(trans('backpack::forms.item_id'))
            ->entity('item')
            ->attribute('name')
            ->model('App\Models\Item');

        CRUD::field('quantity')
            ->type('number')
            ->label(trans('backpack::forms.quantity'));

        CRUD::field('date')
            ->type('date')
            ->label(trans('backpack::forms.date'));

        CRUD::field('supplier_id')
            ->type('select')
            ->label(trans('backpack::forms.supplier_id'))
            ->entity('supplier')
            ->attribute('name')
            ->model('App\Models\Supplier');
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


    public function store()
    {
        $response = $this->traitStore();

        // Dispatch the stock adjustment job after creating the Inbound record
        $inbound = $this->crud->getCurrentEntry();

        AdjustInboundStockJob::dispatch($inbound, 'create');

        return $response;
    }

    public function update()
    {
        // Retrieve original quantity before update
        $oldQuantity = $this->crud->getCurrentEntry()->quantity;

        $response = $this->traitUpdate();
        $inbound = $this->crud->getCurrentEntry();

        // Dispatch the stock adjustment job with old quantity for correct adjustment
        AdjustInboundStockJob::dispatch($inbound, 'update', $oldQuantity);

        return $response;
    }

    public function destroy($id)
    {
        $inbound = Inbound::findOrFail($id);

        // Dispatch the stock adjustment job before deleting the Inbound record
        AdjustInboundStockJob::dispatchSync($inbound, 'delete');

        $response = $this->crud->delete($id);

        return $response;
    }
}
