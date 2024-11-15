<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OutboundRequest;
use App\Jobs\AdjustStockJobs\AdjustOutboundStockJob;
use App\Models\Item;
use App\Models\Outbound;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

/**
 * Class OutboundCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OutboundCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Outbound::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/outbound');
        CRUD::setEntityNameStrings(trans("backpack::forms.outbound"), trans("backpack::forms.outbounds"));
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
        CRUD::column('item_id')->label(trans('backpack::forms.item_id'))
            ->type('select')
            ->entity('item')
            ->attribute('name')
            ->model('App\Models\Item');

        CRUD::column('quantity')->label(trans('backpack::forms.quantity'));
        CRUD::column('date')->label(trans('backpack::forms.date'));

        CRUD::column('customer_id')->label(trans('backpack::forms.customer_id'))
            ->type('select')
            ->entity('customer')
            ->attribute('name')
            ->model('App\Models\Customer');
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
        CRUD::setValidation(OutboundRequest::class);
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

        CRUD::field('customer_id')
            ->type('select')
            ->label(trans('backpack::forms.customer_id'))
            ->entity('customer')
            ->attribute('name')
            ->model('App\Models\Customer');
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
            $newQuantity = request()->input('quantity');

            if (!$this->checkStock($newQuantity, true)) {
                return back()->withInput();
            }

            $request = $this->crud->validateRequest();
            $this->crud->registerFieldEvents();
            $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
            $this->data['entry'] = $this->crud->entry = $item;

            AdjustOutboundStockJob::dispatchSync($item, 'create');
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

        $newQuantity = request()->input('quantity');

        if (!$this->checkStock($newQuantity)) {
            return back()->withInput();
        }

        $oldQuantity = $this->crud->getCurrentEntry()->quantity;

        DB::beginTransaction();

        try {
            $request = $this->crud->validateRequest();
            $this->crud->registerFieldEvents();
            $item = $this->crud->update($this->crud->getCurrentEntryId(), $this->crud->getStrippedSaveRequest($request));
            $this->data['entry'] = $this->crud->entry = $item;

            AdjustOutboundStockJob::dispatchSync($item, 'update', $oldQuantity);
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
        $outbound = Outbound::findOrFail($id);

        // Dispatch the stock adjustment job before deleting the Outbound record
        AdjustOutboundStockJob::dispatchSync($outbound, 'delete');

        $response = $this->crud->delete($id);

        return $response;
    }


    protected function checkStock($newQuantity, $isNew = false)
    {
        $itemId = request()->input('item_id');
        $item = Item::find($itemId);

        if ($item && $newQuantity) {
            if ($isNew) {
                if ($item->current_quantity < $newQuantity) {
                    Alert::error(
                        "المخزون غير كافٍ لهذا العنصر. الكمية المتوفرة هي {$item->current_quantity} فقط."
                    )->flash();
                    return false;
                }
            } else {
                $outboundId = request()->input('id');
                $outbound = Outbound::find($outboundId);
                if ($outbound) {
                    $originalQuantity = $outbound->quantity;
                    $adjustment = $newQuantity - $originalQuantity;

                    if ($adjustment > 0 && $item->current_quantity < $adjustment) {
                        Alert::error(
                            "المخزون غير كافٍ لهذا العنصر. الكمية المتوفرة هي {$item->current_quantity} فقط."
                        )->flash();
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
