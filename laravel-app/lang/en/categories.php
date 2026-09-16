<?php

return [
    // Page title
    'page_title' => 'Product Categories Management',

    // Main card header
    'manage_categories' => 'Manage Categories',
    'manage_subtitle' => 'Manage product categories information',
    'add_category' => 'Add Category',
    'add_tooltip' => 'Add a new category to the system',

    // Stats cards
    'stats' => [
        'total' => 'Total Categories',
        'active' => 'Active Categories',
        'inactive' => 'Inactive Categories',
        'empty' => 'Categories Without Products',
    ],

    // Search
    'search_placeholder' => 'Search by category name',

    // Table columns
    'table' => [
        'row' => '#',
        'name' => 'Category Name',
        'description' => 'Description',
        'products_count' => 'Products',
        'created_at' => 'Created At',
        'status' => 'Status',
        'actions' => 'Actions',
    ],

    // Statuses
    'active' => 'Active',
    'inactive' => 'Inactive',
    'empty_state' => 'No categories have been registered yet.',

    // Add/Edit modal
    'edit_modal_title' => 'Edit Category',
    'create_modal_title' => 'Add New Category',
    'name_label' => 'Category Name',
    'description_label' => 'Description',
    'cancel' => 'Cancel',
    'save_changes' => 'Save Changes',
    'save_category' => 'Save Category',
    'close' => 'Close',

    // Delete modal
    'delete_modal_title' => 'Delete Category',
    'delete_confirm' => 'Are you sure you want to delete the category :name? This action is irreversible.',
    'delete' => 'Delete',
    'edit_tooltip' => 'Edit category',
    'delete_tooltip' => 'Delete category',

    // Flash messages
    'messages' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'deleted' => 'Category deleted successfully.',
        'delete_blocked' => 'This category has products and cannot be deleted.',
    ],

    // Validation messages
    'validation' => [
        'name_required' => 'The category name is required.',
        'name_unique' => 'This category name has already been taken.',
        'name_max' => 'The category name may not be greater than 100 characters.',
    ],
];
