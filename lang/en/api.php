<?php

return [
    // General messages
    'success' => 'Operation completed successfully',
    'error' => 'An error occurred',
    'ok' => 'OK',
    'failed' => 'Operation failed',
    'validation_failed' => 'Validation failed',

    // Authentication
    'login_success' => 'Login successful',
    'login_failed' => 'Login failed',
    'logout_success' => 'Logout successful',
    'register_success' => 'Registration successful',
    'email_exists' => 'Email already exists',

    // Authorization
    'insufficient_permission' => 'Insufficient permission',
    'admin_required' => 'Admin permission required',
    'access_denied' => 'Access denied',
    'not_authorized' => 'Not authorized to perform this action',
    'owner_required' => 'Only the owner can perform this action',

    // User management
    'user_not_found' => 'User not found',
    'user_update_success' => 'User updated successfully',
    'profile_update_success' => 'Profile updated successfully',
    'no_need_set_admin_permission' => 'No need to set permission for admin',

    // Group management
    'group_created' => 'Group created successfully',
    'group_updated' => 'Group updated successfully',
    'group_deleted' => 'Group deleted successfully',
    'group_not_found' => 'Group not found',
    'cannot_delete_group_with_profiles' => 'Cannot delete group that has associated profiles',

    // Profile management
    'profile_created' => 'Profile created successfully',
    'profile_updated' => 'Profile updated successfully',
    'profile_deleted' => 'Profile deleted successfully',
    'profile_restored' => 'Profile restored successfully',
    'profile_not_found' => 'Profile not found',
    'profile_not_found_in_trash' => 'Profile not found in trash',
    'profile_start_using_success' => 'Started using profile successfully',
    'profile_stop_using_success' => 'Stopped using profile successfully',
    'profile_in_use_by_others' => 'Profile is being used by someone else',
    'profile_not_current_user' => 'You are not the current user of this profile',
    'insufficient_permission_profile' => 'Insufficient permission for this profile',
    'insufficient_permission_profile_edit' => 'Insufficient permission to edit profile',
    'insufficient_permission_profile_delete' => 'Insufficient permission to delete profile',
    'insufficient_permission_profile_restore' => 'Insufficient permission to restore profile',
    'insufficient_permission_profile_status' => 'Insufficient permission to update profile status',
    'insufficient_permission_profile_tags' => 'Insufficient permission to add tags to profile',
    'insufficient_permission_profile_remove_tags' => 'Insufficient permission to remove tags from profile',

    // Proxy management
    'proxy_created' => 'Proxy created successfully',
    'proxy_updated' => 'Proxy updated successfully',
    'proxy_deleted' => 'Proxy deleted successfully',
    'proxy_not_found' => 'Proxy not found',
    'proxy_status_updated' => 'Proxy status updated successfully',
    'proxy_connection_success' => 'Proxy is working normally',
    'proxy_connection_failed' => 'Proxy cannot connect',
    'proxy_connection_error' => 'Cannot connect to proxy',
    'insufficient_permission_proxy' => 'Insufficient permission for this proxy',
    'insufficient_permission_proxy_edit' => 'Insufficient permission to edit proxy',
    'insufficient_permission_proxy_delete' => 'Insufficient permission to delete proxy',
    'insufficient_permission_proxy_status' => 'Insufficient permission to change proxy status',
    'insufficient_permission_proxy_test' => 'Insufficient permission to test proxy',
    'insufficient_permission_proxy_tags' => 'Insufficient permission to add tags to proxy',
    'insufficient_permission_proxy_remove_tags' => 'Insufficient permission to remove tags from proxy',

    // Tag management
    'tag_created' => 'Tag created successfully',
    'tag_updated' => 'Tag updated successfully',
    'tag_deleted' => 'Tag deleted successfully',
    'tag_not_found' => 'Tag not found',
    'tag_name_exists' => 'Tag with this name already exists',
    'tag_in_use' => 'Cannot delete tag as it is being used',
    'tag_added_success' => 'Tags added successfully',
    'tag_removed_success' => 'Tags removed successfully',
    'tag_list_empty' => 'Tag list cannot be empty',
    'tag_id_list_empty' => 'Tag ID list cannot be empty',
    'insufficient_permission_tag' => 'Insufficient permission for this tag',
    'insufficient_permission_tag_edit' => 'Insufficient permission to edit tag',
    'insufficient_permission_tag_delete' => 'Insufficient permission to delete tag',

    // File upload
    'upload_success' => 'File uploaded successfully',
    'upload_failed' => 'File upload failed',
    'delete_file_success' => 'File deleted successfully',
    'delete_file_failed' => 'File deletion failed',
    'file_empty' => 'File is empty',

    // Settings
    's3_settings_updated' => 'S3 settings updated successfully',
    's3_settings_error' => 'Error updating S3 settings',
    's3_settings_incomplete' => 'S3 API settings incomplete',

    // S3 Presigned URL
    's3_presigned_url_cached' => 'Presigned URL retrieved from cache',
    's3_presigned_url_generated' => 'New presigned URL generated',
    's3_presigned_url_invalid_type' => 'Invalid type parameter. Must be get or post.',
    's3_presigned_url_missing_params' => 'Missing required parameters: type and session_id',
    's3_presigned_url_config_not_found' => 'S3 configuration not found',
    's3_presigned_url_config_incomplete' => 'S3 configuration is incomplete. Please check your S3 settings.',
    's3_presigned_url_generation_success' => 'Presigned URL generated successfully',
    's3_presigned_url_generation_error' => 'Error generating presigned URL',
    's3_presigned_url_cache_cleared' => 'Cache cleared successfully',
    's3_presigned_url_cache_clear_error' => 'Error clearing cache',

    // System
    'database_connection_failed' => 'Database connection failed. Please check configuration in .env file',
    'setup_success' => 'Setup completed successfully',
    'setup_failed' => 'Setup failed',
    'migration_success' => 'Database migration completed successfully',
    'migration_failed' => 'Database migration failed',
    'reset_profile_status_success' => 'Profile status reset successfully',
    'update_server_success' => 'Private server updated successfully',

    // Additional keys
    'group_name_exists' => 'Group name already exists',
    'no_admin_permission_needed' => 'Admin users do not need permission settings',

    // Generic error with details
    'error_with_details' => 'An error occurred',
];
