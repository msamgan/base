<?php

namespace App\Console\Commands;

use App\Actions\Menu\CreateMenu;
use App\Models\Menu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = text(
            label: 'What is Module"s name?',
            placeholder: 'Support',
            required: true,
            hint: 'Use the same format as used while creating a model (CamelCase)',
        );

        $menuIcon = text(
            label: 'What is the icon for the menu?',
            placeholder: 'ri-user-settings-line',
            required: true,
            hint: 'We are using Remix Icon, you can find the icon name here: https://remixicon.com/',
        );

        $menuLabel = text(
            label: 'What is the label for the menu?',
            placeholder: 'User Management',
            required: true,
        );

        $parentManuOptions = select(
            label: 'Select the parent menu',
            options: [
                1 => 'None',
                2 => 'New',
                3 => 'Existing',
            ],
            required: true,
        );

        $parentId = null;
        if ($parentManuOptions === 2) {
            $parentId = $this->newMenu()->id;
        } elseif ($parentManuOptions === 3) {
            $parentId = select(
                label: 'Select the parent menu',
                options: Menu::query()->whereNull('parent_id')->get()->pluck('label', 'id')->toArray(),
                required: true,
            );
        }

        $classCase = Str::of($moduleName)->trim()->title()->replace(' ', '')->toString();
        $classCasePlural = Str::of($moduleName)->trim()->title()->plural()->replace(' ', '')->toString();
        $underscoreCase = Str::of($moduleName)->trim()->snake()->replace(' ', '_')->toString();
        $underscoreCasePlural = Str::of($moduleName)->trim()->snake()->plural()->replace(' ', '_')->toString();

        $this->info("Creating module: {$moduleName}");

        Artisan::call('make:model', [
            'name' => $classCase,
            '--all' => true,
        ]);

        $this->createRoutes($classCase, $underscoreCase, $underscoreCasePlural);
        $this->createNotifications($classCase);

        sleep(2);
        $this->createModuleMigration(
            $moduleName,
            $menuLabel,
            $menuIcon,
            $parentId,
            $underscoreCase
        );

        $this->createActions($classCase, $underscoreCase);

        $this->replaceController($classCase, $underscoreCase);

        $this->createView(
            $classCase,
            $classCasePlural,
            $underscoreCase,
            $underscoreCasePlural
        );

        $this->info("Module: {$moduleName} created successfully");

        $this->info('To Do:');
        $this->info('1. Update the migrations for the module table.');
        $this->info('2. Update the Permissions in PermissionEnum.');
        $this->info('3. Update the Permissions in constants.js.');
        $this->info('4. Update the Routes and Services in the services.js.');
        $this->info('5. Run the Migrations.');
    }

    public function createView(
        string $classCase,
        string $classCasePlural,
        string $underscoreCase,
        string $underscoreCasePlural,
    ): void {
        $viewHelperStubFile = file_get_contents(base_path('stubs/module.view.helper.stub'));
        $viewFormStubFile = file_get_contents(base_path('stubs/module.view.form.stub'));
        $viewIndexStubFile = file_get_contents(base_path('stubs/module.view.index.stub'));

        $viewHelperStubFile = str_replace('{classCase}', $classCase, $viewHelperStubFile);
        $viewHelperStubFile = str_replace('{classCasePlural}', $classCasePlural, $viewHelperStubFile);
        $viewHelperStubFile = str_replace('{underscoreCase}', $underscoreCase, $viewHelperStubFile);
        $viewHelperStubFile = str_replace('{underscoreCasePlural}', $underscoreCasePlural, $viewHelperStubFile);

        $viewFormStubFile = str_replace('{classCase}', $classCase, $viewFormStubFile);
        $viewFormStubFile = str_replace('{classCasePlural}', $classCasePlural, $viewFormStubFile);
        $viewFormStubFile = str_replace('{underscoreCase}', $underscoreCase, $viewFormStubFile);
        $viewFormStubFile = str_replace('{underscoreCasePlural}', $underscoreCasePlural, $viewFormStubFile);

        $viewIndexStubFile = str_replace('{classCase}', $classCase, $viewIndexStubFile);
        $viewIndexStubFile = str_replace('{classCasePlural}', $classCasePlural, $viewIndexStubFile);
        $viewIndexStubFile = str_replace('{underscoreCase}', $underscoreCase, $viewIndexStubFile);
        $viewIndexStubFile = str_replace('{underscoreCasePlural}', $underscoreCasePlural, $viewIndexStubFile);

        // check if the directory exists
        if (! is_dir(resource_path("js/Pages/{$classCase}"))) {
            mkdir(resource_path("js/Pages/{$classCase}"));
        }

        if (! is_dir(resource_path("js/Pages/{$classCase}/Partials"))) {
            mkdir(resource_path("js/Pages/{$classCase}/Partials"));
        }

        file_put_contents(resource_path("js/Pages/{$classCase}/helper.js"), $viewHelperStubFile);
        file_put_contents(resource_path("js/Pages/{$classCase}/Partials/Form.jsx"), $viewFormStubFile);
        file_put_contents(resource_path("js/Pages/{$classCase}/Index.jsx"), $viewIndexStubFile);
    }

    private function newMenu(): Menu
    {
        $label = text(
            label: 'What is the label for the New Parent Menu?',
            placeholder: 'User Management',
            required: true,
        );

        $icon = text(
            label: 'What is the icon for the New Parent Menu?',
            placeholder: 'ri-user-settings-line',
            required: true,
            hint: 'We are using Remix Icon, you can find the icon name here: https://remixicon.com/',
        );

        return (new CreateMenu)->handle(
            label: $label,
            route: '#',
            icon: $icon,
            permission: null,
        );
    }

    private function createRoutes(
        string $classCase,
        string $underscoreCase,
        string $underscoreCasePlural
    ): void {
        $routeStubFile = file_get_contents(base_path('stubs/module.route.stub'));

        $routeStubFile = str_replace('{classCase}', $classCase, $routeStubFile);
        $routeStubFile = str_replace('{underscoreCase}', $underscoreCase, $routeStubFile);
        $routeStubFile = str_replace('{underscoreCasePlural}', $underscoreCasePlural, $routeStubFile);

        file_put_contents(base_path("routes/modules/{$underscoreCase}.php"), $routeStubFile);
    }

    private function createNotifications(string $classCase): void
    {
        $notifications = [
            'Created',
            'Updated',
            'Deleted',
        ];

        foreach ($notifications as $notification) {
            $notificationStubFile = file_get_contents(base_path('stubs/module.notification.stub'));

            Artisan::call('make:notification', [
                'name' => "{$classCase}{$notification}",
            ]);

            $notificationStubFile = str_replace('{notificationName}', "{$classCase}{$notification}", $notificationStubFile);
            file_put_contents(app_path("Notifications/{$classCase}{$notification}.php"), $notificationStubFile);
        }
    }

    private function createModuleMigration(
        string $moduleName,
        string $menuLabel,
        string $menuIcon,
        ?string $parentId,
        string $underscoreCase
    ): void {
        $migrationStubFile = file_get_contents(base_path('stubs/module.migration.stub'));

        $migrationStubFile = str_replace('{moduleName}', $moduleName, $migrationStubFile);
        $migrationStubFile = str_replace('{menuLabel}', $menuLabel, $migrationStubFile);
        $migrationStubFile = str_replace('{menuIcon}', $menuIcon, $migrationStubFile);
        $migrationStubFile = str_replace('{parentId}', $parentId, $migrationStubFile);

        $timestamp = now()->format('Y_m_d_His');

        file_put_contents(database_path("migrations/{$timestamp}_create_module_{$underscoreCase}.php"), $migrationStubFile);
    }

    private function createActions(string $classCase, string $underscoreCase): void
    {
        $createActionStubFile = file_get_contents(base_path('stubs/module.create.action.stub'));

        $createActionStubFile = str_replace('{classCase}', $classCase, $createActionStubFile);
        $createActionStubFile = str_replace('{actionName}', "Create$classCase", $createActionStubFile);
        $createActionStubFile = str_replace('{underscoreCase}', $underscoreCase, $createActionStubFile);

        $updateActionStubFile = file_get_contents(base_path('stubs/module.update.action.stub'));

        $updateActionStubFile = str_replace('{classCase}', $classCase, $updateActionStubFile);
        $updateActionStubFile = str_replace('{actionName}', "Update$classCase", $updateActionStubFile);
        $updateActionStubFile = str_replace('{underscoreCase}', $underscoreCase, $updateActionStubFile);

        // check if the directory exists
        if (! is_dir(app_path("Actions/{$classCase}"))) {
            mkdir(app_path("Actions/{$classCase}"));
        }

        file_put_contents(app_path("Actions/{$classCase}/Create{$classCase}.php"), $createActionStubFile);
        file_put_contents(app_path("Actions/{$classCase}/Update{$classCase}.php"), $updateActionStubFile);
    }

    private function replaceController(string $classCase, string $underscoreCase): void
    {
        $underscoreCasePlural = Str::of($underscoreCase)->plural()->toString();

        $controllerStubFile = file_get_contents(base_path('stubs/module.controller.stub'));

        $controllerStubFile = str_replace('{classCase}', $classCase, $controllerStubFile);
        $controllerStubFile = str_replace('{underscoreCase}', $underscoreCase, $controllerStubFile);
        $controllerStubFile = str_replace('{underscoreCasePlural}', $underscoreCasePlural, $controllerStubFile);

        file_put_contents(app_path("Http/Controllers/{$classCase}Controller.php"), $controllerStubFile);
    }
}
