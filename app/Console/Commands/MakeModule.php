<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Menu;
use App\Utils\Caseify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class MakeModule extends Command
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

    private array $case;

    /**
     * Execute the console command.
     */
    public function handle(): void
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
        $parentMenuData = [];
        if ($parentManuOptions === 2) {
            $parentMenuData = $this->newMenu();
        } elseif ($parentManuOptions === 3) {
            $parentId = select(
                label: 'Select the parent menu',
                options: Menu::query()->whereNull('parent_id')->get()->pluck('label', 'id')->toArray(),
                required: true,
            );
        }

        $this->case = Caseify::handle($moduleName);

        $moduleName = $this->case['classCase'];

        $this->info("Creating module: {$moduleName}");

        Artisan::call('make:model', ['name' => $this->case['classCase'], '--all' => true]);
        $this->info('Model files created');

        $this->createRoutes();
        $this->info('Routes files created');

        $this->createNotifications();
        $this->info('Notification files created');

        sleep(2);
        $this->createModuleMigration($moduleName, $menuLabel, $menuIcon, $parentId, $parentMenuData);
        $this->info('Module migration created');

        $this->createActions();
        $this->info('Action files created');

        $this->replaceController();
        $this->info('Controller updated.');

        $this->createView();
        $this->info('View files created');

        $this->createPermission();
        $this->info('Permission files created');

        $this->info("Module: {$moduleName} created successfully");

        $this->info('Running Formatter...');
        exec('npm run format');

        $this->info('To Do:');
        $this->info('1. Update the migrations for the module table.');
        $this->info('4. Run the Migrations.');
    }

    private function newMenu(): array
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

        return [
            'label' => $label,
            'icon' => $icon,
        ];
    }

    private function createRoutes(): void
    {
        file_put_contents(
            base_path("routes/modules/{$this->case['underscoreCase']}.php"),
            $this->runReplacers(content: $this->getModuleStub('route'))
        );
    }

    private function runReplacers(string $content): string
    {
        foreach ($this->case as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    private function getModuleStub(string $stub): string
    {
        return file_get_contents(base_path('stubs/module.' . $stub . '.stub'));
    }

    private function createNotifications(): void
    {
        $notifications = ['Created', 'Updated', 'Deleted'];
        foreach ($notifications as $notification) {
            $notificationStubFile = $this->getModuleStub('notification');
            Artisan::call('make:notification', ['name' => "{$this->case['classCase']}{$notification}"]);
            file_put_contents(
                app_path("Notifications/{$this->case['classCase']}{$notification}.php"),
                str_replace('{notificationName}', "{$this->case['classCase']}{$notification}", $notificationStubFile)
            );
        }
    }

    private function createModuleMigration(
        string $moduleName,
        string $menuLabel,
        string $menuIcon,
        ?string $parentId,
        array $parentMenuData
    ): void {
        $migrationStubFile = $this->getModuleStub('migration');

        $migrationStubFile = str_replace('{moduleName}', $moduleName, $migrationStubFile);
        $migrationStubFile = str_replace('{menuLabel}', $menuLabel, $migrationStubFile);
        $migrationStubFile = str_replace('{menuIcon}', $menuIcon, $migrationStubFile);
        $migrationStubFile = str_replace('{parentId}', $parentId ?? '', $migrationStubFile);

        if (count($parentMenuData) > 0) {
            $migrationStubFile = str_replace('{haveNewParent}', 'yes', $migrationStubFile);
            $migrationStubFile = str_replace('{parentMenuLabel}', $parentMenuData['label'], $migrationStubFile);
            $migrationStubFile = str_replace('{parentMenuIcon}', $parentMenuData['icon'], $migrationStubFile);
        } else {
            $migrationStubFile = str_replace('{haveNewParent}', 'no', $migrationStubFile);
            $migrationStubFile = str_replace('{parentMenuLabel}', 'NA', $migrationStubFile);
            $migrationStubFile = str_replace('{parentMenuIcon}', 'NA', $migrationStubFile);
        }

        $timestamp = now()->format('Y_m_d_His');

        file_put_contents(database_path("migrations/{$timestamp}_create_module_{$this->case['underscoreCase']}.php"), $migrationStubFile);
    }

    private function createActions(): void
    {
        $classCase = $this->case['classCase'];

        $createActionStubFile = $this->runReplacers(content: $this->getModuleStub('create.action'));
        $createActionStubFile = str_replace('{actionName}', "Create$classCase", $createActionStubFile);

        $updateActionStubFile = $this->runReplacers(content: $this->getModuleStub('update.action'));
        $updateActionStubFile = str_replace('{actionName}', "Update$classCase", $updateActionStubFile);

        // check if the directory exists
        if (! is_dir(app_path("Actions/{$classCase}"))) {
            mkdir(app_path("Actions/{$classCase}"));
        }

        file_put_contents(app_path("Actions/{$classCase}/Create{$classCase}.php"), $createActionStubFile);
        file_put_contents(app_path("Actions/{$classCase}/Update{$classCase}.php"), $updateActionStubFile);
    }

    private function replaceController(): void
    {
        file_put_contents(
            app_path("Http/Controllers/{$this->case['classCase']}Controller.php"),
            $this->runReplacers(content: $this->getModuleStub('controller'))
        );
    }

    private function createView(): void
    {
        if (! is_dir(resource_path("js/Pages/{$this->case['classCase']}"))) {
            mkdir(resource_path("js/Pages/{$this->case['classCase']}"));
        }

        if (! is_dir(resource_path("js/Pages/{$this->case['classCase']}/Partials"))) {
            mkdir(resource_path("js/Pages/{$this->case['classCase']}/Partials"));
        }

        file_put_contents(resource_path("js/Pages/{$this->case['classCase']}/helper.js"), $this->runReplacers(content: $this->getModuleStub('view.helper')));
        file_put_contents(resource_path("js/Pages/{$this->case['classCase']}/Partials/Form.jsx"), $this->runReplacers(content: $this->getModuleStub('view.form')));
        file_put_contents(resource_path("js/Pages/{$this->case['classCase']}/Index.jsx"), $this->runReplacers(content: $this->getModuleStub('view.index')));
    }

    private function createPermission(): void
    {
        $underscoreCase = $this->case['underscoreCase'];
        $classCase = $this->case['classCase'];

        // JS Part...
        file_put_contents(
            resource_path("js/Utils/permissions/{$underscoreCase}.js"),
            $this->runReplacers(content: $this->getModuleStub('permission'))
        );

        $permissionImport = "import { {$underscoreCase} } from '@/Utils/permissions/{$underscoreCase}.js';";
        $addStatement = "    $underscoreCase,";

        $fileLines = file(resource_path('js/Utils/permissions/index.js'));

        foreach ($fileLines as $key => $line) {
            if ($line === "\n") {
                $fileLines[$key] = $permissionImport . "\n";
                array_splice($fileLines, $key + 1, 0, "\n");
            }
        }

        array_splice($fileLines, count($fileLines) - 1, 0, $addStatement . "\n");

        file_put_contents(resource_path('js/Utils/permissions/index.js'), implode('', $fileLines));

        // PHP Part...
        $permissionEnumFile = file(app_path('Enums/PermissionEnum.php'));

        $newPermission = "    case {$classCase}List = '{$underscoreCase}.list';\n";
        $newPermission .= "    case {$classCase}Create = '{$underscoreCase}.create';\n";
        $newPermission .= "    case {$classCase}View = '{$underscoreCase}.view';\n";
        $newPermission .= "    case {$classCase}Update = '{$underscoreCase}.update';\n";
        $newPermission .= "    case {$classCase}Delete = '{$underscoreCase}.delete';\n";
        $newPermission .= "\n";

        $newPermissionCan = "\n";
        $newPermissionCan .= "            self::{$classCase}List => 'can:{$underscoreCase}.list',\n";
        $newPermissionCan .= "            self::{$classCase}Create => 'can:{$underscoreCase}.create',\n";
        $newPermissionCan .= "            self::{$classCase}View => 'can:{$underscoreCase}.view',\n";
        $newPermissionCan .= "            self::{$classCase}Update => 'can:{$underscoreCase}.update',\n";
        $newPermissionCan .= "            self::{$classCase}Delete => 'can:{$underscoreCase}.delete',\n";

        foreach ($permissionEnumFile as $key => $line) {
            if (str_starts_with($line, '{')) {
                array_splice($permissionEnumFile, $key + 1, 0, $newPermission);
            }

            if (str_ends_with(mb_trim($line), '};')) {
                array_splice($permissionEnumFile, $key + 1, 0, $newPermissionCan);
            }
        }

        file_put_contents(app_path('Enums/PermissionEnum.php'), implode('', $permissionEnumFile));
    }
}
