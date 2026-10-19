<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder {
    public function run(): void {
        $roles = ['superadmin', 'admin', 'sales', 'finance', 'warehouse', 'customer'];
        foreach ($roles as $r) Role::firstOrCreate(['name' => $r]);

        $permissions = [
            'customer.view', 'customer.create', 'customer.edit', 'customer.delete',
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'lead.view', 'lead.create', 'lead.edit',
            'order.view', 'order.create', 'order.approve', 'order.cancel',
            'payment.view', 'payment.verify',
            'invoice.view', 'invoice.generate',
            'shipment.view', 'shipment.create', 'shipment.update',
            'return.view', 'return.approve',
            'loyalty.view', 'loyalty.manage',
            'report.view',
            'setting.manage',
            'user.manage',
        ];
        foreach ($permissions as $p) Permission::firstOrCreate(['name' => $p]);

        Role::findByName('superadmin')->givePermissionTo(Permission::all());
        Role::findByName('admin')->givePermissionTo(Permission::all());
        Role::findByName('sales')->givePermissionTo([
            'customer.view','customer.create','customer.edit',
            'product.view','lead.view','lead.create','lead.edit',
            'order.view','order.create','invoice.view','report.view'
        ]);
        Role::findByName('finance')->givePermissionTo([
            'order.view','order.approve','payment.view','payment.verify',
            'invoice.view','invoice.generate','return.view','return.approve','report.view'
        ]);
        Role::findByName('warehouse')->givePermissionTo([
            'product.view','order.view','shipment.view','shipment.create','shipment.update','return.view'
        ]);
        Role::findByName('customer')->givePermissionTo([
            'product.view','order.view','order.create','invoice.view'
        ]);
    }
}