<?php

namespace Modules\User\Models\Traits;

use Catch\CatchAdmin;
use Catch\Support\Module\ModuleRepository;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Permissions\Models\Permissions;

trait UserRelations
{
    protected bool $isPermissionModuleEnabled = false;

    /**
     * init traits
     */
    public function initializeUserRelations(): void
    {
        $this->isPermissionModuleEnabled = app(ModuleRepository::class)->enabled('permissions');

        if ($this->isPermissionModuleEnabled) {
            $this->with = ['roles', 'jobs'];
        }
    }

    /**
     * roles
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany($this->getRolesModel(), 'user_has_roles', 'user_id', 'role_id');
    }

    /**
     * jobs
     *
     * @return BelongsToMany
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany($this->getJobsModel(), 'user_has_jobs', 'user_id', 'job_id');
    }


    /**
     * permissions
     */
    public function withPermissions(): self
    {
        if (! $this->isPermissionModuleEnabled) {
            return $this;
        }

        $permissionsTree = [];

        /* @var Permissions $permissionsModel */
        $permissionsModel = app($this->getPermissionsModel());
        if ($this->isSuperAdmin()) {
            $permissions = $permissionsModel->orderByDesc('sort')->get();
            $permissionsTree = $permissionsModel->getPermissionTree();
        } else {
            $permissionIds = Collection::make();
            $this->roles()->with('permissions')->get()
                ->each(function ($role) use (&$permissionIds) {
                    $permissionIds = $permissionIds->concat($role->permissions?->pluck('id'));
                });

            $permissions = $permissionsModel->whereIn('id', $permissionIds->unique())->orderByDesc('sort')->get();

            if (!empty($permissionIds->toArray())) {
                $permissionsTree = $permissionsModel->getPermissionTree($permissionIds->toArray());
            }
        }

        $this->setAttribute('permissions', $permissions->each(fn ($permission) => $permission->setAttribute('hidden', $permission->isHidden())));

        //添加菜单项
        if (empty($permissionsTree)){
            $this->setAttribute('menus', []);
        }else{
            $this->setAttribute('menus', $this->convertPermissionsToMenus($permissionsTree));
        }


        return $this;
    }

    /**
     * 将权限转为前端所需格式
     * @return array
     */
    public function convertPermissionsToMenus(array $permissions): array
    {
        $newPermissions = [];
        foreach ($permissions as $permission) {
            $newPermission = [
                'path' => (int)$permission['type'] === 1 ? '/' . $permission['route'] : $permission['route'],
                'name' => $permission['route'],
                'component' => $permission['parent_id'] === 0 ? 'LAYOUT':$permission['component'],
                'redirect' => $permission['redirect'],
                'meta' => [
                    'title' => [
                        'zh_CN'=>$permission['permission_name'],
                        'en_US'=>$permission['permission_name']
                    ],
//                    'icon' => $permission['icon'],
                    'hidden' => $permission['hidden'] === 1,
                ],
                'children' => isset($permission['children']) ? $this->convertPermissionsToMenus($permission['children']) : [],
            ];

            if($permission['icon']){
                $newPermission['meta']['icon'] = $permission['icon'];
            }


            $newPermissions[] = $newPermission;
        }

        return $newPermissions;
    }

    /**
     *
     * permission module controller.action
     *
     * @param string|null $permission
     * @return bool
     */
    public function can(string $permission = null): bool
    {
        if (! $this->isPermissionModuleEnabled) {
            return true;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $this->withPermissions();

        $actions = Collection::make();

        $this->getAttribute('permissions')->each(function ($permission) use (&$actions) {
            if ($permission->isAction()) {
                [$controller, $action] = explode('@', $permission->permission_mark);

                $actions->add(CatchAdmin::getModuleControllerNamespace($permission->module). ucfirst($controller).'Controller@'.$action);
            }
        });

        // 自定义权限判断
        if ($permission) {
            [$module, $controller, $action] = explode('@', $permission);

            $permission = CatchAdmin::getModuleControllerNamespace($module). ucfirst($controller) .'Controller@'.$action;
        }

        return $actions->contains($permission ?: Route::currentRouteAction());
    }

    /**
     * get RolesModel
     *
     * @see \Modules\Permissions\Models\Roles
     * @return string
     */
    protected function getRolesModel(): string
    {
        return '\\'.CatchAdmin::getModuleModelNamespace('permissions').'Roles';
    }


    /**
     * get JobsModel
     *
     * @see \Modules\Permissions\Models\Jobs
     * @return string
     */
    protected function getJobsModel(): string
    {
        return '\\'.CatchAdmin::getModuleModelNamespace('permissions').'Jobs';
    }

    /**
     * get PermissionsModel
     *
     * @return string
     *@see \Modules\Permissions\Models\Permissions
     */
    protected function getPermissionsModel(): string
    {
        return '\\'.CatchAdmin::getModuleModelNamespace('permissions').'Permissions';
    }
}
