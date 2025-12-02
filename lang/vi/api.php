<?php

return [
    // General messages
    'success' => 'Thành công',
    'error' => 'Có lỗi xảy ra',
    'ok' => 'OK',
    'failed' => 'Thất bại',
    'validation_failed' => 'Xác thực thất bại',

    // Authentication
    'login_success' => 'Đăng nhập thành công',
    'login_failed' => 'Đăng nhập thất bại',
    'logout_success' => 'Đăng xuất thành công',
    'register_success' => 'Đăng ký thành công',
    'email_exists' => 'Email đã tồn tại',

    // Authorization
    'insufficient_permission' => 'Không đủ quyền',
    'admin_required' => 'Cần quyền admin',
    'access_denied' => 'Từ chối truy cập',
    'not_authorized' => 'Không được phép thực hiện hành động này',
    'owner_required' => 'Chỉ chủ sở hữu mới có thể thực hiện hành động này',

    // User management
    'user_not_found' => 'Người dùng không tồn tại',
    'user_update_success' => 'Cập nhật người dùng thành công',
    'profile_update_success' => 'Cập nhật profile thành công',
    'no_need_set_admin_permission' => 'Không cần set quyền cho Admin',

    // Group management
    'group_created' => 'Tạo nhóm thành công',
    'group_updated' => 'Cập nhật nhóm thành công',
    'group_deleted' => 'Xóa nhóm thành công',
    'group_not_found' => 'Nhóm không tồn tại',
    'cannot_delete_group_with_profiles' => 'Không thể xóa nhóm có liên kết với profiles',

    // Profile management
    'profile_created' => 'Tạo profile thành công',
    'profile_updated' => 'Cập nhật profile thành công',
    'profile_deleted' => 'Xóa profile thành công',
    'profile_restored' => 'Khôi phục profile thành công',
    'profile_not_found' => 'Profile không tồn tại',
    'profile_not_found_in_trash' => 'Profile không tồn tại trong thùng rác',
    'profile_start_using_success' => 'Bắt đầu sử dụng profile thành công',
    'profile_stop_using_success' => 'Dừng sử dụng profile thành công',
    'profile_in_use_by_others' => 'Profile đang được sử dụng bởi người khác',
    'profile_not_current_user' => 'Bạn không phải người đang sử dụng profile này',
    'insufficient_permission_profile' => 'Không đủ quyền với profile này',
    'insufficient_permission_profile_edit' => 'Không đủ quyền sửa profile',
    'insufficient_permission_profile_delete' => 'Không đủ quyền xóa profile',
    'insufficient_permission_profile_restore' => 'Không đủ quyền khôi phục profile',
    'insufficient_permission_profile_status' => 'Không đủ quyền update trạng thái profile',
    'insufficient_permission_profile_tags' => 'Không đủ quyền thêm tag cho profile',
    'insufficient_permission_profile_remove_tags' => 'Không đủ quyền xóa tag khỏi profile',

    // Proxy management
    'proxy_created' => 'Tạo proxy thành công',
    'proxy_updated' => 'Cập nhật proxy thành công',
    'proxy_deleted' => 'Xóa proxy thành công',
    'proxy_not_found' => 'Proxy không tồn tại',
    'proxy_status_updated' => 'Cập nhật trạng thái proxy thành công',
    'proxy_connection_success' => 'Proxy hoạt động bình thường',
    'proxy_connection_failed' => 'Proxy không thể kết nối',
    'proxy_connection_error' => 'Không thể kết nối đến proxy',
    'insufficient_permission_proxy' => 'Không đủ quyền với proxy này',
    'insufficient_permission_proxy_edit' => 'Không đủ quyền cập nhật proxy',
    'insufficient_permission_proxy_delete' => 'Không đủ quyền xóa proxy',
    'insufficient_permission_proxy_status' => 'Không đủ quyền thay đổi trạng thái proxy',
    'insufficient_permission_proxy_test' => 'Không đủ quyền test proxy',
    'insufficient_permission_proxy_tags' => 'Không đủ quyền thêm tag cho proxy',
    'insufficient_permission_proxy_remove_tags' => 'Không đủ quyền xóa tag khỏi proxy',

    // Tag management
    'tag_created' => 'Tạo tag thành công',
    'tag_updated' => 'Cập nhật tag thành công',
    'tag_deleted' => 'Xóa tag thành công',
    'tag_not_found' => 'Tag không tồn tại',
    'tag_name_exists' => 'Tag với tên này đã tồn tại',
    'tag_in_use' => 'Không thể xóa tag này vì đang được sử dụng',
    'tag_added_success' => 'Thêm tag thành công',
    'tag_removed_success' => 'Xóa tag thành công',
    'tag_list_empty' => 'Danh sách tag không được để trống',
    'tag_id_list_empty' => 'Danh sách tag ID không được để trống',
    'insufficient_permission_tag' => 'Không đủ quyền với tag này',
    'insufficient_permission_tag_edit' => 'Không đủ quyền cập nhật tag',
    'insufficient_permission_tag_delete' => 'Không đủ quyền xóa tag',

    // File upload
    'upload_success' => 'Upload file thành công',
    'upload_failed' => 'Upload file thất bại',
    'delete_file_success' => 'Xóa file thành công',
    'delete_file_failed' => 'Xóa file thất bại',
    'file_empty' => 'File rỗng',

    // Settings
    's3_settings_updated' => 'Cập nhật S3 settings thành công',
    's3_settings_error' => 'Lỗi khi cập nhật S3 settings',
    's3_settings_incomplete' => 'Chưa cài đặt đủ thông tin S3 API',

    // S3 Presigned URL
    's3_presigned_url_cached' => 'Lấy presigned URL từ cache',
    's3_presigned_url_generated' => 'Tạo presigned URL mới',
    's3_presigned_url_invalid_type' => 'Tham số type không hợp lệ. Phải là get hoặc post.',
    's3_presigned_url_missing_params' => 'Thiếu tham số bắt buộc: type và session_id',
    's3_presigned_url_config_not_found' => 'Không tìm thấy cấu hình S3',
    's3_presigned_url_config_incomplete' => 'Cấu hình S3 chưa đầy đủ. Vui lòng kiểm tra cài đặt S3.',
    's3_presigned_url_generation_success' => 'Tạo presigned URL thành công',
    's3_presigned_url_generation_error' => 'Lỗi khi tạo presigned URL',
    's3_presigned_url_cache_cleared' => 'Xóa cache thành công',
    's3_presigned_url_cache_clear_error' => 'Lỗi khi xóa cache',

    // System
    'database_connection_failed' => 'Không kết nối được đến database. Vui lòng kiểm tra cấu hình trong file .env',
    'setup_success' => 'Thiết lập hoàn tất thành công',
    'setup_failed' => 'Thiết lập thất bại',
    'migration_success' => 'Chạy migration database thành công',
    'migration_failed' => 'Chạy migration database thất bại',
    'reset_profile_status_success' => 'Reset trạng thái profile thành công',
    'update_server_success' => 'Cập nhật private server thành công',

    // Additional keys
    'group_name_exists' => 'Tên nhóm đã tồn tại',
    'no_admin_permission_needed' => 'Người dùng admin không cần thiết lập quyền',

    // Generic error with details
    'error_with_details' => 'Có lỗi xảy ra',
];
