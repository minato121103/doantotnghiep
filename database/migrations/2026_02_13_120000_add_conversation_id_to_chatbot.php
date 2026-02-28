<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->string('conversation_id', 36)->nullable()->after('session_id')->comment('Unique ID cho mỗi cuộc hội thoại');
            $table->string('conversation_title', 255)->nullable()->after('conversation_id')->comment('Tiêu đề cuộc hội thoại');
            $table->index('conversation_id');
        });

        // Update existing records with unique conversation_id based on date grouping
        $records = DB::table('chatbot_conversations')
            ->whereNull('conversation_id')
            ->orderBy('id')
            ->get();

        $lastDate = null;
        $lastUserId = null;
        $lastSessionId = null;
        $convId = null;

        foreach ($records as $record) {
            $recordDate = date('Y-m-d', strtotime($record->created_at));
            
            // New conversation if different day or different user/session
            if ($recordDate !== $lastDate || $record->user_id !== $lastUserId || $record->session_id !== $lastSessionId) {
                $convId = \Illuminate\Support\Str::uuid()->toString();
                $lastDate = $recordDate;
                $lastUserId = $record->user_id;
                $lastSessionId = $record->session_id;
            }

            DB::table('chatbot_conversations')
                ->where('id', $record->id)
                ->update(['conversation_id' => $convId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropIndex(['conversation_id']);
            $table->dropColumn('conversation_id');
            $table->dropColumn('conversation_title');
        });
    }
};
