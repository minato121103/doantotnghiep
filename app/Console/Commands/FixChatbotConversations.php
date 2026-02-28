<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixChatbotConversations extends Command
{
    protected $signature = 'chatbot:fix-conversations {--fresh : Clear all and start fresh}';
    protected $description = 'Fix chatbot conversations - group messages that belong together';

    public function handle()
    {
        if ($this->option('fresh')) {
            if ($this->confirm('This will DELETE all chatbot history. Continue?')) {
                DB::table('chatbot_conversations')->truncate();
                $this->info('All chatbot conversations cleared.');
                return 0;
            }
            return 1;
        }

        $this->info('Fixing chatbot conversations...');
        
        // Get all unique user_id/session_id combinations
        $users = DB::table('chatbot_conversations')
            ->select('user_id', 'session_id')
            ->distinct()
            ->get();
        
        $fixed = 0;
        
        foreach ($users as $user) {
            // Get all messages for this user/session, ordered by time
            $query = DB::table('chatbot_conversations')
                ->where(function($q) use ($user) {
                    if ($user->user_id) {
                        $q->where('user_id', $user->user_id);
                    } else {
                        $q->where('session_id', $user->session_id);
                    }
                })
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($query->count() <= 1) continue;
            
            // Group messages that are within 30 minutes of each other
            $currentConvId = null;
            $lastTime = null;
            $firstQuestion = null;
            
            foreach ($query as $msg) {
                $msgTime = strtotime($msg->created_at);
                
                // Start new conversation if:
                // - First message
                // - More than 30 minutes since last message
                // - Different day
                $shouldStartNew = false;
                if (!$lastTime) {
                    $shouldStartNew = true;
                } elseif (($msgTime - $lastTime) > 1800) { // 30 minutes
                    $shouldStartNew = true;
                } elseif (date('Y-m-d', $msgTime) !== date('Y-m-d', $lastTime)) {
                    $shouldStartNew = true;
                }
                
                if ($shouldStartNew) {
                    $currentConvId = Str::uuid()->toString();
                    $firstQuestion = $msg->question;
                }
                
                // Update the message with the correct conversation_id
                DB::table('chatbot_conversations')
                    ->where('id', $msg->id)
                    ->update([
                        'conversation_id' => $currentConvId,
                        'conversation_title' => mb_strlen($firstQuestion) > 50 
                            ? mb_substr($firstQuestion, 0, 50) . '...' 
                            : $firstQuestion,
                    ]);
                
                $lastTime = $msgTime;
                $fixed++;
            }
        }
        
        $this->info("Fixed {$fixed} messages.");
        
        // Show summary
        $convCount = DB::table('chatbot_conversations')
            ->select('conversation_id')
            ->distinct()
            ->count();
        $msgCount = DB::table('chatbot_conversations')->count();
        
        $this->info("Summary: {$convCount} conversations, {$msgCount} total messages.");
        
        return 0;
    }
}
