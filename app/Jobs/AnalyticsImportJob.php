<?php

namespace App\Jobs;

use App\Models\PageAnalytic;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;

class AnalyticsImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public array $chunkData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $chunk)
    {
        $this->chunkData = $chunk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $validatedInputs = $this->validateEachRowAndDiscard();
        // Store the validated inputs in the database
        PageAnalytic::query()->insert($validatedInputs);
    }

    public function validateEachRowAndDiscard(): array
    {
        $validatedInputs = [];
        $size = count($this->chunkData);

        for($i = 0; $i < $size; $i++) {
            $inputArray = [
                'user_id' => $this->chunkData[$i][0],
                'created_at' => $this->chunkData[$i][1],
                'activity' => $this->chunkData[$i][2],
                'url' => $this->chunkData[$i][3],
            ];

            $validator = Validator::make($inputArray, [
                'user_id' => ['required', 'integer'],
                'activity' => ['required', 'string'],
                'url' => ['required', 'string'],
            ]);

            if($validator->fails()) {
                continue;
            }

            $validatedInputs[] = $inputArray;
        }

        $userIDsArray = array_column($validatedInputs, 'user_id');
        $uniqueUserIDs = array_unique($userIDsArray);

        $userIDsFromDB = User::query()
            ->whereIn('id', $uniqueUserIDs)
            ->pluck('id')
            ->toArray();

        return array_filter($validatedInputs, fn($input) => in_array($input['user_id'], $userIDsFromDB));
    }
}
