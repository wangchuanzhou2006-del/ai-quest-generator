<?php

namespace App\Http\Controllers;

use App\Exceptions\QuestGenerationException;
use App\Http\Requests\GenerateQuestRequest;
use App\Models\PlayerQuest;
use App\Services\QuestGenerationService;
use Illuminate\Http\Request;

class QuestGenerationController extends Controller
{
    public function create()
    {
        return view('quests.create');
    }

    public function store(GenerateQuestRequest $request, QuestGenerationService $service)
    {
        try {
            $result = $service->generateForUser($request->user(), $request->questParameters());
        } catch (QuestGenerationException $exception) {
            return back()->withInput()->withErrors([
                'generation' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('quests.show', $result['quest'])
            ->with('status', '任务已生成。');
    }

    public function show(Request $request, PlayerQuest $quest)
    {
        abort_unless((int) $quest->user_id === (int) $request->user()->id, 403);

        $quest->load('aiGeneration');

        return view('quests.show', compact('quest'));
    }
}
