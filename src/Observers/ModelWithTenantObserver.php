<?php

namespace HenryAvila\LaravelNovaMultitenancy\Observers;

use Exception;
use HenryAvila\LaravelNovaMultitenancy\Models\Tenant;
use HenryAvila\LaravelNovaMultitenancy\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModelWithTenantObserver
{
    public function creating(Model $model)
    {
        $modelHasUser = $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'user_id');

        if (empty($model->getAttribute('tenant_id'))) {
            $model->setAttribute('tenant_id', Tenant::current()->id);
        }

        $automaticallySetModelOwner = config('nova-multitenancy.automatically_set_owner_user_for_classes') !== false;
        if ($automaticallySetModelOwner && $modelHasUser && empty($model->getAttribute('user_id'))) {

            if (! in_array($model::class, config('nova-multitenancy.ignore_set_owner_user_for_classes'))) {
                /** @var User $authenticatedUser */
                $authenticatedUser = Auth::user();

                if ($authenticatedUser) {
                    $userId = $authenticatedUser->id;
                } else {
                    throw new Exception('The model "'.get_class($model).'" must be linked to a user. No authenticated user found');
                }

                $model->setAttribute('user_id', $userId);
            }
        }
    }
}
