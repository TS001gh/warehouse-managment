{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('Groups')) }}" icon="la la-users"
    :link="backpack_url('group')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('Items')) }}" icon="la la-box" :link="backpack_url('item')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('Outbounds')) }}" icon="la la-arrow-right"
    :link="backpack_url('outbound')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('Inbounds')) }}" icon="la la-arrow-left"
    :link="backpack_url('inbound')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('Customers')) }}" icon="la la-user-friends"
    :link="backpack_url('customer')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('Suppliers')) }}" icon="la la-truck"
    :link="backpack_url('supplier')" />

<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('permissions')) }}" icon="la la-key"
    :link="backpack_url('permission')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('roles')) }}" icon="la la-users"
    :link="backpack_url('role')" />
<x-backpack::menu-item title="{{ trans('backpack::forms.' . Str::lower('users')) }}" icon="la la-user"
    :link="backpack_url('user')" />
