<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mọi query list profile đều bắt đầu bằng `is_deleted = ?` (scopeActive/scopeIntrashed),
     * nhưng bảng profiles chỉ có index do khoá ngoại tạo ra. Các composite index dưới đây
     * lấy is_deleted làm prefix để MySQL lọc trước rồi mới lọc/sắp xếp theo cột còn lại,
     * thay vì full table scan + filesort.
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->index(['is_deleted', 'group_id'], 'profiles_deleted_group_idx');
            $table->index(['is_deleted', 'created_by'], 'profiles_deleted_creator_idx');
            $table->index(['is_deleted', 'created_at'], 'profiles_deleted_created_at_idx');
        });
    }

    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_deleted_group_idx');
            $table->dropIndex('profiles_deleted_creator_idx');
            $table->dropIndex('profiles_deleted_created_at_idx');
        });
    }
};
