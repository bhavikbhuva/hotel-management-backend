<?php

namespace App\Filament\Pages;

use App\Filament\Enums\NavigationGroup;
use App\Models\Language;
use Illuminate\Support\Arr;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action as Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Schemas\Components\Actions;
use Illuminate\Support\Facades\File;

class ManageLanguages extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public static function getNavigationLabel(): string
    {
        return __('admin.languages');
    }
    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('admin.languages');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::Settings;
    }

    protected string $view = 'filament.pages.manage-languages';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('admin.add_language'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label(__('admin.language_name'))
                                ->placeholder(__('admin.language_name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('code')
                                ->label(__('admin.language_code'))
                                ->placeholder(__('admin.language_code'))
                                ->required()
                                ->unique('languages', 'code')
                                ->regex('/^[a-z0-9-]+$/')
                                ->helperText(__('admin.only_small_english_characters_numbers_and_hyphens_allowed')),

                            Grid::make(3)->schema([
                                Toggle::make('is_rtl')->label(__('admin.rtl'))->inline(false),
                                Toggle::make('status')->label(__('admin.status'))->default(true)->inline(false),
                                Toggle::make('is_default')->label(__('admin.default'))->inline(false),
                            ])->columnSpan(1),
                        ]),

                        Grid::make(6)->schema([
                            FileUpload::make('admin_json')
                                ->label(__('admin.file_for_admin_panel'))
                                ->disk('local')
                                ->directory('temp/translations')
                                ->acceptedFileTypes(['application/json'])
                                ->maxSize(5120)
                                ->columnSpan(2)
                                ->required(),

                            FileUpload::make('app_json')
                                ->label(__('admin.file_for_app'))
                                ->disk('local')
                                ->directory('temp/translations')
                                ->acceptedFileTypes(['application/json'])
                                ->maxSize(5120)
                                ->columnSpan(2),

                            FileUpload::make('web_json')
                                ->label(__('admin.file_for_web'))
                                ->disk('local')
                                ->directory('temp/translations')
                                ->acceptedFileTypes(['application/json'])
                                ->maxSize(5120)
                                ->columnSpan(2),
                        ]),

                        Actions::make([
                            Action::make(__('admin.sampleadmin'))
                                ->label(__('admin.sample_for_admin'))
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->action(fn () => $this->downloadSample('admin')),
                            Action::make(__('admin.sampleapp'))
                                ->label(__('admin.sample_for_app'))
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->action(fn () => $this->downloadSample('app')),
                            Action::make(__('admin.sampleweb'))
                                ->label(__('admin.sample_for_web'))
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->action(fn () => $this->downloadSample('web')),
                        ])->alignRight(),
                        
                        Placeholder::make('note')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('<span class="text-red-500 text-xs">Note: Do not translate any text in the files that looks like :example. Leave placeholders (words starting with :) as they are.</span>')),
                    ])
            ])
            ->statePath('data');
    }

    public function createLanguage(): void
    {
        $data = $this->form->getState();

        try {
            $language = Language::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'is_rtl' => $data['is_rtl'] ?? false,
                'status' => $data['status'] ?? true,
                'is_default' => $data['is_default'] ?? false,
            ]);

            $this->processUploadedFile($data['admin_json'] ?? null, $language->code, 'admin.php');
            $this->processUploadedFile($data['app_json'] ?? null, $language->code, 'app.php');
            $this->processUploadedFile($data['web_json'] ?? null, $language->code, 'web.php');

            Notification::make()->title(__('admin.translations_updated_successfully'))->success()->send();
            $this->form->fill();

        } catch (\Exception $e) {
            Notification::make()->title(__('admin.validation_error'))->body($e->getMessage())->danger()->send();
        }
    }

    protected function processUploadedFile($filePath, string $langCode, string $fileName): void
    {
        $filePath = Arr::first(Arr::wrap($filePath));

        if (empty($filePath)) {
            return;
        }

        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($filePath);
        if (!file_exists($path)) {
            \Illuminate\Support\Facades\Log::error("Translation file not found at " . $path);
            throw new \Exception("Uploaded file not found.");
        }

        $content = file_get_contents($path);
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("File {$fileName} is not a valid JSON file.");
        }

        foreach ($json as $key => $value) {
            if (!is_string($key)) {
                throw new \Exception("Invalid key in {$fileName}. Keys must be strings.");
            }
            if (trim($key) === '') {
                throw new \Exception("Empty key found in {$fileName}. Keys cannot be empty.");
            }
            if (!is_string($value)) {
                throw new \Exception("Invalid value for key '{$key}' in {$fileName}. Values must be strings. No nested structures allowed.");
            }
        }

        $targetDir = base_path("resources/lang/{$langCode}");
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        // Generate the JSON file (expected by external clients & custom Loader)
        $jsonFileName = str_replace('.php', '', $fileName) . '.json';
        $jsonTargetPath = "{$targetDir}/{$jsonFileName}";
        $jsonTmpPath = "{$jsonTargetPath}.tmp";
        $jsonContent = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($jsonTmpPath, $jsonContent);
        rename($jsonTmpPath, $jsonTargetPath);

        \Illuminate\Support\Facades\Storage::disk('local')->delete($filePath);
    }

    public function downloadSample(string $type)
    {
        $path = resource_path("lang_samples/{$type}.json");
        
        $data = File::exists($path) 
            ? json_decode(File::get($path), true) 
            : ['welcome' => 'Welcome'];
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, "{$type}.json");
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Language::query()->latest())
            ->columns([
                TextColumn::make('id')->label(__('admin.id'))->sortable(),
                TextColumn::make('name')->label(__('admin.name'))->searchable(),
                TextColumn::make('code')->label(__('admin.code'))->searchable(),
                TextColumn::make('is_rtl')
                    ->label(__('admin.is_rtl'))
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                ToggleColumn::make('status')->label(__('admin.status')),
                IconColumn::make('is_default')
                    ->label(__('admin.default'))
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('')
                    ->color('warning'),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('code')->required()->unique(ignoreRecord: true)->regex('/^[a-zA-Z0-9-]+$/'),
                        Toggle::make('is_rtl')->label(__('admin.is_rtl')),
                        Toggle::make('status')->label(__('admin.status')),
                        Toggle::make('is_default')->label(__('admin.default')),
                        
                        FileUpload::make('admin_json')
                            ->label(__('admin.override_admin_translations_optional'))
                            ->disk('local')
                            ->directory('temp/translations')
                            ->acceptedFileTypes(['application/json']),

                        FileUpload::make('app_json')
                            ->label(__('admin.override_app_translations_optional'))
                            ->disk('local')
                            ->directory('temp/translations')
                            ->acceptedFileTypes(['application/json']),

                        FileUpload::make('web_json')
                            ->label(__('admin.override_web_translations_optional'))
                            ->disk('local')
                            ->directory('temp/translations')
                            ->acceptedFileTypes(['application/json']),
                    ])
                    ->action(function ($record, array $data) {
                         $record->update([
                             'name' => $data['name'],
                             'code' => $data['code'],
                             'is_rtl' => $data['is_rtl'],
                             'status' => $data['status'],
                             'is_default' => $data['is_default'],
                         ]);

                         if (!empty($data['admin_json'])) {
                             $this->processUploadedFile($data['admin_json'], $record->code, 'admin.php');
                         }
                         if (!empty($data['app_json'])) {
                             $this->processUploadedFile($data['app_json'], $record->code, 'app.php');
                         }
                         if (!empty($data['web_json'])) {
                             $this->processUploadedFile($data['web_json'], $record->code, 'web.php');
                         }
                         Notification::make()->title(__('admin.language_updated_successfully'))->success()->send();
                    }),
                
                DeleteAction::make()
                    ->disabled(fn (Language $record) => $record->is_default),

                Action::make(__('admin.setdefault'))
                    ->label(__('admin.set_default'))
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->hidden(fn (Language $record) => $record->is_default)
                    ->action(function (Language $record) {
                        $record->update(['is_default' => true]);
                        Notification::make()->title(__('admin.default_language_updated'))->success()->send();
                    }),
            ]);
    }
}
