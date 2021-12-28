<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $urlArr = explode('/', $url);
            $fakeUrl = 'http://localhost:3000/' . 'verify?id=' . $urlArr[7] . '&hash=' . $urlArr[8];
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $fakeUrl);
        });
        $this->GatePolicyUser();
        $this->GatePolicyRole();
        $this->GatePolicyCar();
        $this->GatePolicyPolicies();
        $this->GatePolicyNewCategory();
        $this->GatePolicyNews();
        $this->GatePolicyDeparture();
        $this->GatePolicyCommentNews();
        $this->GatePolicyCommentDeparture();
        $this->GatePolicyClient();
    }
    public function GatePolicyUser()
    {
        Gate::define('user-list', 'App\Policies\UserPolicy@viewAny');
        Gate::define('user-add', 'App\Policies\UserPolicy@create');
        Gate::define('user-edit', 'App\Policies\UserPolicy@update');
        Gate::define('user-delete', 'App\Policies\UserPolicy@delete');
        Gate::define('user-deleteChecked', 'App\Policies\UserPolicy@deleteChecked');
        Gate::define('user-viewDelete', 'App\Policies\UserPolicy@viewDelete');
        Gate::define('user-show', 'App\Policies\UserPolicy@view');
        Gate::define('user-force', 'App\Policies\UserPolicy@forceDelete');
        Gate::define('user-restore', 'App\Policies\UserPolicy@restore');
        Gate::define('user-restoreAll', 'App\Policies\UserPolicy@restoreAll');
    }
    public function GatePolicyRole()
    {
        Gate::define('role-list', 'App\Policies\RolePolicy@viewAny');
        Gate::define('role-add', 'App\Policies\RolePolicy@create');
        Gate::define('role-edit', 'App\Policies\RolePolicy@update');
        Gate::define('role-delete', 'App\Policies\RolePolicy@delete');
        Gate::define('role-show', 'App\Policies\RolePolicy@view');
        Gate::define('role-viewDelete', 'App\Policies\RolePolicy@viewDelete');
        Gate::define('role-restore', 'App\Policies\RolePolicy@restore');
        Gate::define('role-deleteChecked', 'App\Policies\RolePolicy@deleteChecked');
        Gate::define('role-restoreAll', 'App\Policies\RolePolicy@restoreAll');
    }
    public function GatePolicyCar()
    {
        Gate::define('car-list', 'App\Policies\CarPolicy@viewAny');
        Gate::define('car-show', 'App\Policies\CarPolicy@view');
        Gate::define('car-add', 'App\Policies\CarPolicy@create');
        Gate::define('car-edit', 'App\Policies\CarPolicy@update');
        Gate::define('car-delete', 'App\Policies\CarPolicy@delete');
        Gate::define('car-viewDelete', 'App\Policies\CarPolicy@viewDelete');
        Gate::define('car-deleteChecked', 'App\Policies\CarPolicy@deleteChecked');
        Gate::define('car-restore', 'App\Policies\CarPolicy@restore');
        Gate::define('car-restoreAll', 'App\Policies\CarPolicy@restoreAll');
        Gate::define('car-forceDelete', 'App\Policies\CarPolicy@forceDelete');
    }
    //Policy xuất chuyến
    public function GatePolicyDeparture()
    {
        Gate::define('departure-list', 'App\Policies\DeparturePolicy@viewAny');
        Gate::define('departure-show', 'App\Policies\DeparturePolicy@view');
        Gate::define('departure-add', 'App\Policies\DeparturePolicy@create');
        Gate::define('departure-edit', 'App\Policies\DeparturePolicy@update');
        Gate::define('departure-delete', 'App\Policies\DeparturePolicy@delete');
        Gate::define('departure-viewDelete', 'App\Policies\DeparturePolicy@viewDelete');
        Gate::define('departure-deleteChecked', 'App\Policies\DeparturePolicy@deleteChecked');
        Gate::define('departure-restore', 'App\Policies\DeparturePolicy@restore');
        Gate::define('departure-restoreAll', 'App\Policies\DeparturePolicy@restoreAll');
        Gate::define('departure-forceDelete', 'App\Policies\DeparturePolicy@forceDelete');
    }

    public function GatePolicyPolicies()
    {
        Gate::define('policies-list', 'App\Policies\PoliciesPolicy@viewAny');
        Gate::define('policies-show', 'App\Policies\PoliciesPolicy@view');
        Gate::define('policies-add', 'App\Policies\PoliciesPolicy@create');
        Gate::define('policies-edit', 'App\Policies\PoliciesPolicy@update');
        Gate::define('policies-delete', 'App\Policies\PoliciesPolicy@delete');
        Gate::define('policies-viewDelete', 'App\Policies\PoliciesPolicy@viewDelete');
        Gate::define('policies-deleteChecked', 'App\Policies\PoliciesPolicy@deleteChecked');
        Gate::define('policies-restore', 'App\Policies\PoliciesPolicy@restore');
        Gate::define('policies-restoreAll', 'App\Policies\PoliciesPolicy@restoreAll');
        Gate::define('policies-forceDelete', 'App\Policies\PoliciesPolicy@forceDelete');
    }

    public function GatePolicyNewCategory()
    {
        Gate::define('newCategory-list', 'App\Policies\NewCategoryPolicy@viewAny');
        Gate::define('newCategory-show', 'App\Policies\NewCategoryPolicy@view');
        Gate::define('newCategory-add', 'App\Policies\NewCategoryPolicy@create');
        Gate::define('newCategory-edit', 'App\Policies\NewCategoryPolicy@update');
        Gate::define('newCategory-delete', 'App\Policies\NewCategoryPolicy@delete');
        Gate::define('newCategory-viewDelete', 'App\Policies\NewCategoryPolicy@viewDelete');
        Gate::define('newCategory-deleteChecked', 'App\Policies\NewCategoryPolicy@deleteChecked');
        Gate::define('newCategory-restore', 'App\Policies\NewCategoryPolicy@restore');
        Gate::define('newCategory-restoreAll', 'App\Policies\NewCategoryPolicy@restoreAll');
        Gate::define('newCategory-forceDelete', 'App\Policies\NewCategoryPolicy@forceDelete');
    }
    public function GatePolicyNews()
    {
        Gate::define('News-list', 'App\Policies\NewPolicy@viewAny');
        Gate::define('News-add', 'App\Policies\NewPolicy@create');
        Gate::define('News-edit', 'App\Policies\NewPolicy@update');
        Gate::define('News-delete', 'App\Policies\NewPolicy@delete');
        Gate::define('News-show', 'App\Policies\NewPolicy@view');
        Gate::define('News-viewDelete', 'App\Policies\NewPolicy@viewDelete');
        Gate::define('News-deleteChecked', 'App\Policies\NewPolicy@deleteChecked');
        Gate::define('News-restore', 'App\Policies\NewPolicy@restore');
        Gate::define('News-restoreAll', 'App\Policies\NewPolicy@restoreAll');
        Gate::define('News-forceDelete', 'App\Policies\NewPolicy@forceDelete');
    }
    public function GatePolicyCommentNews()
    {
        Gate::define('comment-news-list', 'App\Policies\CommentNewsPolicy@viewAny');
        Gate::define('comment-news-edit', 'App\Policies\CommentNewsPolicy@update');
        Gate::define('comment-news-delete', 'App\Policies\CommentNewsPolicy@delete');
        Gate::define('comment-news-show', 'App\Policies\CommentNewsPolicy@view');
        Gate::define('comment-news-viewDelete', 'App\Policies\CommentNewsPolicy@viewDelete');
        Gate::define('comment-news-deleteChecked', 'App\Policies\CommentNewsPolicy@deleteChecked');
        Gate::define('comment-news-restore', 'App\Policies\CommentNewsPolicy@restore');
        Gate::define('comment-news-restoreAll', 'App\Policies\CommentNewsPolicy@restoreAll');
        Gate::define('comment-news-forceDelete', 'App\Policies\CommentNewsPolicy@forceDelete');
    }
    public function GatePolicyCommentDeparture()
    {
        Gate::define('comment-departure-list', 'App\Policies\CommentDeparturePolicy@viewAny');
        Gate::define('comment-departure-edit', 'App\Policies\CommentDeparturePolicy@update');
        Gate::define('comment-departure-delete', 'App\Policies\CommentDeparturePolicy@delete');
        Gate::define('comment-departure-show', 'App\Policies\CommentDeparturePolicy@view');
        Gate::define('comment-departure-viewDelete', 'App\Policies\CommentDeparturePolicy@viewDelete');
        Gate::define('comment-departure-deleteChecked', 'App\Policies\CommentDeparturePolicy@deleteChecked');
        Gate::define('comment-departure-restore', 'App\Policies\CommentDeparturePolicy@restore');
        Gate::define('comment-departure-restoreAll', 'App\Policies\CommentDeparturePolicy@restoreAll');
        Gate::define('comment-departure-forceDelete', 'App\Policies\CommentDeparturePolicy@forceDelete');
    }
    public function GatePolicyClient()
    {
        Gate::define('client-list', 'App\Policies\ClientPolicy@viewAny');
        Gate::define('client-edit', 'App\Policies\ClientPolicy@update');
        Gate::define('client-delete', 'App\Policies\ClientPolicy@delete');
        Gate::define('client-show', 'App\Policies\ClientPolicy@view');
        Gate::define('client-viewDelete', 'App\Policies\ClientPolicy@viewDelete');
        Gate::define('client-deleteChecked', 'App\Policies\ClientPolicy@deleteChecked');
        Gate::define('client-restore', 'App\Policies\ClientPolicy@restore');
        Gate::define('client-restoreAll', 'App\Policies\ClientPolicy@restoreAll');
        Gate::define('client-forceDelete', 'App\Policies\ClientPolicy@forceDelete');
    }
}
