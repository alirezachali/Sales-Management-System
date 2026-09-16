<?php

return [
    // Page title
    'page_title' => 'Product Brands Management',

    // Main card header
    'manage_brands' => 'Manage Brands',
    'manage_subtitle' => 'Manage product brands information',
    'add_brand' => 'Add Brand',
    'add_tooltip' => 'Add a new Brand to the system',

    // Stats cards
    'stats' => [
        'total' => 'Total Brands',
        'active' => 'Active Brands',
        'inactive' => 'Inactive Brands',
        'not_supplier' => 'Brands Without Supplier',
    ],
    // Search
    'search_placeholder' => 'Search by Brand name',

    // Table columns
    'table' => [
        'row' => '#',
        'name' => 'Brand Name',
        'description' => 'Description',
        'suppliers' => 'Suppliers',
        'status' => 'Status',
        'actions' => 'Actions',
        'edit'   =>  'Edit',
        'delete' =>  'Delete',
    ],

    // Statuses
    'active' => 'Active',
    'inactive' => 'Inactive',
    'empty_state' => 'No Brand have been registered yet.',

    // Add/Edit modal
    'edit_modal_title' => 'Edit Brand',
    'create_modal_title' => 'Add New Brand',
    'name_label' => 'Brand Name',
    'description_label' => 'Description',
    'logo_label' => 'Path/Link Logo',
    'suppliers_label' => 'Suppliers of Brand',
    'supplier_empty' => 'No active suppliers registered',
    'supplier_roll' => 'You can select multiple suppliers at the same time',
    'cancel' => 'Cancel',
    'save_changes' => 'Save Changes',
    'save_brand' => 'Save Brand',
    'close' => 'Close',

    // Delete modal
    'delete_modal_title' => 'Delete Brand',
    'delete_confirm' => 'Are you sure you want to delete this brand? If this brand is connected to another supplier or product, deletion may be problematic.',
    'delete' => 'Delete',
    'edit_tooltip' => 'Edit Brand',
    'delete_tooltip' => 'Delete Brand',

    // Flash messages
    'messages' => [
        'created' => 'Brand created successfully.',
        'updated' => 'Brand updated successfully.',
        'deleted' => 'Brand deleted successfully.',
        'delete_blocked' => 'This Brand has products and cannot be deleted.',
    ],

    // Validation messages
    'validation' => [
        'name_required' => 'Entering a brand name is required.',
        'name_unique' => 'A brand with this name is already registered.',
    ],

];