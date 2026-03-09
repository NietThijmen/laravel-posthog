<?php

namespace Nietthijmen\LaravelPosthog;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Contracts\DefinesFeaturesExternally;
use Laravel\Pennant\Contracts\Driver;
use Laravel\Pennant\Feature;

class PostHogPennantDriver implements Driver, DefinesFeaturesExternally
{
    public function define(string $feature, callable $resolver): void
    {}

    public function defined(): array
    {
        return [];
    }

    private function getDistinctIdForScope(
        mixed $scope
    )
    {
        if ($scope instanceof Authenticatable) {
            return $scope->getAuthIdentifier();
        }

        if ($scope instanceof Model) {
            return Feature::serializeScope($scope);
        }

        return null;
    }

    public function getAll(array $features): array
    {
        $results = collect($features)->map(function ($scopes, $feature) {
            return $this->get($feature, $scopes);
        });

        return $results->toArray();
    }



    public function get(string $feature, mixed $scope): mixed
    {
        return collect($scope)
            ->map(function ($scope) {
                if ($scope === null) {
                    return null;
                }

                return $this->getDistinctIdForScope($scope);
            })
            ->map(function ($scope) use ($feature) {
                if(!$scope) {
                    return null;
                }

                try {
                    $is_enabled = LaravelPosthog::getFeatureFlag(
                        $feature,
                        $scope
                    );
                    return $is_enabled ?? false;
                } catch (\Exception $e) {
                    return false;
                }
            });
    }

    public function set(string $feature, mixed $scope, mixed $value): void
    {
        throw new \BadMethodCallException('Not supported');
    }

    public function setForAllScopes(string $feature, mixed $value): void
    {
        throw new \BadMethodCallException('Not supported');
    }

    public function delete(string $feature, mixed $scope): void
    {
        throw new \BadMethodCallException('Not supported');
    }

    public function purge(?array $features): void
    {
        throw new \BadMethodCallException('Not supported');
    }

    public function definedFeaturesForScope(mixed $scope): array
    {
        $distinctId = $this->getDistinctIdForScope($scope);
        if (!$distinctId) {
            return [];
        }

        try {
            $allFeatures = LaravelPosthog::getAllFeatureFlags(
                $distinctId
            );

            return array_keys($allFeatures);
        } catch (\Exception $e) {
            return [];
        }
    }
}
