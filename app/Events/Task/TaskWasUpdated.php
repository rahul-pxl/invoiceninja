<?php
/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2020. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\Events\Task;

use App\Models\Task;
use Illuminate\Queue\SerializesModels;

/**
 * Class TaskWasUpdated.
 */
class TaskWasUpdated
{
    use SerializesModels;

    /**
     * @var Task
     */
    public $task;

    public $company;

    /**
     * Create a new event instance.
     *
     * @param Task $task
     */
    public function __construct(Task $task, $company)
    {
        $this->task = $task;
        $this->company = $company;
    }
}