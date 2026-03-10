<?php

namespace Nietthijmen\LaravelPosthog\Events;

use Laravel\Ai\Providers\Provider;
use Nietthijmen\LaravelPosthog\LaravelPosthog;

class LaravelPosthogEventHandler
{
    public function handleLogin(
        \Illuminate\Auth\Events\Login $event
    ): void {
        $user = $event->user;
        $request = request();


        LaravelPosthog::identify($user->getAuthIdentifier(), $user->toArray());

        if($request->session()->has('posthog_distinct_id')) {
            $distinctId = $request->session()->get('posthog_distinct_id');
            LaravelPosthog::alias($user->getAuthIdentifier(), $distinctId);
            $request->session()->forget('posthog_distinct_id');
        }
    }

    public function handleAgentPrompted(
        \Laravel\Ai\Events\AgentPrompted $event
    ): void
    {
        $response = $event->response;
        $agent = $event->prompt;
        $traceId = $event->invocationId;

        $distinctId = LaravelPosthog::getAuthIdentifier();

        $providerName = class_basename($agent->provider);
        if($agent->provider instanceof Provider) {
            $providerName = $agent->provider->name();
        }
        LaravelPosthog::capture($distinctId, '$ai_generation', [
            '$ai_trace_id' => $traceId,
            '$ai_model' => $agent->model,
            '$ai_provider' => $providerName,
            '$ai_input' => $agent->prompt,
            '$ai_input_tokens' => $response->usage->promptTokens,
            '$ai_output_choices' => $response->messages->toArray(),
            '$ai_output_tokens' => $response->usage->completionTokens,
        ]);

    }
}
