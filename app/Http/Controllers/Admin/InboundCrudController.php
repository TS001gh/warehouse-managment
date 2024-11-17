<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InboundRequest;
use App\Jobs\AdjustStockJobs\AdjustInboundStockJob;
use App\Models\Inbound;
use App\Models\Item;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\BulkDeleteOperation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Prologue\Alerts\Facades\Alert;

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

    use BulkDeleteOperation;
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
        $this->crud->addClause('whereHas', 'item', function ($query) {
            $query->active();
        });
        $this->crud->addClause('whereHas', 'supplier', function ($query) {
            $query->active();
        });
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
        $this->crud->hasAccessOrFail('create');

        DB::beginTransaction();

        try {
            $request = $this->crud->validateRequest();
            $this->crud->registerFieldEvents();
            $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
            $this->data['entry'] = $this->crud->entry = $item;

            AdjustInboundStockJob::dispatchSync($item, 'create');
            DB::commit();

            Alert::success('تمت إضافة العنصر وتعديل المخزون بنجاح.')->flash();

            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($item->getKey());
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to store item and adjust stock: ' . $e->getMessage());
            Alert::error('فشل تخزين العنصر وتعديل المخزون. يرجى المحاولة مرة أخرى.')->flash();

            return redirect()->back()->withInput();
        }
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // Retrieve original quantity before update
        $oldQuantity = $this->crud->getCurrentEntry()->quantity;

        DB::beginTransaction();

        try {
            $request = $this->crud->validateRequest();
            $this->crud->registerFieldEvents();
            $item = $this->crud->update($this->crud->getCurrentEntryId(), $this->crud->getStrippedSaveRequest($request));
            $this->data['entry'] = $this->crud->entry = $item;

            // Dispatch the stock adjustment job with old quantity for correct adjustment
            AdjustInboundStockJob::dispatchSync($item, 'update', $oldQuantity);
            DB::commit();

            Alert::success('تم تعديل العنصر وتحديث المخزون بنجاح.')->flash();

            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($item->getKey());
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update item and adjust stock: ' . $e->getMessage());
            Alert::error('فشل تعديل العنصر وتحديث المخزون. يرجى المحاولة مرة أخرى.')->flash();

            return redirect()->back()->withInput();
        }
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
