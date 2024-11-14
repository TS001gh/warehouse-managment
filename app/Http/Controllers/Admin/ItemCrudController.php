<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

/**
 * Class ItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ItemCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Item::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/item');
        CRUD::setEntityNameStrings(trans("backpack::forms.item"), trans("backpack::forms.items"));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::addClause('withoutGlobalScopes');

        // CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('name')->label(trans('backpack::forms.item_name'));
        CRUD::column('code')->label(trans('backpack::forms.item_code'));
        CRUD::column('current_quantity')->label(trans('backpack::forms.current_quantity'));
        CRUD::column('image')
            ->label(trans('backpack::forms.image'))
            ->type('image')
            ->height('50px')
            ->width('50px')
            ->prefix('storage/');

        CRUD::column('price')->label(trans('backpack::forms.price'))->suffix('$');

        CRUD::column('is_active')->label(trans('backpack::forms.is_active'))->type('boolean')->wrapper(["element" => "span", "class" => "status-cell"]);


        CRUD::column('group_id')
            ->type('select')
            ->label(trans('backpack::forms.group_id'))
            ->entity('group')
            ->attribute('name')
            ->model('App\Models\Group');


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
        CRUD::setValidation(ItemRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('name')
            ->type('text')
            ->label(trans('backpack::forms.item_name'));

        CRUD::field('code')
            ->type('text')
            ->label(trans('backpack::forms.item_code'));

        CRUD::field('min_quantity')
            ->type('number')
            ->label(trans('backpack::forms.min_quantity'));

        CRUD::field('current_quantity')
            ->type('number')
            ->label(trans('backpack::forms.current_quantity'));

        CRUD::field('price')
            ->type('number')
            ->label(trans('backpack::forms.price'))
            ->prefix('$');

        CRUD::field('image')
            ->type('upload')
            ->label(trans('backpack::forms.image'))
            ->upload(true)
            ->disk('public')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('is_active')
            ->type('checkbox')
            ->label(trans('backpack::forms.is_active'));

        CRUD::field('group_id')
            ->type('select')
            ->label(trans('backpack::forms.group_id'))
            ->entity('group')
            ->attribute('name')
            ->model('App\Models\Group');



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

        $item = Item::withoutGlobalScope('active')->find($request->id);

        if ($item) {
            $item->is_active = !$item->is_active;
            $item->save();


            return response()->json([
                'success' => true,
                'is_active' => $item->is_active,
                'message' => $item->is_active ? 'Item activated' : 'Item deactivated',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item not found'], 404);
    }
}