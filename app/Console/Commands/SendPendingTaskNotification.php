<?php

namespace App\Console\Commands;

use App\Models\LeadTask;
use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class SendPendingTaskNotification extends Command
{
    protected $signature = 'tasks:send-pending-today';
    protected $description = 'Send notification for tasks with today\'s due date and still pending';


    public function handle()
    {
        $now = Carbon::now();

        $tasks = LeadTask::whereDate('date', $now->toDateString())
            ->where('status', 'pending')
            ->get();

        foreach ($tasks as $task) {
            // Assuming your table has a 'time' column storing task time (HH:mm:ss format)
            $taskDateTime = Carbon::parse($task->date . ' ' . $task->time);

            // Subtract 10 minutes from task's datetime
            $taskReminderTime = $taskDateTime->copy()->subMinutes(11);

            // Check if it's time to send notification
            if ($now->greaterThanOrEqualTo($taskReminderTime) && $now->lessThan($taskDateTime)) {
                SendPushNotification(
                    $task->assigned_to,
                    '⏰ Reminder: The task "' . $task->description . '" for ' . $task->lead->company_name . ' is due today.'
                );
            }
        }

        $this->info("Pending task notifications checked for {$tasks->count()} tasks.");
    }
}
