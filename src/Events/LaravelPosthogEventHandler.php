<?php

namespace Nietthijmen\LaravelPosthog\Events;

use Nietthijmen\LaravelPosthog\LaravelPosthog;

class LaravelPosthogEventHandler
{
    public function handleLogin(
        \Illuminate\Auth\Events\Login $event
    ): void {
        $user = $event->user;
        LaravelPosthog::identify($user->getAuthIdentifier(), $user->toArray());
    }

    public function handleAgentPrompted(
        \Laravel\Ai\Events\AgentPrompted $event
    ): void
    {
        $response = $event->response;
        $agent = $event->prompt;
        $traceId = $event->invocationId;


        $distinctId = LaravelPosthog::getAuthIdentifier();
        LaravelPosthog::capture($distinctId, '$ai_generation', [
            '$ai_trace_id' => $traceId,
            '$ai_model' => $agent->model,
            '$ai_provider' => class_basename($agent->provider), // todo: change to a more user friendly name, don't forgot to make a PR to laravel/ai
            '$ai_input' => $agent->prompt,
            '$ai_input_tokens' => $response->usage->promptTokens,
            '$ai_output_choices' => $response->messages->toArray(),
            '$ai_output_tokens' => $response->usage->completionTokens,
        ]);

    }
}
