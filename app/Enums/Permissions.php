<?php

namespace App\Enums;

enum Permissions: string
{
        // إدارة المواد
    case VIEW_ITEMS = 'view_items';
    case ADD_ITEM = 'add_item';
    case EDIT_ITEM = 'edit_item';
    case DELETE_ITEM = 'delete_item';
    case ACTIVATE_ITEM = 'activate_item';
    case DEACTIVATE_ITEM = 'deactivate_item';

        // إدارة الصادر
    case VIEW_OUTBOUNDS = 'view_outbounds';
    case ADD_OUTBOUND = 'add_outbound';
    case EDIT_OUTBOUND = 'edit_outbound';
    case DELETE_OUTBOUND = 'delete_outbound';

        // إدارة الوارد
    case VIEW_INBOUNDS = 'view_inbounds';
    case ADD_INBOUND = 'add_inbound';
    case EDIT_INBOUND = 'edit_inbound';
    case DELETE_INBOUND = 'delete_inbound';

        // إدارة الزبائن
    case VIEW_CUSTOMERS = 'view_customers';
    case ADD_CUSTOMER = 'add_customer';
    case EDIT_CUSTOMER = 'edit_customer';
    case DELETE_CUSTOMER = 'delete_customer';
    case BLOCK_CUSTOMER = 'block_customer';

        // إدارة الموردين
    case VIEW_SUPPLIERS = 'view_suppliers';
    case ADD_SUPPLIER = 'add_supplier';
    case EDIT_SUPPLIER = 'edit_supplier';
    case DELETE_SUPPLIER = 'delete_supplier';
    case BLOCK_SUPPLIER = 'block_supplier';

        // التقارير
    case VIEW_REPORTS = 'view_reports';

        // ادارة المستخدمين
    case VIEW_USERS = 'view_users';
    case CREATE_USERS = 'create_users';
    case EDIT_USERS = 'edit_users';
    case DELETE_USERS = 'delete_users';

        // ادارة الوظائف
    case VIEW_ROLES = 'view_roles';
    case CREATE_ROLES = 'create_roles';
    case EDIT_ROLES = 'edit_roles';
    case DELETE_ROLES = 'delete_roles';

        // ادارة الصلاحيات
    case VIEW_PERMISSIONS = 'view_permissions';
    case CREATE_PERMISSIONS = 'create_permissions';
    case EDIT_PERMISSIONS = 'edit_permissions';
    case DELETE_PERMISSIONS = 'delete_permissions';
}
