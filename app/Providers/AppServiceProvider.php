<?php

namespace App\Providers;

use App\Application\Booking\Contracts\BookingRepositoryInterface;
use App\Application\Booking\UseCases\CreateBookingUseCase;
use App\Application\Catalog\Contracts\ServiceRepositoryInterface;
use App\Application\Payment\UseCases\CalculatePaymentAmountUseCase;
use App\Domain\Booking\Entities\Booking;
use App\Domain\Booking\Events\BookingCreated;
use App\Domain\Chat\Entities\Conversation;
use App\Infrastructure\Cache\TenantCacheManager;
use App\Infrastructure\Persistence\EloquentBookingRepository;
use App\Infrastructure\Persistence\EloquentServiceRepository;
use App\Listeners\BookingCreatedListener;
use App\Policies\BookingPolicy;
use App\Policies\ConversationPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Support\Tenant\TenantContext;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(TenantContext::class, function () {
            return new TenantContext();
        });

        // Register repository bindings
        $this->app->bind(
            ServiceRepositoryInterface::class,
            EloquentServiceRepository::class
        );

        $this->app->bind(
            BookingRepositoryInterface::class,
            EloquentBookingRepository::class
        );

        // Register application services
        $this->app->bind(CreateBookingUseCase::class);
        $this->app->bind(CalculatePaymentAmountUseCase::class);

        // Register cache manager
        $this->app->singleton(TenantCacheManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model policies
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);

        // Register event listeners
        Event::listen(
            BookingCreated::class,
            BookingCreatedListener::class
        );
    }
}
